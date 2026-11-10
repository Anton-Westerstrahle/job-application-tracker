<?php
$statuses = [
    'planned',
    'submitted',
    'interview',
    'rejected',
    'withdrawn',
    'offer'
];
require_once __DIR__ . '/db.php';
$duplicateWarning = false;
$confirmed = isset($_POST['confirm_duplicate']);
if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
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
$stmt = $pdo->prepare(
        'SELECT id, application_date
        FROM applications
        WHERE company = :company COLLATE NOCASE
        AND position = :position COLLATE NOCASE
        LIMIT 1'
    );

    $stmt->execute([
        ':company' => $company,
        ':position' => $position
    ]);

    $duplicate = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($duplicate && !$confirmed) {
        $duplicateWarning = true;
    }
     else {        
    $sql = "INSERT INTO applications (company, position, application_date, status, notes)
            VALUES (:company, :position, :date, :status, :notes)";
    
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':company' => $company,
        ':position' => $position,
        ':date' => $date,
        ':status' => $status,
        ':notes' => $notes
    ]);

    header('Location: main_page.php');
    exit;
    }
   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Application</title>
</head>
<body>

<h1>Create Application</h1>
<form action="create.php" method="post">
    <label>Company:</label>
    <input type="text" name="company" value="<?= htmlspecialchars($company ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

    <label>Position:</label>
    <input type="text" name="position" value="<?= htmlspecialchars($position ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

    <label>Application date:</label>
    <input type="date" name="application_date" value="<?= htmlspecialchars($date ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

    <label for="status">Status:</label>

    <select name="status" id="status" required>
        <?php foreach ($statuses as $option): ?>
            <option
                value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?>"
                <?= ($status ?? 'planned') === $option ? 'selected' : '' ?>
            >
                <?= htmlspecialchars(ucfirst($option), ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Notes:</label>
    <textarea name="notes"><?= htmlspecialchars($notes ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
    
    <?php if ($duplicateWarning): ?>
        <br>
        <button type="submit" name="confirm_duplicate" value="1" style="background-color: red;">Save anyway</button>
        <p style="color: red;">
        Duplicate Warning: An application for <?= htmlspecialchars($company ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> with the same position already exists.
    <br><br>
            Note that Application date, Status and Notes are not compared.
        </p>
    <?php else: ?>
    <br>
    <button type="submit">Save application</button>
    <?php endif; ?>
</form>

</body>
</html>