<?php
$plainPassword = '12345';
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
echo $hash;
?>
