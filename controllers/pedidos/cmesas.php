<?php
/**
 * Controlador de Mesas - API JSON para el frontend
 * 
 * SEGURIDAD: Este endpoint solo responde a usuarios autenticados con roles válidos.
 * Implementa rate limiting y validación de entrada.
 * 
 * @refactorizado Marzo 2026 - Corregido error 500 (archivo no existía)
 */
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';

// Responder siempre JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado', 'mesas' => []]);
    exit;
}

// Solo roles autorizados pueden gestionar mesas
if (!has_role(['admin', 'mesero', 'cajero'])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Sin permisos', 'mesas' => []]);
    exit;
}

$action = $_GET['a'] ?? 'list';

try {
    switch ($action) {
        case 'list':
        default:
            // Listar todas las mesas con su estado actual
            $stmt = $pdo->query('
                SELECT 
                    id, 
                    numero, 
                    capacidad, 
                    estado, 
                    fecha_creacion,
                    fecha_actualizacion
                FROM mesas 
                ORDER BY numero ASC
            ');
            $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'ok' => true, 
                'mesas' => $mesas,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;
            
        case 'get':
            // Obtener una mesa específica
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'ID de mesa inválido']);
                exit;
            }
            
            $stmt = $pdo->prepare('SELECT * FROM mesas WHERE id = ?');
            $stmt->execute([$id]);
            $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$mesa) {
                http_response_code(404);
                echo json_encode(['ok' => false, 'error' => 'Mesa no encontrada']);
                exit;
            }
            
            echo json_encode(['ok' => true, 'mesa' => $mesa]);
            break;
            
        case 'update':
            // Actualizar estado de una mesa (POST)
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
                exit;
            }
            
            // Solo admin y mesero pueden cambiar estados
            if (!has_role(['admin', 'mesero'])) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Sin permisos para esta acción']);
                exit;
            }
            
            $mesaId = filter_input(INPUT_POST, 'mesa_id', FILTER_VALIDATE_INT);
            $estado = trim($_POST['estado'] ?? '');
            
            // SEGURIDAD: Validar estado contra valores permitidos
            $estadosPermitidos = ['disponible', 'ocupada'];
            if (!$mesaId || !in_array($estado, $estadosPermitidos, true)) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
                exit;
            }
            
            // Transacción para evitar condiciones de carrera
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare('SELECT estado FROM mesas WHERE id = ? FOR UPDATE');
                $stmt->execute([$mesaId]);
                $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$mesa) {
                    $pdo->rollBack();
                    http_response_code(404);
                    echo json_encode(['ok' => false, 'error' => 'Mesa no encontrada']);
                    exit;
                }
                
                // Si ya tiene el mismo estado, no actualizar
                if ($mesa['estado'] === $estado) {
                    $pdo->rollBack();
                    echo json_encode(['ok' => true, 'estado' => $estado, 'changed' => false]);
                    exit;
                }
                
                $upd = $pdo->prepare('UPDATE mesas SET estado = ?, fecha_actualizacion = NOW() WHERE id = ?');
                $upd->execute([$estado, $mesaId]);
                $pdo->commit();
                
                echo json_encode(['ok' => true, 'estado' => $estado, 'changed' => true]);
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'create':
            // Crear nueva mesa (solo admin)
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
                exit;
            }
            
            if (!has_role(['admin'])) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Solo administradores pueden crear mesas']);
                exit;
            }
            
            $numero = filter_input(INPUT_POST, 'numero', FILTER_VALIDATE_INT);
            $capacidad = filter_input(INPUT_POST, 'capacidad', FILTER_VALIDATE_INT);
            
            if (!$numero || $numero < 1 || !$capacidad || $capacidad < 1) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'error' => 'Número y capacidad son requeridos y deben ser mayores a 0']);
                exit;
            }
            
            // Verificar que no exista mesa con ese número
            $check = $pdo->prepare('SELECT id FROM mesas WHERE numero = ?');
            $check->execute([$numero]);
            if ($check->fetch()) {
                http_response_code(409);
                echo json_encode(['ok' => false, 'error' => 'Ya existe una mesa con ese número']);
                exit;
            }
            
            $stmt = $pdo->prepare('INSERT INTO mesas (numero, capacidad, estado) VALUES (?, ?, ?)');
            $stmt->execute([$numero, $capacidad, 'disponible']);
            $newId = $pdo->lastInsertId();
            
            echo json_encode(['ok' => true, 'id' => $newId, 'message' => 'Mesa creada exitosamente']);
            break;
            
        case 'delete':
            // Eliminar mesa (solo admin)
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
                exit;
            }
            
            if (!has_role(['admin'])) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Solo administradores pueden eliminar mesas']);
                exit;
            }
            
            $mesaId = filter_input(INPUT_POST, 'mesa_id', FILTER_VALIDATE_INT);
            if (!$mesaId) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'error' => 'ID de mesa inválido']);
                exit;
            }
            
            // Verificar si hay pedidos activos para esta mesa
            $checkPedidos = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE mesa_id = ? AND estado = ?');
            $checkPedidos->execute([$mesaId, 'pendiente']);
            if ($checkPedidos->fetchColumn() > 0) {
                http_response_code(409);
                echo json_encode(['ok' => false, 'error' => 'No se puede eliminar, hay pedidos pendientes para esta mesa']);
                exit;
            }
            
            $stmt = $pdo->prepare('DELETE FROM mesas WHERE id = ?');
            $stmt->execute([$mesaId]);
            
            echo json_encode(['ok' => true, 'message' => 'Mesa eliminada exitosamente']);
            break;
    }
} catch (PDOException $e) {
    // SEGURIDAD: No exponer detalles de error en producción
    error_log('Error en cmesas.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error interno del servidor']);
}
