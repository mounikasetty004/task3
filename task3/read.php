<?php
session_start();
include("../includes/config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$posts = $stmt->fetchAll();
?>

<h1>My Posts</h1>
<a href="create.php">Create New Post</a>

<ul>
    <?php foreach ($posts as $post): ?>
        <li>
            <h3><?= $post["title"] ?></h3>
            <p><?= $post["content"] ?></p>
            <a href="edit.php?id=<?= $post['id'] ?>">Edit</a>
            <a href="delete.php?id=<?= $post['id'] ?>">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>