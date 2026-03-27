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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt Library - DevGenius</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dash-body">

    <!-- ===== TOP NAV ===== -->
    <nav class="dash-nav">
        <span class="dash-nav-brand">DevGenius</span>
        <div class="dash-nav-right">
            <a href="logout.php" class="dash-nav-link">Logout</a>
            <div class="dash-avatar">👤</div>
        </div>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="dash-main">

        <!-- Page header -->
        <div class="dash-header">
            <div>
                <h1 class="dash-title">Prompt Library</h1>
                <p class="dash-subtitle">Curate and manage your collection of high-performance AI architectural blueprints.</p>
            </div>
        </div>

        <!-- Filter bar + Add button -->
        <div class="dash-toolbar">
            <form method="GET" class="dash-filters">
                <button type="submit" name="category_filter" value=""
                    class="filter-pill <?= (!$category_filter) ? 'active' : '' ?>">All</button>
                <?php foreach($cats as $c): ?>
                    <button type="submit" name="category_filter" value="<?= $c['id'] ?>"
                        class="filter-pill <?= ($category_filter == $c['id']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($c['name']) ?>
                    </button>
                <?php endforeach; ?>
            </form>
            <a href="add.php" class="btn-add">+ Add Prompt</a>
        </div>

        <!-- ===== CARD GRID ===== -->
        <?php if(count($prompts) == 0): ?>
            <div class="dash-empty">
                <p>Aucune prompt pour l'instant. <a href="add.php">Créez le premier !</a></p>
            </div>
        <?php else: ?>
        <div class="prompt-grid">
            <?php foreach($prompts as $p): ?>
            <div class="prompt-card">
                <div class="prompt-card-top">
                    <span class="category-badge"><?= htmlspecialchars($p['Categorie']) ?></span>
                    <h2 class="prompt-card-title"><?= htmlspecialchars($p['title']) ?></h2>
                    <p class="prompt-card-content"><?= htmlspecialchars(mb_substr($p['content'], 0, 120)) ?><?= mb_strlen($p['content']) > 120 ? '…' : '' ?></p>
                </div>
                <div class="prompt-card-footer">
                    <span class="prompt-meta">
                        🙍 <?= htmlspecialchars($p['Nom']) ?> <?= ($p['user_id'] == $user_id) ? '<em>(Moi)</em>' : '' ?>
                    </span>
                    <div class="prompt-actions">
                        <?php if($_SESSION['role'] == 'Admin' || $p['user_id']==$user_id ): ?>
                            <a href="edit.php?id=<?php echo $p['id'] ?>" class="action-btn edit-btn">Edit</a>
                            <a href="delete.php?id=<?php echo $p['id'] ?>" class="action-btn delete-btn"
                               onclick="return confirm('Voulez-vous vraiment supprimer cette prompts ?')">Delete</a>
                        <?php else: ?>
                            <span class="locked-badge">🔒</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Create new prompt CTA card -->
            <a href="add.php" class="prompt-card prompt-card-new">
                <span class="new-card-plus">+</span>
                <p>Create New Prompt</p>
            </a>
        </div>
        <?php endif; ?>

    </main>

</body>
</html>