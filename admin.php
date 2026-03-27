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
    <title>Admin Panel - DevGenius</title>
</head>
<body>

    <h1>🛠️ Administration - Knowledge Base</h1>
    <p>Bienvenue, <strong><?= $_SESSION['username'] ?></strong> | <a href="dashboard.php">🏠 Voir les Prompts</a>
    <a href="logout.php">🚪 Logout</a>
    </p>

    <div >
        <div>
            <p>👥 Utilisateurs</p>
            <h2><?= $total_users ?></h2>
        </div>
        <div>
            <p>📄 Prompts Stockés</p>
            <h2><?= $total_prompts ?></h2>
        </div>
        <div>
            <p>🏆 Top Contributeur</p>
            <h2><?= $top_user['name'] ?? '---' ?></h2>
            <small><?= $top_user['total_p'] ?? 0 ?> prompts postés</small>
        </div>
    </div>

    <div>
        <h3>➕ Ajouter une Catégorie</h3>
        <form method="POST">
            <input type="text" name="cat_name" placeholder="Nom de la catégorie (ex: Python, Design...)" >
            <button type="submit" name="add_category">Ajouter</button>
            <link rel="stylesheet" href="style.css">

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
                    <a href="admin.php?delete_cat=<?= $cat['id'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette catégorie ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>