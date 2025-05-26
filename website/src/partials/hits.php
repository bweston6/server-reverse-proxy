<?php
require_once dirname(__DIR__) . '/includes/db.php';

$db->exec('BEGIN');
$db->query('UPDATE hits SET count = count + 1');
$hits = $db->querySingle('SELECT h.count FROM hits h');
$db->exec('COMMIT');

return $hits;
?>
