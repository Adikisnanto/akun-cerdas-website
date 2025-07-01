<?php
// Ganti 'PasswordRahasiaAdmin' dengan password yang Anda inginkan
$password_polos = 'PasswordRahasiaAdmin'; 

$hash_password = password_hash($password_polos, PASSWORD_BCRYPT);

echo "Password Anda: " . $password_polos . "<br>";
echo "Hash untuk database: <br>";
echo "<textarea rows='3' cols='70' readonly>" . $hash_password . "</textarea>";
?>