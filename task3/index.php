<?php
session_start();
include 'db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$searchSQL = $search ? "WHERE title LIKE ? OR content LIKE ?" : '';
$sql = "SELECT * FROM posts $searchSQL ORDER BY created_at DESC LIMIT ?, ?";
$count_sql = "SELECT COUNT(*) FROM posts $searchSQL";

$stmt = $conn->prepare($sql);
$count_stmt = $conn->prepare($count_sql);

if ($search) {
    $param = "%$search%";
    $stmt->bind_param("ssii", $param, $param, $offset, $limit);
    $count_stmt->bind_param("ss", $param, $param);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_row()[0];
$total_pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4 text-center">My Blog</h1>

    <form method="GET" class="d-flex mb-4">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control me-2" placeholder="Search posts...">
        <button class="btn btn-primary">Search</button>
    </form>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="mb-4">
            <a href="create.php" class="btn btn-success">Create New Post</a>
            <a href="logout.php" class="btn btn-danger float-end">Logout</a>
        </div>
    <?php else: ?>
        <a href="login.php" class="btn btn-primary">Login</a>
        <a href="register.php" class="btn btn-secondary">Register</a>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                <p class="card-text"><?= nl2br(htmlspecialchars(substr($row['content'], 0, 200))) ?>...</p>
                <p class="text-muted"><?= date('M d, Y H:i', strtotime($row['created_at'])) ?></p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete post?')">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
