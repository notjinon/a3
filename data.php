<?php

// Start or resume session
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

file_put_contents(__DIR__ . '/debug.txt', "Reached data.php\n", FILE_APPEND);

try {
    // Use absolute path to ensure db.php is found
    require_once(__DIR__ . '/db.php');
    
    // Check if connection exists and is valid
    if (!isset($conn) || $conn->connect_error) {
        file_put_contents(__DIR__ . '/debug.txt', "DB connection failed\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    
    file_put_contents(__DIR__ . '/debug.txt', "DB connection successful\n", FILE_APPEND);
    
    $type = $_GET['type'] ?? '';
    $data = [];

    file_put_contents(__DIR__ . '/debug.txt', "Type: $type\n", FILE_APPEND);
    
    // Add a simple test case
    switch ($type) {
        case 'test-connection':
            $data = ['status' => 'connected', 'server' => 'Database connection successful'];
            break;
            
        case 'all-employees':
            $sql = "
            SELECT 
                e.PersonID,
                p.FirstName,
                p.LastName,
                p.Email,
                e.Role,
                e.HireDate
            FROM Employees e
            JOIN People p ON e.PersonID = p.PersonID
            ORDER BY e.Role, p.LastName, p.FirstName";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        case 'test-connection':
            $data = ['status' => 'success', 'message' => 'Database connection works'];
            break;

        case 'revenue':
            $sql = "
                SELECT 
                CONCAT(p.FirstName, ' ', p.LastName) AS salesperson, 
                SUM(od.Quantity * od.UnitPrice) AS total_revenue
                FROM OrderDetails od
                JOIN Orders o ON od.InvoiceNumber = o.InvoiceNumber
                JOIN Pickups pk ON o.InvoiceNumber = pk.OrderID
                JOIN Employees e ON pk.ScheduledByEmployeeID = e.PersonID
                JOIN People p ON e.PersonID = p.PersonID
                WHERE o.Status != 'Cancelled' AND o.PaymentStatus = 'Paid'
                GROUP BY e.PersonID
                ORDER BY total_revenue DESC";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        case 'top_products':
            $sql = "
                SELECT 
                p.ProductName AS product_name, 
                SUM(od.Quantity) AS total_sold,
                SUM(od.Quantity * od.UnitPrice) AS total_revenue
                FROM OrderDetails od
                JOIN Products p ON od.ProductID = p.ProductID
                JOIN Orders o ON od.InvoiceNumber = o.InvoiceNumber
                WHERE o.Status != 'Cancelled'
                GROUP BY p.ProductID
                ORDER BY total_sold DESC
                LIMIT 10";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        case 'order_status':
            $sql = "
                SELECT 
                Status AS status, 
                COUNT(*) AS count 
                FROM Orders 
                GROUP BY Status";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        case 'customer_types':
            $sql = "
                SELECT 
                c.CustomerType AS type,
                COUNT(*) AS count
                FROM Customers c
                GROUP BY c.CustomerType";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        case 'payment_status':
            $sql = "
                SELECT 
                PaymentStatus AS status,
                COUNT(*) AS count
                FROM Orders
                GROUP BY PaymentStatus";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid type']);
            exit;
    }
} catch (mysqli_sql_exception $e) {
    file_put_contents(__DIR__ . '/debug.txt', "SQL Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/debug.txt', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    exit;
}

header('Content-Type: application/json');
echo json_encode($data);