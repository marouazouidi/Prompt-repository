<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$total_prompts = $pdo->query("SELECT COUNT(*) FROM prompts")->fetchColumn();

$sql_top = "SELECT users.name, COUNT(prompts.id) as total_p 
            FROM users 
            JOIN prompts ON users.id = prompts.user_id 
            GROUP BY users.id 
            LIMIT 1";
$top_user = $pdo->query($sql_top)->fetch(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $cat_name = $_POST['cat_name'];
    if (!empty($cat_name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$cat_name]);
        header("Location: admin.php");
        exit();
    }
}

if (isset($_GET['delete_cat'])) {
    $delete = $_GET['delete_cat'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$delete]);
    header("Location: admin.php?success=deleted");
    exit();
}

$all_cats = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - DevGenius</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>

<body class="admin-body">

    <nav class="admin-nav">
        <span class="admin-nav-brand">DevGenius</span>
        <div class="admin-nav-right">
            <a href="logout.php" class="admin-nav-logout">Logout</a>
        </div>
    </nav>

    <div class="admin-breadcrumb">
        <a href="dashboard.php" class="breadcrumb-link">Aller au Dashboard</a>
    </div>

    <main class="admin-main">

        <div class="admin-stats">
            <div class="stat-card">
                <p class="stat-label">Total Développeurs</p>
                <p class="stat-value"><?= number_format($total_users) ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Total Prompts</p>
                <p class="stat-value"><?= number_format($total_prompts) ?></p>
            </div>
            <div class="stat-card stat-card-top">
                <p class="stat-label">Top Contributeur</p>
                <div class="stat-top-user">
                    <div class="stat-avatar"><?= strtoupper(mb_substr($top_user['name'] ?? 'X', 0, 2)) ?></div>
                    <span class="stat-top-name"><?= htmlspecialchars($top_user['name'] ?? '---') ?></span>
                </div>
                <small class="stat-sub"><?= $top_user['total_p'] ?? 0 ?> prompts postés</small>
            </div>
        </div>

        <div class="admin-cols">

            <div class="admin-col-main">
                <div class="admin-section-card">
                    <div class="admin-section-header">
                        <h2 class="admin-section-title">Gestion des Catégories</h2>
                        <span class="admin-cat-count"><?= count($all_cats) ?> Catégories</span>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nom de la Catégorie</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_cats as $cat): ?>
                                <tr>
                                    <td class="admin-td-id">#<?= $cat['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                                    <td>
                                        <div class="admin-row-actions">
                                            <a href="admin.php?delete_cat=<?= $cat['id'] ?>"
                                                onclick="return confirm('Voulez-vous vraiment supprimer cette catégorie ?')"
                                                class="admin-action-delete">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-col-side">

                <!-- Add category form -->
                <div class="admin-section-card">
                    <h3 class="admin-form-title">Ajouter une catégorie</h3>
                    <form method="POST" class="admin-add-form">
                        <label class="admin-add-label">Nom de la Catégorie</label>
                        <input type="text" name="cat_name" placeholder="ex: Data Science" class="admin-add-input">
                        <button type="submit" name="add_category" class="admin-add-btn">+ Ajouter</button>
                    </form>
                </div>


            </div>
        </div>

    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="admin-footer">
        <div class="admin-footer-left">
            <span class="admin-footer-brand">DevGenius</span>
            <span>© 2026 DevGenius. Engineering Precision.</span>
        </div>
    </footer>

</body>

</html>