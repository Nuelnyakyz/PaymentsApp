<?php
echo "PDO drivers: ";
print_r(PDO::getAvailableDrivers());
echo "\nSQLite extension loaded: " . (extension_loaded('pdo_sqlite') ? 'YES' : 'NO');
echo "\nSQLite3 extension loaded: " . (extension_loaded('sqlite3') ? 'YES' : 'NO');
?>
