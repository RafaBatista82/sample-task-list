<?php
// process.php
header('Content-Type: application/json');
require_once 'db_config.php';
require_once 'Database.php';
require_once 'DatabaseOperations.php';

try {
    $db = new DatabaseOperations();
    $action = $_POST['action'] ?? 'getData';    

    switch($action) {
        case 'getData':
            $tasks = $db->select('tasks', 'id, task', '', [], 'id', 'DESC');
            $response = [
                'success' => true,
                'data' => $tasks,
                'message' => 'Data loaded succesfull'
            ];
            break;

        case 'getTaskByID':
            $tasks = $db->select('tasks', 'task', 'id = :id', ['id' => $_POST['id']], 'id', 'DESC');
            $response = [
                'success' => true,
                'data' => $tasks,
                'message' => 'Data loaded succesfull'
            ];
            break;            
            
        case 'addTask':
            $taskData = [
                'task' => $_POST['task'] ?? ''
            ];
            $id = $db->insert('tasks', $taskData);
            $response = [
                'success' => true,
                'message' => 'Task added succesfull',
                'id' => $id
            ];
            break;

        case 'updateTask':
            $taskData = [
                'task' => $_POST['task'] ?? ''
            ];
            
            // Update existing record
            $id = $db->update(
                'tasks',
                $taskData,
                'id = ' . $_POST['id'],
                []
            );    

            $response = [
                'success' => true,
                'message' => 'Task updated succesfull',
                'id' => $id
            ];
            break;            
            
        default:
            throw new Exception('No valid action');
    }
    
    echo json_encode($response);

    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

