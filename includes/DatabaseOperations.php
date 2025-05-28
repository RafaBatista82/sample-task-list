<?php
// DatabaseOperations.php
class DatabaseOperations {
    private $db;

	private $tables = [
        'tasks' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'task' => 'VARCHAR(255) NOT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]
        // Add more table definitions as needed
    ];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->initializeTables();
    }

    private function initializeTables() {
        try {
            foreach ($this->tables as $tableName => $columns) {
                $this->createTableIfNotExists($tableName, $columns);
            }
        } catch (PDOException $e) {
            throw new Exception("Table initialization failed: " . $e->getMessage());
        }
    }

    private function createTableIfNotExists($tableName, $columns) {
        // First check if table exists
        $tableExists = $this->db->query(
            "SHOW TABLES LIKE '$tableName'"
        )->rowCount() > 0;

        if (!$tableExists) {
            // Build column definitions
            $columnDefinitions = [];
            foreach ($columns as $columnName => $definition) {
                $columnDefinitions[] = "$columnName $definition";
            }

            // Create table query
            $sql = "CREATE TABLE $tableName (
                " . implode(", ", $columnDefinitions) . "
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->db->exec($sql);
            
            echo "Table '$tableName' created successfully.\n";
        }
    }

    // Add index to table
    public function addIndex($tableName, $indexName, $columns, $unique = false) {
        try {
            $columnList = implode(', ', $columns);
            $indexType = $unique ? 'UNIQUE' : '';
            
            $sql = "ALTER TABLE $tableName ADD $indexType INDEX $indexName ($columnList)";
            $this->db->exec($sql);
        } catch (PDOException $e) {
            // If error is not "Index already exists", rethrow it
            if ($e->getCode() != '42000') {
                throw new Exception("Index creation failed: " . $e->getMessage());
            }
        }
    }

    // SELECT query
    public function select($table, $columns = "*", $where = "", $params = [], $orderBy = "", $order = "ASC") {
        try {
            $sql = "SELECT $columns FROM $table";
            if ($where != "") {
                $sql .= " WHERE $where";
            }

            if ($orderBy != "") {
                $sql .= " ORDER BY " . $orderBy . " " . strtoupper($order);
             }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Select failed: " . $e->getMessage());
        }
    }

    // INSERT query
    public function insert($table, $data) {
        try {
            $columns = implode(", ", array_keys($data));
            $values = implode(", :", array_keys($data));
            
            $sql = "INSERT INTO $table ($columns) VALUES (:$values)";
            $stmt = $this->db->prepare($sql);
            
            foreach($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Insert failed: " . $e->getMessage());
        }
    }

    // UPDATE query
    public function update($table, $data, $where, $whereParams = []) {
        try {
            $sets = [];
            foreach(array_keys($data) as $column) {
                $sets[] = "$column = :$column";
            }
            
            $sql = "UPDATE $table SET " . implode(", ", $sets) . " WHERE $where";
            $stmt = $this->db->prepare($sql);
            
            // Bind SET values
            foreach($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            // Bind WHERE params
            foreach($whereParams as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Update failed: " . $e->getMessage());
        }
    }
	
    // DELETE query
    public function delete($table, $where, $params = []) {
        try {
            $sql = "DELETE FROM $table WHERE $where";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            throw new Exception("Delete failed: " . $e->getMessage());
        }
    }	
}
