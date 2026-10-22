<?php
$dbpath = __DIR__ . '/data/Job-Tracker_database.db';
if (!file_exists($dbpath)){
    die ('Database file not found.');
}
$pdo = new PDO('sqlite:' . $dbpath);

$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);