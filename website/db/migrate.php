<?php

$db = new SQLite3('website.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
$db->enableExceptions(true);

// db settings
$db->exec("PRAGMA journal_mode = WAL;");

// migrate
$db->query('CREATE TABLE IF NOT EXISTS "hits" (
    "count" INTEGER DEFAULT 0
)');

// seed
$db->exec('BEGIN');
$rowCount = $db->querySingle('SELECT COUNT(*) FROM hits');
if ($rowCount !== 1) {
    $db->query('DELETE FROM hits');
    $db->query('INSERT INTO hits DEFAULT VALUES');
}
$db->exec('COMMIT');

$hitCount = $db->querySingle('SELECT h.count FROM hits h');
echo("User count: $hitCount" . PHP_EOL);
