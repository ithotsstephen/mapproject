<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_admin_auth();
validate_admin_session();

$img_id = intval($_GET['id'] ?? 0);
$post_id = intval($_GET['post'] ?? 0);

if ($img_id <= 0 || $post_id <= 0) {
    header('Location: edit-post.php?id=' . $post_id);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT image_path FROM post_images WHERE id = ? AND post_id = ?");
    $stmt->execute([$img_id, $post_id]);
    $img = $stmt->fetch();
    if ($img) {
        // Delete file
        if ($img['image_path'] && file_exists('../' . $img['image_path'])) {
            @unlink('../' . $img['image_path']);
        }
        // Delete DB row
        $del = $pdo->prepare("DELETE FROM post_images WHERE id = ? AND post_id = ?");
        $del->execute([$img_id, $post_id]);
        log_admin_activity('Deleted post image', "Post: $post_id, ImageID: $img_id");
    }
} catch (Exception $e) {
    // ignore and redirect
}

header('Location: edit-post.php?id=' . $post_id . '&img_deleted=1');
exit();

?>
