<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'employee_management';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Get all employees or a specific employee
        if(isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($employee);
        } else {
            $stmt = $pdo->query("SELECT * FROM employees");
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($employees);
        }
        break;
        
    case 'POST':
        // Create a new employee
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO employees (name, position, department, salary, status, email, phone, hireDate, recruitmentDate, lastGradeDate, birthDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $input['name'],
            $input['position'],
            $input['department'],
            $input['salary'],
            $input['status'],
            $input['email'],
            $input['phone'],
            $input['hireDate'],
            $input['recruitmentDate'],
            $input['lastGradeDate'],
            $input['birthDate']
        ]);
        $id = $pdo->lastInsertId();
        $input['id'] = $id;
        echo json_encode($input);
        break;
        
    case 'PUT':
        // Update an employee
        if(isset($_GET['id'])) {
            $input = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE employees SET name=?, position=?, department=?, salary=?, status=?, email=?, phone=?, hireDate=?, recruitmentDate=?, lastGradeDate=?, birthDate=? WHERE id=?");
            $stmt->execute([
                $input['name'],
                $input['position'],
                $input['department'],
                $input['salary'],
                $input['status'],
                $input['email'],
                $input['phone'],
                $input['hireDate'],
                $input['recruitmentDate'],
                $input['lastGradeDate'],
                $input['birthDate'],
                $_GET['id']
            ]);
            $input['id'] = $_GET['id'];
            echo json_encode($input);
        }
        break;
        
    case 'DELETE':
        // Delete an employee
        if(isset($_GET['id'])) {
            $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode(['success' => true]);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Method not supported']);
        break;
}
?>