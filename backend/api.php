<?php
// backend/api.php - API riêng cho frontend
header("Access-Control-Allow-Origin: https://bansach1.netlify.app");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// InfinityFree database config
$host = "sql310.infinityfree.com";
$username = "if0_40677219";
$password = "Hoangliem27112004";
$dbname = "if0_40677219_sach_db";

$conn = new mysqli($host, $username, $password, $dbname);
if($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Xử lý action
$action = $_GET['action'] ?? '';

switch($action) {
    case 'list':
        getBooks($conn);
        break;
    case 'add':
        addBook($conn);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

// ========== HÀM GET BOOKS ==========
function getBooks($conn) {
    $sql = "SELECT * FROM sach ORDER BY id DESC";
    $result = $conn->query($sql);
    
    if(!$result) {
        echo json_encode(['error' => 'Query failed: ' . $conn->error]);
        return;
    }
    
    $books = [];
    while($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    echo json_encode($books);
}

// ========== HÀM ADD BOOK ==========
function addBook($conn) {
    // Nhận dữ liệu JSON từ frontend
    $input = json_decode(file_get_contents('php://input'), true);
    
    if(!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        return;
    }
    
    $title = $conn->real_escape_string($input['title'] ?? '');
    $author = $conn->real_escape_string($input['author'] ?? '');
    $price = floatval($input['price'] ?? 0);
    $quantity = intval($input['quantity'] ?? $input['stock'] ?? 0);
    
    // Kiểm tra dữ liệu
    if(empty($title) || empty($author)) {
        echo json_encode(['success' => false, 'message' => 'Tiêu đề và tác giả không được để trống']);
        return;
    }
    
    // Thực hiện INSERT
    $sql = "INSERT INTO sach (title, author, price, quantity) VALUES ('$title', '$author', $price, $quantity)";
    
    if($conn->query($sql)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Thêm sách thành công',
            'id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $conn->error]);
    }
}

$conn->close();
?>