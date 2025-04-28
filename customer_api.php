<?php
require_once('db.php'); // Assuming this has your database connection

// Set JSON response headers
header('Content-Type: application/json');

// Get the action type from request
$// new:
$action = $_REQUEST['action'] ?? '';
require_once __DIR__.'/db.php';

switch($action) {
    case 'search':
        searchCustomer();
        break;
    case 'orders':
        getCustomerOrders();
        break;
    // Add these cases to your switch statement:
    case 'update':
        updateCustomer();
        break;
    case 'get_orders':
        getDetailedOrders();
        break;
    
    case 'updateComplaint':
            // read incoming JSON
            $p = json_decode(file_get_contents('php://input'), true);
            $id  = (int)($p['id']         ?? 0);
            $res = trim($p['resolution']  ?? '');
    
            if (!$id || $res === '') {
                http_response_code(400);
                echo json_encode(['error'=>'Invalid complaint ID or empty resolution']);
                exit;
            }
    
            // perform the update
            $safe = $conn->real_escape_string($res);
            $sql  = "
              UPDATE Complaints
                 SET Resolution = '$safe',
                     Status     = 'Resolved'
               WHERE ComplaintID = $id
            ";
            if ($conn->query($sql)) {
                echo json_encode(['success'=>true]);
            } else {
                http_response_code(500);
                echo json_encode(['error'=>'DB: '.$conn->error]);
            }
            exit;
    
    
    default:
        echo json_encode(['error' => 'Invalid action']);
        exit;
}

function searchCustomer() {
    global $conn;
    
    $search_term = isset($_GET['term']) ? $_GET['term'] : '';
    $search_term = $conn->real_escape_string($search_term);
    
    $sql = "
        SELECT 
            p.*,
            CASE 
                WHEN ic.PersonID IS NOT NULL THEN 'Individual'
                WHEN cc.PersonID IS NOT NULL THEN 'Company'
            END as CustomerType,
            cc.CompanyName,
            cc.TaxID
        FROM People p
        LEFT JOIN IndividualCustomers ic ON p.PersonID = ic.PersonID
        LEFT JOIN CompanyCustomers cc ON p.PersonID = cc.PersonID
        WHERE 
            (ic.PersonID IS NOT NULL OR cc.PersonID IS NOT NULL)
            AND (
                p.PersonID LIKE ? OR
                p.FirstName LIKE ? OR
                p.LastName LIKE ? OR
                p.Email LIKE ? OR
                cc.CompanyName LIKE ? OR
                cc.TaxID LIKE ?
            )
        LIMIT 1
    ";

    $search_pattern = "%{$search_term}%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssss', 
        $search_pattern, 
        $search_pattern, 
        $search_pattern, 
        $search_pattern, 
        $search_pattern, 
        $search_pattern
    );
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        
        // Format the address
        $customer['FullAddress'] = implode(', ', [
            $customer['StreetAddress'],
            $customer['City'],
            $customer['State'],
            $customer['ZipCode']
        ]);
        
        // Format the name
        $customer['FullName'] = $customer['FirstName'] . ' ' . $customer['LastName'];
        
        echo json_encode([
            'success' => true,
            'data' => $customer
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Customer not found'
        ]);
    }
    
    $stmt->close();
}

function getCustomerOrders() {
    global $conn;
    
    $person_id = isset($_GET['person_id']) ? (int)$_GET['person_id'] : 0;
    
    // First verify this is a valid customer
    $verify_sql = "
        SELECT PersonID FROM (
            SELECT PersonID FROM IndividualCustomers
            UNION
            SELECT PersonID FROM CompanyCustomers
        ) as Customers
        WHERE PersonID = ?
    ";
    
    $stmt = $conn->prepare($verify_sql);
    $stmt->bind_param('i', $person_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid customer ID'
        ]);
        $stmt->close();
        exit;
    }
    $stmt->close();
    
    // Get orders for the last 6 months
    $orders_sql = "
        SELECT 
            DATE_FORMAT(orderdate, '%Y-%m') as month,
            COUNT(*) as order_count,
            SUM(total) as total_amount
        FROM Orders
        WHERE customer = ?
        AND orderdate >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(orderdate, '%Y-%m')
        ORDER BY month DESC
    ";
    
    $stmt = $conn->prepare($orders_sql);
    $stmt->bind_param('i', $person_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'orders' => $orders,
            'generated_at' => '2025-04-27 21:52:35', // Using your system time
            'generated_by' => 'notjinon'  // Using your login
        ]
    ]);
    
    $stmt->close();
}
?>