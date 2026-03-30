<?php
require 'db.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$cats = $pdo->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']=== 'POST'){
    $sql = "INSERT INTO prompts(title , content, category_id, user_id)
            VALUES (:title, :content, :category, :user)";
    
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'category' => $_POST['category_id'],
        'user'=> $_SESSION['user_id']
    ]);


    header('Location: dashboard.php');
    exit();
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Prompt - DevGenius</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="add-body">

    <nav class="dash-nav">
        <span class="dash-nav-brand">DevGenius</span>
        <a href="logout.php" class="dash-nav-link">Logout</a>
    </nav>

    <main class="add-main">
        <div class="add-card">

            <div class="add-card-header">
                <h1 class="add-title">Créer une nouvelle Prompt</h1>
            </div>

            <form method="POST" class="add-form">

                <!-- Title + Category side by side -->
                <div class="add-row">
                    <div class="add-field">
                        <label class="add-label">titre</label>
                        <input type="text" name="title" placeholder="ex. TypeScript Boilerplate Generator" class="add-input">
                    </div>
                    <div class="add-field">
                        <label class="add-label">Catégorie</label>
                        <select name="category_id" class="add-select">
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach($cats as $c){ ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Prompt content textarea -->
                <div class="add-field add-field-full">
                    <label class="add-label">Prompt Content</label>
                    <textarea name="content" class="add-textarea" placeholder="// Write your prompt logic here..."></textarea>
                </div>

                <!-- Actions: Cancel + Save -->
                <div class="add-actions">
                    <a href="dashboard.php" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-save">Sauvegarder Prompt</button>
                </div>

            </form>
        </div>

    </main>

    <!-- Footer -->
    <footer class="add-footer">
        <span>© 2026 DevGenius Architectural Blueprinting. All rights reserved.</span>
    </footer>

</body>
</html>