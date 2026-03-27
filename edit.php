<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$cats = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$id = $_GET['id'];

$sql = "SELECT * FROM prompts WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$prompts = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "UPDATE prompts
    SET title = :title, content = :content, category_id = :category_id
    WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'category_id' => $_POST['category_id'],
        'id' => $id
    ]);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Prompt - DevGenius</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="add-body">

    <!-- Top nav — reuses dash-nav -->
    <nav class="dash-nav">
        <span class="dash-nav-brand">DevGenius</span>
        <a href="logout.php" class="dash-nav-link">Logout</a>
    </nav>

    <!-- Page wrapper — reuses add-main -->
    <main class="add-main">
        <div class="add-card">

            <!-- Card header -->
            <div class="add-card-header">
                <h1 class="add-title">Modifier le Prompt</h1>
                <p class="add-subtitle">Mettez à jour les paramètres de votre prompt de développement.</p>
            </div>

            <!-- Form — PHP POST logic completely unchanged -->
            <form method="POST" class="add-form">

                <!-- Title + Category side by side -->
                <div class="add-row">
                    <div class="add-field">
                        <label class="add-label">Title</label>
                        <input type="text" name="title"
                               value="<?php echo htmlspecialchars($prompts['title']); ?>"
                               placeholder="le titre"
                               class="add-input">
                    </div>
                    <div class="add-field">
                        <label class="add-label">Category</label>
                        <select name="category_id" class="add-select">
                            <option value="">Selectionner une catégorie</option>
                            <?php foreach ($cats as $c) { ?>
                                <option value="<?php echo $c['id']; ?>"
                                    <?= ($prompts['category_id'] == $c['id']) ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Prompt content -->
                <div class="add-field add-field-full">
                    <label class="add-label">Prompt Content</label>
                    <textarea name="content" class="add-textarea"
                              placeholder="// Write your prompt logic here..."><?php echo htmlspecialchars($prompts['content']); ?></textarea>
                </div>

                <!-- Actions -->
                <div class="add-actions">
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save">Save Prompt</button>
                </div>

            </form>
        </div>

        <p class="add-security-note">🔒 ENCRYPTED STORAGE ACTIVE</p>
    </main>

    <!-- Footer -->
    <footer class="add-footer">
        <span>© 2024 DevGenius Architectural Blueprinting. All rights reserved.</span>
        <div class="add-footer-links">
            <a href="#">Terms</a>
            <a href="#">Privacy</a>
            <a href="#">Support</a>
        </div>
    </footer>

</body>
</html>