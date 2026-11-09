<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            die('Invalid application ID');
        }
        $stmt = $pdo->prepare( "DELETE FROM applications WHERE id =:id" );
        $stmt->execute([':id' => $id]);
        header('Location: main_page.php');
        exit; 
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die('Invalid application ID');
}
$stmt = $pdo->prepare(    'SELECT * FROM applications WHERE id = :id');
$stmt->execute([':id' => $id]);
$application = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$application) {
    die('Application not found');
}
?>

<h2>Delete application</h2>

<p>
    Are you sure you want to delete
    <?= htmlspecialchars(
    (string)$application['company'],
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
    ) ?>
</p>

<form action="delete.php" method="POST">

    <input type="hidden" name="id"
           value="<?= (int)$application['id'] ?>">

    <button type="submit">Confirm delete</button>

</form>

<a href="main_page.php">Cancel</a>