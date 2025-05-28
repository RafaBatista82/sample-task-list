<?php
// process.php
header('Content-Type: application/json');
require_once 'db_config.php';
require_once 'Database.php';
require_once 'DatabaseOperations.php';

try {
    $db = new DatabaseOperations();
    
    // Ejemplo: obtener datos
    $tasks = $db->select('tasks', 'id, task', '', []);
    
    echo json_encode([
        'success' => true,
        'data' => $tasks,
        'message' => 'Datos cargados correctamente'
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

