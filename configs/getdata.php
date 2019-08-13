<?php
$username="techmeds_FlexSystem";
$password="BoPest2016";
$database="techmeds_FlexSystem";
$mysqli = new mysqli("localhost", $username, $password, $database);
print "Working";
$mysqli->select_db($database) or die( "Unable to select database");
$mysqli->close();
?>
