<?php
require __DIR__ . '/db.php';

// Kontrollera om tabellen finns
$sql = "SELECT name FROM sqlite_master WHERE type='table'";

$result = $pdo->query($sql);

foreach ($result as $row) {
    echo "Table: " . $row['name'] . "\n";
}