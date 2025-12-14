<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION['books'])) {
    $_SESSION['books'] = [
        1 => ['id'=>1, 'title'=>'Những người khốn khổ', 'author'=>'Victor Hugo', 'price'=>120000, 'stock'=>5],
        2 => ['id'=>2, 'title'=>'Sapiens', 'author'=>'Yuval Noah Harari', 'price'=>150000, 'stock'=>10],
        3 => ['id'=>3, 'title'=>'Đắc Nhân Tâm', 'author'=>'Dale Carnegie', 'price'=>90000, 'stock'=>7],
    ];
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && $action === 'getBooks') {
    echo json_encode(array_values($_SESSION['books']));
    exit;
}

if ($method === 'POST' && $action === 'addBook') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $ids = array_keys($_SESSION['books']);
    $newId = $ids ? (max($ids) + 1) : 1;
    
    $_SESSION['books'][$newId] = [
        'id' => $newId,
        'title' => $input['title'] ?? '',
        'author' => $input['author'] ?? '',
        'price' => floatval($input['price'] ?? 0),
        'stock' => intval($input['stock'] ?? 0),
    ];
    
    echo json_encode({success: true, id: $newId});
    exit;
}

echo json_encode({error: 'Invalid request'});
?>
