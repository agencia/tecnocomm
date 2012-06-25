<?php
require_once("lib/BD.php");
$host="10.6.186.75";
$username="tecnocomm";
$pass="tec.55.A";
$database="tecnocomm";
$tecnobd = new BD($host,$username,$pass,$database);
$connection = $tecnobd->Connection();
?>