<?php
require 'db.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$cats = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$category_filter = $_GET['category_filter'] ?? '';

if($category_filter){
    $stmt = $pdo->prepare('
        SELECT prompts.*, users.name AS Nom, categories.name AS Categorie
        FROM prompts
        INNER JOIN categories ON prompts.category_id = categories.id
        INNER JOIN users ON prompts.user_id = users.id
        WHERE category_id = :cat_id
        ORDER BY (prompts.user_id = :user_id) DESC, prompts.id DESC
    ');
    $stmt->execute(['cat_id'=>$category_filter, 'user_id'=>$user_id]);
} else {
    $stmt = $pdo->prepare('
        SELECT prompts.*, users.name AS Nom, categories.name AS Categorie
        FROM prompts
        INNER JOIN categories ON prompts.category_id = categories.id
        INNER JOIN users ON prompts.user_id = users.id
        ORDER BY (prompts.user_id = :user_id) DESC, prompts.id DESC
    ');
    $stmt->execute(['user_id'=>$user_id]);
}

$prompts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard Prompts</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div>
    <h1>📂 Repository des Prompts</h1>
    <a href="add.php" >➕ Add Prompt</a>
    <a href="logout.php" >🚪 Logout</a>

    <!-- Filter par category -->
    <form method="GET">
        <select name="category_filter">
            <option value="">📂 Tous les catégories</option>
            <?php foreach($cats as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($category_filter==$c['id']) ? 'selected' : '' ?>>
                    <?php echo($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <!-- Table des prompts -->
    <?php if(count($prompts) == 0): ?>
        <p>Aucune prompt pour l'instant</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Title</th>
                <th>Prompt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($prompts as $p): ?>
            <tr>
                <td><?php echo($p['Nom']) ?> <?php echo ($p['user_id']==$user_id) ? '(Moi)' : '' ?></td>
                <td><?php echo($p['Categorie']) ?></td>
                <td><?php echo($p['title']) ?></td>
                <td><?php echo($p['content']) ?></td>
                <td>
                    <?php if($_SESSION['role'] == 'Admin' || $p['user_id']==$user_id ): ?>
                        <a href="edit.php?id=<?php echo $p['id'] ?>" >Edit</a>
                        <a href="delete.php?id=<?php echo $p['id'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette propmts ?')">Delete</a>
                    <?php else: ?>
                        <span>🔒</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html>