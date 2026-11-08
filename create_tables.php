<?php
require __DIR__ . '/db.php';

$sql = <<<SQL
CREATE TABLE IF NOT EXISTS applications (
    id INTEGER PRIMARY KEY,
    company TEXT NOT NULL,
    position TEXT NOT NULL,
    application_date TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'planned',
    notes TEXT
);
SQL;

$pdo->exec($sql);

echo "Tabel has been created";