<?php
$mysqli = new mysqli("localhost", "root", "", "product_system");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
