

<?php
require_once __DIR__ . '/db.php';
$statuses = [
    'planned',
    'submitted',
    'interview',
    'rejected',
    'withdrawn',
    'offer'
];
if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            die('Invalid application ID');
        }


        $company = trim($_POST['company'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $date = trim($_POST['application_date'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

    if (
        $company === '' ||
        $position === '' ||
        $date === '' ||
        !in_array($status, $statuses, true)
    ) {
        die('Invalid or missing fields.');
    }

    $sql = "UPDATE applications SET company=:company, position=:position,application_date=:application_date,status=:status,notes=:notes where id=:id";
     
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':company' => $company,
        ':position' => $position,
        ':application_date' => $date,
        ':status' => $status,
        ':notes' => $notes,
        ':id' => $id
    ]);

    header('Location: main_page.php');
    exit; 
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die('Invalid id');
}
$stmt = $pdo->prepare(
    'SELECT * FROM applications WHERE id = :id'
);
$stmt->execute([':id' => $id]);
$application = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$application) {
    die('Application not found.');
}
?>

<form action="edit.php" method="POST">
    <input
    type="hidden"
    name="id"
    value="<?= (int)$application['id'] ?>"
    >
    <label>Company:</label>
    <input type="text" name="company"   value="<?= htmlspecialchars((string)$application['company'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

    <label>Position:</label>
    <input type="text" name="position" value="<?= htmlspecialchars((string)$application['position'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

    <label>Application date:</label>
    <input type="date" name="application_date" value="<?= htmlspecialchars((string)$application['application_date'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

    <label for="status">Status:</label>

    <select name="status" id="status" required>
        <?php foreach ($statuses as $option): ?>
            <option
                value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?>"
                <?= strtolower($application['status']) === $option ? 'selected' : '' ?>
            >
                <?= htmlspecialchars(ucfirst($option), ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Notes:</label>
    <textarea name="notes"><?= htmlspecialchars((string)$application['notes'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>

    <button type="submit">Save application</button>
</form>