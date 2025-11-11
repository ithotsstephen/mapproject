<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_admin_auth();
validate_admin_session();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit();
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_admin_csrf_token($csrf)) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit();
}

$post_id = intval($_POST['post_id'] ?? 0);
$images_json = $_POST['images'] ?? '';

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing post id']);
    exit();
}

// Parse images JSON expected as [{id:..., caption:..., sort_order:...}, ...]
$images = json_decode($images_json, true);
if (!is_array($images)) {
    echo json_encode(['success' => false, 'message' => 'Invalid images payload']);
    exit();
}

try {
    $pdo->beginTransaction();
    $updateStmt = $pdo->prepare("UPDATE post_images SET caption = ?, sort_order = ? WHERE id = ? AND post_id = ?");
    foreach ($images as $img) {
        $id = intval($img['id'] ?? 0);
        $caption = isset($img['caption']) ? trim($img['caption']) : null;
        $order = intval($img['sort_order'] ?? 0);
        if ($id <= 0) continue;
        $updateStmt->execute([$caption, $order, $id, $post_id]);
    }
    $pdo->commit();
    echo json_encode(['success' => true]);
    exit();
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('update-post-images error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

?>
