
<?php
include 'db.php';
include 'session.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$conn->query("DELETE FROM posts WHERE id=$id AND user_id=" . $_SESSION['user_id']);
header("Location: index.php");
