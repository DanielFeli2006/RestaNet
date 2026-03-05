<?php
/**
 * Controlador de Mesas
 * 
 * SEGURIDAD:
 * - Requiere autenticación
 * - Requiere rol admin/mesero para gestión
 * - Validación CSRF en operaciones de escritura
 * - Validación de datos de entrada
 * - Protección contra condiciones de carrera con bloqueo FOR UPDATE
 */
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();
require_role(['admin', 'mesero', 'cajero']);

$action = $_GET['a'] ?? 'list';

switch ($action) {
    case 'list':
        // Listar todas las mesas con información adicional
        $mesas = $pdo->query('
            SELECT m.*, 
                   (SELECT COUNT(*) FROM pedidos p WHERE p.mesa_id = m.id AND p.estado IN ("pendiente", "en_proceso")) as pedidos_activos
            FROM mesas m 
            ORDER BY m.numero
        ')->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/mesas/vmesas.php';
        break;
        
    case 'create':
        require_role(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SEGURIDAD: Validar CSRF
            if (!validate_csrf($_POST['csrf_token'] ?? null)) {
                $_SESSION['error'] = 'Token CSRF inválido.';
                header('Location: cmesa.php?a=create');
                exit;
            }
            
            // Validar datos
            $numero = (int)($_POST['numero'] ?? 0);
            $capacidad = (int)($_POST['capacidad'] ?? 0);
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $notas = trim($_POST['notas'] ?? '');
            $estado = $_POST['estado'] ?? 'disponible';
            
            if ($numero <= 0 || $capacidad <= 0) {
                $_SESSION['error'] = 'El número de mesa y la capacidad deben ser mayores a 0.';
                header('Location: cmesa.php?a=create');
                exit;
            }
            
            if (!in_array($estado, ['disponible', 'ocupada', 'reservada', 'mantenimiento'], true)) {
                $estado = 'disponible';
            }
            
            // Verificar número único
            $stmt = $pdo->prepare('SELECT id FROM mesas WHERE numero = ?');
            $stmt->execute([$numero]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Ya existe una mesa con ese número.';
                header('Location: cmesa.php?a=create');
                exit;
            }
            
            $stmt = $pdo->prepare('INSERT INTO mesas (numero, capacidad, ubicacion, notas, estado) VALUES (?,?,?,?,?)');
            $stmt->execute([$numero, $capacidad, $ubicacion, $notas, $estado]);
            
            $_SESSION['success'] = 'Mesa creada exitosamente.';
            header('Location: cmesa.php');
            exit;
        }
        
        $mesa = null;
        include __DIR__ . '/../../views/mesas/vmesa_form.php';
        break;
        
    case 'edit':
        require_role(['admin']);
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { header('Location: cmesa.php'); exit; }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SEGURIDAD: Validar CSRF
            if (!validate_csrf($_POST['csrf_token'] ?? null)) {
                $_SESSION['error'] = 'Token CSRF inválido.';
                header('Location: cmesa.php?a=edit&id=' . $id);
                exit;
            }
            
            // Validar datos
            $numero = (int)($_POST['numero'] ?? 0);
            $capacidad = (int)($_POST['capacidad'] ?? 0);
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $notas = trim($_POST['notas'] ?? '');
            $estado = $_POST['estado'] ?? 'disponible';
            
            if ($numero <= 0 || $capacidad <= 0) {
                $_SESSION['error'] = 'El número de mesa y la capacidad deben ser mayores a 0.';
                header('Location: cmesa.php?a=edit&id=' . $id);
                exit;
            }
            
            if (!in_array($estado, ['disponible', 'ocupada', 'reservada', 'mantenimiento'], true)) {
                $estado = 'disponible';
            }
            
            // Verificar número único (excluyendo la mesa actual)
            $stmt = $pdo->prepare('SELECT id FROM mesas WHERE numero = ? AND id != ?');
            $stmt->execute([$numero, $id]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Ya existe otra mesa con ese número.';
                header('Location: cmesa.php?a=edit&id=' . $id);
                exit;
            }
            
            $stmt = $pdo->prepare('UPDATE mesas SET numero=?, capacidad=?, ubicacion=?, notas=?, estado=?, fecha_actualizacion=NOW() WHERE id=?');
            $stmt->execute([$numero, $capacidad, $ubicacion, $notas, $estado, $id]);
            
            $_SESSION['success'] = 'Mesa actualizada exitosamente.';
            header('Location: cmesa.php');
            exit;
        }
        
        $stmt = $pdo->prepare('SELECT * FROM mesas WHERE id=?');
        $stmt->execute([$id]);
        $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$mesa) {
            $_SESSION['error'] = 'Mesa no encontrada.';
            header('Location: cmesa.php');
            exit;
        }
        
        include __DIR__ . '/../../views/mesas/vmesa_form.php';
        break;
        
    case 'cambiar_estado':
        // SEGURIDAD: Solo POST, con CSRF y validación
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
            exit;
        }
        
        // Para AJAX, verificar CSRF desde header o POST
        $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!validate_csrf($csrfToken)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Token CSRF inválido']);
            exit;
        }
        
        $mesaId = (int)($_POST['mesa_id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        
        if (!$mesaId || !in_array($estado, ['disponible', 'ocupada', 'reservada', 'mantenimiento'], true)) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
            exit;
        }
        
        // SEGURIDAD: Usar transacción con bloqueo para evitar condiciones de carrera
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT estado FROM mesas WHERE id=? FOR UPDATE');
            $stmt->execute([$mesaId]);
            $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$mesa) {
                $pdo->rollBack();
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'Mesa no encontrada']);
                exit;
            }
            
            // Si ya tiene el estado, no hacer nada
            if ($mesa['estado'] === $estado) {
                $pdo->rollBack();
                header('Content-Type: application/json');
                echo json_encode(['ok' => true, 'estado' => $estado, 'mensaje' => 'Estado sin cambios']);
                exit;
            }
            
            // Actualizar estado
            $stmt = $pdo->prepare('UPDATE mesas SET estado=?, fecha_actualizacion=NOW() WHERE id=?');
            $stmt->execute([$estado, $mesaId]);
            
            $pdo->commit();
            
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'estado' => $estado, 'mensaje' => 'Estado actualizado']);
            exit;
            
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log('Error al cambiar estado de mesa: ' . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Error interno']);
            exit;
        }
        
    case 'delete':
        require_role(['admin']);
        
        // Solo aceptar POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $id = (int)($_GET['id'] ?? 0);
            if ($id) {
                $stmt = $pdo->prepare('SELECT * FROM mesas WHERE id=?');
                $stmt->execute([$id]);
                $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($mesa) {
                    include __DIR__ . '/../../views/mesas/vmesa_delete.php';
                    exit;
                }
            }
            header('Location: cmesa.php');
            exit;
        }
        
        // SEGURIDAD: Validar CSRF
        if (!validate_csrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'Token CSRF inválido.';
            header('Location: cmesa.php');
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            // Verificar que no tenga pedidos activos
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE mesa_id = ? AND estado IN ("pendiente", "en_proceso")');
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error'] = 'No se puede eliminar la mesa porque tiene pedidos activos.';
                header('Location: cmesa.php');
                exit;
            }
            
            $pdo->prepare('DELETE FROM mesas WHERE id=?')->execute([$id]);
            $_SESSION['success'] = 'Mesa eliminada exitosamente.';
        }
        header('Location: cmesa.php');
        exit;
        
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
