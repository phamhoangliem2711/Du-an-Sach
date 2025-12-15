<?php
// backend/api.php - API HOÀN CHỈNH CHO INFINITYFREE
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// ========== CẤU HÌNH DATABASE INFINITYFREE ==========
// THAY THẾ BẰNG THÔNG TIN CỦA BẠN
$host = "sql310.infinityfree.com"; // Host từ MySQL Databases
$username = "if0_40677219";        // Database username
$password = "Hoangliem27112004";   // Database password
$dbname = "if0_40677219_sach_db";  // Database name

// Kết nối database 
$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'message' => $conn->connect_error
    ]);
    exit;
}

// Xử lý CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== XỬ LÝ ACTION ==========
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch($action) {
    case 'list':
    case 'getBooks':
        getBooks($conn);
        break;
        
    case 'add':
    case 'addBook':
        addBook($conn);
        break;
        
    default:
        // API info page
        echo json_encode([
            'api_name' => 'Book Store API - InfinityFree',
            'status' => 'running',
            'database' => 'Connected: ' . ($conn->ping() ? 'Yes' : 'No'),
            'endpoints' => [
                'GET  ?action=list' => 'Get all books',
                'POST ?action=add' => 'Add new book (JSON)',
                'POST ?action=add&title=...' => 'Add new book (Form Data)'
            ],
            'note' => 'Use HTTP (not HTTPS) due to SSL certificate issue'
        ]);
}

// ========== HÀM GET BOOKS ==========
function getBooks($conn) {
    $sql = "SELECT * FROM sach ORDER BY id DESC";
    $result = $conn->query($sql);
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'error' => 'Query failed',
            'message' => $conn->error
        ]);
        return;
    }
    
    $books = [];
    while($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($books),
        'data' => $books
    ]);
}

// ========== HÀM ADD BOOK ==========
function addBook($conn) {
    // Nhận dữ liệu từ frontend (cả JSON và Form Data)
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Nếu không phải JSON, dùng $_POST
    if (!$input) {
        $input = $_POST;
    }
    
    // Lấy dữ liệu
    $title = $conn->real_escape_string($input['title'] ?? '');
    $author = $conn->real_escape_string($input['author'] ?? '');
    $price = floatval($input['price'] ?? 0);
    $quantity = intval($input['quantity'] ?? $input['stock'] ?? 0);
    
    // Validate
    if (empty($title) || empty($author)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tiêu đề và tác giả không được trống'
        ]);
        return;
    }
    
    // Insert vào database
    $sql = "INSERT INTO sach (title, author, price, quantity) 
            VALUES ('$title', '$author', $price, $quantity)";
    
    if ($conn->query($sql)) {
        $newId = $conn->insert_id;
        
        // Lấy book vừa thêm
        $newBook = [
            'id' => $newId,
            'title' => $title,
            'author' => $author,
            'price' => $price,
            'quantity' => $quantity
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Thêm sách thành công!',
            'book_id' => $newId,
            'data' => $newBook
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi database: ' . $conn->error
        ]);
    }
}

$conn->close();
?>