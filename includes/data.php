<?php

// This is data.php, which is used to fetch data for charts in the dashboard
// Start or resume session
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

    // Establish database connection through db.php
    require_once(__DIR__ . '/db.php');
    
    // Check if connection exists and is valid
    if (!isset($conn) || $conn->connect_error) {
        file_put_contents(__DIR__ . '/debug.txt', "DB connection failed\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    
    // Set up type and data variables for switch cases
    $type = $_GET['type'];
    $data = [];

    // SQL Queries based on the type parameter needed in charts.js
    switch ($type) {
            
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
            ORDER BY p.PersonID ASC";
            
            $result = $conn->query($sql);

            // Check if the query was successful
            if (!$result) {
                // Log the error to the debug file
                file_put_contents(__DIR__ . '/debug.txt', "SQL Error: " . $conn->error . "\n", FILE_APPEND);
            } else {
                // If the query was successful, fetch the data
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        case 'revenue':

            $sql = "
            SELECT 
                YEAR(o.OrderDate) AS year,
                MONTH(o.OrderDate) AS month,
                DATE_FORMAT(o.OrderDate, '%Y-%m') AS period,
                SUM(od.Quantity * od.UnitPrice) AS total_revenue,
                COUNT(DISTINCT o.InvoiceNumber) AS order_count
            FROM Orders o
            JOIN OrderDetails od ON o.InvoiceNumber = od.InvoiceNumber
            WHERE o.Status != 'Cancelled'
            GROUP BY year, month
            ORDER BY year, month
            LIMIT 12";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;
        
        case 'inventory':
            $sql = "
            SELECT 
                ProductID,
                ProductName AS name,
                StockQuantity AS quantity
            FROM Products
            ORDER BY quantity ASC";
        
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
                    SUM(details.Quantity) AS total_sold,
                    SUM(details.Quantity * details.UnitPrice) AS total_revenue
                FROM OrderDetails details
                JOIN Products p ON details.ProductID = p.ProductID
                JOIN Orders o ON details.InvoiceNumber = o.InvoiceNumber
                WHERE o.Status != 'Cancelled'
                GROUP BY p.ProductID, p.ProductName
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

        case 'bottom_products':
            $sql = "
                SELECT 
                    p.ProductName AS product_name, 
                    SUM(details.Quantity) AS total_sold,
                    SUM(details.Quantity * details.UnitPrice) AS total_revenue
                FROM OrderDetails details
                JOIN Products p ON details.ProductID = p.ProductID
                JOIN Orders o ON details.InvoiceNumber = o.InvoiceNumber
                WHERE o.Status != 'Cancelled'
                GROUP BY p.ProductID, p.ProductName
                ORDER BY total_sold ASC
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
                GROUP BY c.CustomerType
                ORDER BY count DESC";
            
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
                GROUP BY PaymentStatus
                ORDER BY FIELD(PaymentStatus, 'Paid', 'Pending', 'Overdue')";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }

            break;

        case 'list-complaints':
            // Fetch every complaint you inserted via whine.php
            $sql = "
            SELECT
                ComplaintID    AS id,
                CustomerID     AS customer_id,
                ComplaintText  AS text,
                Status         AS status
            FROM Complaints
            ORDER BY ComplaintID DESC
            ";
            $result = $conn->query($sql);
            if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
            }
            break;

        case 'salesperson_summary':
            $sql = "
                SELECT 
                    CONCAT(p.FirstName, ' ', p.LastName) AS salesperson_name,
                    COUNT(DISTINCT o.InvoiceNumber) AS total_orders,
                    SUM(od.Quantity * od.UnitPrice) AS total_revenue
                FROM Orders o
                JOIN OrderDetails od ON o.InvoiceNumber = od.InvoiceNumber
                JOIN Pickups pk ON o.InvoiceNumber = pk.OrderID
                JOIN Employees e ON pk.ScheduledByEmployeeID = e.PersonID
                JOIN People p ON e.PersonID = p.PersonID
                WHERE o.Status != 'Cancelled'
                AND e.Role = 'Sales'
                GROUP BY e.PersonID, p.FirstName, p.LastName
                ORDER BY total_revenue DESC";
            
            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;
            
        case 'sales_by_category':
            $sql = "
                SELECT 
                    p.Category AS category,
                    SUM(od.Quantity * od.UnitPrice) AS total_sales
                FROM OrderDetails od
                JOIN Products p ON od.ProductID = p.ProductID
                JOIN Orders o ON od.InvoiceNumber = o.InvoiceNumber
                WHERE o.Status != 'Cancelled'
                GROUP BY p.Category
                ORDER BY total_sales DESC";
            
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        case 'canceled_revenue':
            $sql = "
            SELECT 
                DATE_FORMAT(o.OrderDate, '%Y-%m') AS period,
                SUM(od.Quantity * od.UnitPrice) AS lost_revenue
            FROM OrderDetails od
            JOIN Orders o ON od.InvoiceNumber = o.InvoiceNumber
            WHERE o.Status = 'Cancelled'
            GROUP BY period
            ORDER BY period";
                
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            break;

        case 'find-customer':
                // sanitize input
                $query = isset($_GET['query'])
                    ? $conn->real_escape_string(trim($_GET['query']))
                    : '';
                if ($query === '') {
                    echo json_encode(['error'=>'No query provided']);
                    exit;
                }
        
                // lookup by first or last name
                $sql = "
                    SELECT 
                    c.CustomerID AS id,
                    CONCAT(p.FirstName,' ',p.LastName) AS name,
                    p.PersonID
                    FROM Customers c
                    JOIN People    p ON c.PersonID=p.PersonID
                    WHERE p.FirstName LIKE '%$query%'
                        OR p.LastName  LIKE '%$query%'
                    LIMIT 1
                ";
                $result = $conn->query($sql);
                if (!$result) {
                    echo json_encode(['error'=>'DB error: '.$conn->error]);
                    exit;
                }
                if ($result->num_rows===0) {
                    echo json_encode([]);  // no match
                    exit;
                }
        
                // fetch customer & orders
                $cust = $result->fetch_assoc();
                $orders = [];
                $pid = $cust['PersonID'];
                $oq = "
                    SELECT 
                    o.InvoiceNumber AS id,
                    SUM(od.Quantity*od.UnitPrice) AS total,
                    (o.PaymentStatus='Paid')   AS paid,
                    (cpl.ComplaintID IS NOT NULL) AS complaint
                    FROM Orders o
                    JOIN OrderDetails od ON o.InvoiceNumber=od.InvoiceNumber
                    LEFT JOIN Complaints cpl ON o.InvoiceNumber=cpl.OrderID
                    WHERE o.CustomerID IN (
                    SELECT CustomerID FROM Customers WHERE PersonID='$pid'
                    )
                    GROUP BY o.InvoiceNumber
                ";
                if ($or = $conn->query($oq)) {
                    while ($r = $or->fetch_assoc()) {
                        $orders[] = [
                            'id'        => $r['id'],
                            'total'     => (float)$r['total'],
                            'paid'      => (bool)$r['paid'],
                            'complaint' => (bool)$r['complaint']
                        ];
                    }
                    $or->free();
                }
        
                $cust['orders'] = $orders;
                echo json_encode($cust);
                exit;
            

        case 'avg_days_to_pickup':
            $sql = "
                SELECT 
                    IFNULL(ROUND(AVG(DATEDIFF(p.ScheduledDate, o.OrderDate)), 2), 0) AS avg_days
                FROM Pickups p
                JOIN Orders o ON p.OrderID = o.InvoiceNumber
                WHERE p.ScheduledDate IS NOT NULL AND o.OrderDate IS NOT NULL
            ";
            $result = $conn->query($sql);
            if ($result) {
                $data[] = $result->fetch_assoc();
                $result->free();
            } else {
                http_response_code(500);
                $data = ['error' => 'Database error: ' . $conn->error];
            }
            break;
            
        case 'current_orders':
            $sql = "
                SELECT 
                    o.InvoiceNumber AS order_id,
                    DATE(o.OrderDate) AS order_date,
                    CONCAT(p.FirstName, ' ', p.LastName) AS customer_name,
                    CONCAT(sp.FirstName, ' ', sp.LastName) AS salesperson_name,
                    DATEDIFF(CURRENT_DATE, o.OrderDate) AS days_outstanding,
                    (c.ResolveText IS NULL) AS unresolved_complaint
                FROM Orders o
                JOIN Customers cu ON o.CustomerID = cu.PersonID
                JOIN IndividualCustomers ic ON cu.PersonID = ic.PersonID
                JOIN People p ON ic.PersonID = p.PersonID
                LEFT JOIN Pickups pk ON o.InvoiceNumber = pk.OrderID
                LEFT JOIN Employees e ON pk.ScheduledByEmployeeID = e.PersonID
                LEFT JOIN People sp ON e.PersonID = sp.PersonID
                LEFT JOIN Complaints c ON o.InvoiceNumber = c.OrderID
                WHERE o.Status NOT IN ('PickedUp', 'Cancelled')
                ORDER BY days_outstanding DESC
            ";
        
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            } else {
                file_put_contents(__DIR__.'/debug.txt', "SQL ERROR (current_orders): ".$conn->error."\n", FILE_APPEND);
            }
            break;
            
            
                
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid type']);
            exit;    
    }

if (!headers_sent()) {
    header('Content-Type: application/json');
}
echo json_encode($data);
?>