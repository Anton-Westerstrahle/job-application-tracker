<?php

$dbPath = __DIR__ . '/data/Job-Tracker_database.db';
try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Database opened successfully";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}