<?php
use SQLite3;

$db = new SQLite3('/var/data/website.db', SQLITE3_OPEN_READWRITE);

$db->enableExceptions(true);
$db->busyTimeout(5000);
$db->exec('PRAGMA synchronous = 0');
?>
