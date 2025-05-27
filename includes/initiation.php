<?php
require_once 'db_config.php';
require_once 'Database.php';
require_once 'DatabaseOperations.php';


try {
	
    $db = new DatabaseOperations(); 
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}

/*
try {
    $db = new DatabaseOperations();
    
    // SELECT example
    $users = $db->select(
        'users', 
        '*', 
        'age > :age', 
        ['age' => 18]
    );
    
    // INSERT example
    $insertData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'age' => 25
    ];
    $db->insert('users', $insertData);
    
    // UPDATE example
    $updateData = [
        'age' => 26,
        'email' => 'john.new@example.com'
    ];
    $db->update(
        'users', 
        $updateData, 
        'id = :id', 
        ['id' => 1]
    );

} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
*/
