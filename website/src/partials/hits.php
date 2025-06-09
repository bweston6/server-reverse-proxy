<?php
require_once ROOT_DIR . '/includes/db.php';

$db->exec('BEGIN');
$db->query('UPDATE hits SET count = count + 1');
$hits = $db->querySingle('SELECT h.count FROM hits h');
$db->exec('COMMIT');

return number_format($hits);
?>
