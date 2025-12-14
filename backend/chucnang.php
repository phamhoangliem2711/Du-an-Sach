<?php
// InfinityFree database config
$host = "sql310.infinityfree.com"; // Host trong CPanel
$username = "if0_40677219"; // Username database
$password = "Hoangliem27112004"; // Password database
$dbname = "if0_40677219_sach_db"; // Database name

$conn = new mysqli($host, $username, $password, $dbname);
// Xử lý API
$action = $_GET['action'] ?? '';

switch($action) {
    case 'list':
        getBooks($conn);
        break;
    case 'add':
        addBook($conn);
        break;
    // ... các action khác
}
function getBooks($conn) {
    $sql = "SELECT * FROM sach ORDER BY id DESC";
    $result = $conn->query($sql);
    $books = [];
    while($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    echo json_encode($books);
}
?>

<?php


// ======================
// DỮ LIỆU ẢO (KHÔNG CẦN DATABASE)
// Lưu trong session để tồn tại tạm thời trong một phiên duyệt
// ======================
session_start();

// THÊM NGAY SAU session_start();
header("Access-Control-Allow-Origin: https://bansach1.netlify.app");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

// ========== PHẦN API MỚI CHO FRONTEND ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'addBook') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $title = $data['title'] ?? '';
        $author = $data['author'] ?? '';
        $price = $data['price'] ?? 0;
        $stock = $data['stock'] ?? 0;
        
        // Tạo ID mới
        $ids = array_keys($_SESSION['books']);
        $newId = $ids ? (max($ids) + 1) : 1;
        
        $_SESSION['books'][$newId] = [
            'id' => $newId,
            'title' => $title,
            'author' => $author,
            'price' => $price,
            'stock' => $stock,
        ];
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $newId]);
        exit;
    }
    
    if ($action === 'getBooks') {
        header('Content-Type: application/json');
        echo json_encode(array_values($_SESSION['books']));
        exit;
    }
}

// ========== PHẦN HIỂN THỊ HTML CŨ ==========
// ... phần HTML hiện tại của bạn


if (!isset($_SESSION['books'])) {
    // Sample fake data
    $_SESSION['books'] = [
        1 => ['id' => 1, 'title' => 'Những người khốn khổ', 'author' => 'Victor Hugo', 'price' => 120000.00, 'stock' => 5],
        2 => ['id' => 2, 'title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'price' => 150000.00, 'stock' => 10],
        3 => ['id' => 3, 'title' => 'Đắc Nhân Tâm', 'author' => 'Dale Carnegie', 'price' => 90000.00, 'stock' => 7],
    ];
}


// API: trả JSON khi yêu cầu
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    $rows = array_values($_SESSION['books']);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows);
    exit;
}

// XÓA
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (isset($_SESSION['books'][$id])) unset($_SESSION['books'][$id]);
    header('Location: chucnang.php');
    exit;
}


// SỬA - LẤY DATA CẦN SỬA
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    if (isset($_SESSION['books'][$id])) $editData = $_SESSION['books'][$id];
}


// CẬP NHẬT
if (isset($_POST['update'])) {
    $id     = intval($_POST['id']);
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price  = floatval($_POST['price']);
    $stock  = intval($_POST['stock']);

    if (isset($_SESSION['books'][$id])) {
        $_SESSION['books'][$id]['title']  = $title;
        $_SESSION['books'][$id]['author'] = $author;
        $_SESSION['books'][$id]['price']  = $price;
        $_SESSION['books'][$id]['stock']  = $stock;
    }
    header('Location: chucnang.php');
    exit;
}


// THÊM MỚI
if (isset($_POST['add'])) {
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price  = floatval($_POST['price']);
    $stock  = intval($_POST['stock']);

    $ids = array_keys($_SESSION['books']);
    $newId = $ids ? (max($ids) + 1) : 1;
    $_SESSION['books'][$newId] = [
        'id' => $newId,
        'title' => $title,
        'author' => $author,
        'price' => $price,
        'stock' => $stock,
    ];

    header('Location: chucnang.php');
    exit;
}


// LẤY DANH SÁCH SÁCH (từ session)
$list = array_values($_SESSION['books']);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quản lý bán sách - Backend</title>
    <style>
        body { width: 800px; margin: 30px auto; font-family: Arial, sans-serif; }
        input, select { padding: 7px; width: 100%; margin: 5px 0; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align:left; }
        a { text-decoration: none; color: blue; }
        button { padding: 8px 15px; }
        .box { border: 1px solid #ccc; padding: 15px; margin-top: 20px; }
        .actions a { margin-right: 8px; }
    </style>
</head>
<body>

<h2>Quản lý bán sách</h2>

<div class="box">
    <h3><?php echo $editData ? "Sửa thông tin sách" : "Thêm sách mới"; ?></h3>

    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id'] ?? '') ?>">

        Tiêu đề:
        <input type="text" name="title" value="<?= htmlspecialchars($editData['title'] ?? '') ?>" required>

        Tác giả:
        <input type="text" name="author" value="<?= htmlspecialchars($editData['author'] ?? '') ?>" required>

        Giá (VNĐ):
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($editData['price'] ?? '') ?>" required>

        Số lượng:
        <input type="number" name="stock" value="<?= htmlspecialchars($editData['stock'] ?? '') ?>" required><br>

        <?php if ($editData) { ?>
            <button name="update">Cập nhật</button>
            <a href="chucnang.php">Hủy</a>
        <?php } else { ?>
            <button name="add">Thêm mới</button>
        <?php } ?>
    </form>
</div>


<h3>Danh sách sách</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Tiêu đề</th>
        <th>Tác giả</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Hành động</th>
    </tr>
    <?php foreach ($list as $row) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['author']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= $row['stock'] ?></td>
            <td class="actions">
                <a href="chucnang.php?edit=<?= $row['id'] ?>">Sửa</a>
                <a href="chucnang.php?delete=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
            </td>
        </tr>
    <?php } ?>
</table>

<p style="margin-top:20px">Frontend (tĩnh): <a href="/fontend/index.html">Mở giao diện frontend</a></p>

</body>
</html>
