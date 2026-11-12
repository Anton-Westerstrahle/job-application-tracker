<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Tracker</title>
</head>
<body>
<h1>Job Tracker</h1>

<?php
require_once __DIR__ . '/db.php';
$allowedStatuses = [
    'planned',
    'submitted',
    'interview',
    'rejected',
    'withdrawn',
    'offer'
];
$selectedStatuses = $_GET['status'] ?? [];

if (!is_array($selectedStatuses)) {
    $selectedStatuses = [];
}

$selectedStatuses = array_values(
    array_intersect($allowedStatuses, $selectedStatuses)
);

$position = trim($_GET['position'] ?? '');
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM applications WHERE 1=1";
$params = [];
// Filter by company name
if ($search !== '') {
    $sql .= " AND company LIKE ?";
    $params[] = '%' . $search . '%';
}

// Filter by position
if ($position !== '') {
    $sql .= " AND position LIKE ?";
    $params[] = '%' . $position . '%';
}

// Filter by status
if (count($selectedStatuses) > 0) {
    $placeholders = implode(
        ',',
        array_fill(0, count($selectedStatuses), '?')
    );

    $sql .= " AND status COLLATE NOCASE IN ($placeholders)";

    foreach ($selectedStatuses as $status) {
        $params[] = $status;
    }
}

// From and to date filters
if ($dateFrom !== '') {
    $sql .= " AND application_date >= ?";
    $params[] = $dateFrom;
}

if ($dateTo !== '') {
    $sql .= " AND application_date <= ?";
    $params[] = $dateTo;
}

// Fetch column names from the applications table
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query('PRAGMA table_info(applications)');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo '<a href="create.php">Add Application</a>';

?>
<form action="main_page.php" method="GET">
     <label for="search">Company:</label>
         <input
        type="text"
        id="search"
        name="search"
        value="<?= htmlspecialchars($search, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
        placeholder="Search company..."
    >

        <p>Position:</p>
    <input
        type="text"
        name="position"
        value="<?= htmlspecialchars($position, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
    >

       <p>Filter by status:</p>
    <?php foreach ($allowedStatuses as $option): ?>
        <label>
            <input
                type="checkbox"
                name="status[]"
                value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?>"
                <?= in_array($option, $selectedStatuses, true)
                    ? 'checked' : '' ?>
            >
            <?= htmlspecialchars(ucfirst($option), ENT_QUOTES, 'UTF-8') ?>
        </label>
    <?php endforeach; ?>

    <p>Application date:</p>

    <label>From:</label>
    <input
        type="date"
        name="date_from"
        value="<?= htmlspecialchars($dateFrom, ENT_QUOTES, 'UTF-8') ?>"
    >

    <label>To:</label>
    <input
        type="date"
        name="date_to"
        value="<?= htmlspecialchars($dateTo, ENT_QUOTES, 'UTF-8') ?>"
    >

        <br><br>

<button type="submit">Search</button>
    <a href="main_page.php">Show all</a>
</form>
<?php
echo "<table border='1'>";
echo "<tr>";

// Show column names as table headers
foreach ($columns as $column) {
    echo "<th>"
        . htmlspecialchars($column['name'], ENT_QUOTES, 'UTF-8')
        . "</th>";
}

echo "<th>Edit</th>";
echo "<th>Delete</th>";
echo "</tr>";

// Show application data in table rows
foreach ($applications as $Row) {
    echo "<tr>";

    foreach ($columns as $column) {
        echo "<td>";

        $value = $Row[$column['name']] ?? '';

        echo htmlspecialchars(
            (string)$value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        echo "</td>";
    }
    echo '<td><a href="edit.php?id=' . (int)$Row['id'] . '">Edit</a></td>';
    echo '<td><a href="delete.php?id=' . (int)$Row['id'] . '">Delete</a></td>';
    echo "</tr>";
}

echo "</table>";
?>
</body>
</html>