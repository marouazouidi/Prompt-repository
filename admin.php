<?php
require 'db.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}


$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$total_prompts = $pdo->query("SELECT COUNT(*) FROM prompts")->fetchColumn();

$sql_top = "SELECT users.name, COUNT(prompts.id) as total_p 
            FROM users 
            JOIN prompts ON users.id = prompts.user_id 
            GROUP BY users.id 
            ORDER BY total_p DESC LIMIT 1";
$top_user = $pdo->query($sql_top)->fetch(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $cat_name = trim($_POST['cat_name']);
    if (!empty($cat_name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$cat_name]);
        header("Location: admin.php");
        exit();
    }
}

if (isset($_GET['delete_cat'])) {
    $id_to_delete = $_GET['delete_cat'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id_to_delete]);
    header("Location: admin.php?success=deleted");
    exit();
}

$all_cats = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - DevGenius</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .stats-grid { display: flex; gap: 15px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; text-align: center; }
        .card h2 { font-size: 2em; margin: 10px 0; color: #3498db; }
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #34495e; color: white; }
        .btn-del { color: #e74c3c; text-decoration: none; font-weight: bold; }
        .form-add { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <h1>🛠️ Administration - Knowledge Base</h1>
    <p>Bienvenue, <strong><?= $_SESSION['username'] ?></strong> | <a href="dashboard.php">🏠 Voir les Prompts</a>
    <a href="logout.php">🚪 Logout</a>
    </p>

    <div class="stats-grid">
        <div class="card">
            <p>👥 Utilisateurs</p>
            <h2><?= $total_users ?></h2>
        </div>
        <div class="card">
            <p>📄 Prompts Stockés</p>
            <h2><?= $total_prompts ?></h2>
        </div>
        <div class="card">
            <p>🏆 Top Contributeur</p>
            <h2><?= $top_user['name'] ?? '---' ?></h2>
            <small><?= $top_user['total_p'] ?? 0 ?> prompts postés</small>
        </div>
    </div>

    <div class="form-add">
        <h3>➕ Ajouter une Catégorie</h3>
        <form method="POST">
            <input type="text" name="cat_name" placeholder="Nom de la catégorie (ex: Python, Design...)" required style="padding: 8px; width: 250px;">
            <button type="submit" name="add_category" style="padding: 8px 15px; cursor: pointer;">Ajouter</button>
        </form>
    </div>

    <h3>📂 Liste des Catégories</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($all_cats as $cat): ?>
            <tr>
                <td>#<?= $cat['id'] ?></td>
                <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                <td>
                    <a href="admin.php?delete_cat=<?= $cat['id'] ?>" class="btn-del" onclick="return confirm('Voulez-vous vraiment supprimer cette catégorie ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>