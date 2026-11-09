<!DOCTYPE html>
<html>
<body>
<h1>Job Tracker</h1>

<?php
require_once __DIR__ . '/db.php';

echo '<a href="create.php">Add Application</a>';

// Fetch all applications from the database
$applications = $pdo->query('SELECT * FROM applications')
    ->fetchAll(PDO::FETCH_ASSOC);

// Fetch column names from the applications table
$stmt = $pdo->query('PRAGMA table_info(applications)');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr>";

// Show column names as table headers
foreach ($columns as $column) {
    echo "<th>"
        . htmlspecialchars($column['name'], ENT_QUOTES, 'UTF-8')
        . "</th>";
}

echo "<th>Edit</th>";
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
    echo "</tr>";
}

echo "</table>";
?>
</body>
</html>