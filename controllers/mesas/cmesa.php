<?php
/**
 * Controlador de Mesas (CORREGIDO)
 * 
 * CAMBIOS REALIZADOS:
 * 1. case 'cambiar_estado': Validación de estados restringida a los valores
 *    que realmente existen en el ENUM de la BD ('disponible','ocupada').
 *    Los estados 'reservada' y 'mantenimiento' solo se permiten si el ENUM
 *    de la BD fue ampliado (ver nota al final).
 * 2. Añadido Content-Type: application/json de forma consistente en TODAS
 *    las respuestas JSON del endpoint cambiar_estado.
 * 3. Mejorado manejo de errores para devolver mensajes útiles.
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

// =====================================================
// ESTADOS VÁLIDOS - Deben coincidir con el ENUM de la BD
// =====================================================
// La tabla mesas tiene: estado ENUM('disponible','ocupada') DEFAULT 'disponible'
// Si necesitas más estados, primero ejecuta:
//   ALTER TABLE mesas MODIFY estado ENUM('disponible','ocupada','reservada','mantenimiento') DEFAULT 'disponible';
// y luego descomenta los estados adicionales aquí:
const ESTADOS_MESA_VALIDOS = ['disponible', 'ocupada'];

switch ($action) {
    case 'list':
        // Listar todas las mesas con información adicional
        $mesas = $pdo->query('
            SELECT m.*, 
                   (SELECT COUNT(*) FROM pedidos p WHERE p.mesa_id = m.id AND p.estado IN ("pendiente", "en_preparacion", "listo", "entregado")) as pedidos_activos
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
            
            // FIX: Validar contra estados reales del ENUM
            if (!in_array($estado, ESTADOS_MESA_VALIDOS, true)) {
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
            
            // FIX: Validar contra estados reales del ENUM
            if (!in_array($estado, ESTADOS_MESA_VALIDOS, true)) {
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
        // =====================================================
        // FIX: Endpoint AJAX para cambiar estado de mesa
        // =====================================================
        // SEGURIDAD: Solo POST, con CSRF y validación
        header('Content-Type: application/json'); // FIX: Siempre establecer Content-Type al inicio
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
            exit;
        }
        
        // Para AJAX, verificar CSRF desde header o POST
        $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!validate_csrf($csrfToken)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token CSRF inválido']);
            exit;
        }
        
        $mesaId = (int)($_POST['mesa_id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        
        // FIX: Validar contra ESTADOS_MESA_VALIDOS (que coinciden con el ENUM de la BD)
        if (!$mesaId || !in_array($estado, ESTADOS_MESA_VALIDOS, true)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false, 
                'error' => 'Datos inválidos. Estados permitidos: ' . implode(', ', ESTADOS_MESA_VALIDOS)
            ]);
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
                echo json_encode(['ok' => false, 'error' => 'Mesa no encontrada']);
                exit;
            }
            
            // Si ya tiene el estado, no hacer nada
            if ($mesa['estado'] === $estado) {
                $pdo->rollBack();
                echo json_encode(['ok' => true, 'estado' => $estado, 'mensaje' => 'Estado sin cambios']);
                exit;
            }
            
            // FIX: Validar regla de negocio - no liberar mesa con pedidos activos
            if ($estado === 'disponible') {
                $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE mesa_id=? AND estado IN ("pendiente","en_preparacion","listo","entregado")');
                $stmtCheck->execute([$mesaId]);
                $pedidosActivos = (int)$stmtCheck->fetchColumn();
                
                if ($pedidosActivos > 0) {
                    $pdo->rollBack();
                    http_response_code(409);
                    echo json_encode([
                        'ok' => false, 
                        'error' => "No se puede liberar la mesa: tiene $pedidosActivos pedido(s) activo(s). Completa o cancela los pedidos primero."
                    ]);
                    exit;
                }
            }
            
            // Actualizar estado
            $stmt = $pdo->prepare('UPDATE mesas SET estado=?, fecha_actualizacion=NOW() WHERE id=?');
            $stmt->execute([$estado, $mesaId]);
            
            $pdo->commit();
            
            echo json_encode(['ok' => true, 'estado' => $estado, 'mensaje' => 'Estado actualizado']);
            exit;
            
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log('Error al cambiar estado de mesa: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Error interno del servidor']);
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
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE mesa_id = ? AND estado IN ("pendiente", "en_preparacion", "listo", "entregado")');
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
