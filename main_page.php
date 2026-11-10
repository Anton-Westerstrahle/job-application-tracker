<!DOCTYPE html>
<html>
<body>
<h1>Job Tracker</h1>

<?php
require_once __DIR__ . '/db.php';

echo '<a href="create.php">Add Application</a>';

$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM applications WHERE 1=1";
$params = [];
// Filter by company name
if ($search !== '') {
    $sql .= " AND company LIKE ?";
    $params[] = '%' . $search . '%';
}
// Fetch column names from the applications table
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query('PRAGMA table_info(applications)');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
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