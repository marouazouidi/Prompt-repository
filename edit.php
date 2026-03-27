<?php
require 'db.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$cats = $pdo ->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$id = $_GET['id'];

$sql = "SELECT * FROM prompts WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$prompts = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $sql = "UPDATE prompts
    SET title = :title, content = :content, category_id = :category_id
    WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title' =>$_POST['title'],
        'content' => $_POST['content'],
        'category_id' => $_POST['category_id'],
        'id' => $id
    ]);

    header("Location: dashboard.php");
    exit();
}




?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Device</title>

<link rel="stylesheet" href="style.css">

</head>
<body>

    <div>
        <h2>✏️ Modifier Prompts</h2>
        
        <form method="POST">

            <input name="title" value="<?php echo ($prompts['title']); ?>" placeholder="le titre">
            <textarea name="content" placeholder="Prompt">
            <?php echo $prompts['content']; ?>
            </textarea>
            
            <select name="category_id" >  
                <option value="">Selectionner une catégorie</option>  
            <?php foreach($cats as $c){ ?> 
            <option value="<?php echo $c['id']; ?>">
                <?php echo $c['name'] ?>
            </option>
            <?php }?>
            </select>

            <div>
                <a href="dashboard.php" >⬅ Retour</a>
                <button >Modifier</button>
            </div>
            
        
        </form>


    </div>

</body>
</html>