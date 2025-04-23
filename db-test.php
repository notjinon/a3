<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting database test...<br>";

try {
    // Include database connection
    include_once('db.php');
    
    echo "DB file included successfully<br>";
    
    // Check if $conn exists
    if (isset($conn)) {
        echo "Connection variable exists<br>";
        
        // Test connection
        if ($conn->ping()) {
            echo "Connection successfully pings the database server<br>";
            
            // Try a simple query
            $result = $conn->query("SELECT 1 as test");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "Query executed successfully. Result: " . $row['test'] . "<br>";
                echo "Connection is fully working!";
            } else {
                echo "Query failed: " . $conn->error;
            }
        } else {
            echo "Connection ping failed: " . $conn->error;
        }
    } else {
        echo "Connection variable does not exist after including db.php";
    }
} catch (Exception $e) {
    echo "Exception occurred: " . $e->getMessage();
}