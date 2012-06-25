<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_tecnocomm = "localhost";
$database_tecnocomm = "tecnocomm";
$username_tecnocomm = "root";
$password_tecnocomm = "root";
$tecnocomm = mysql_pconnect($hostname_tecnocomm, $username_tecnocomm, $password_tecnocomm) or trigger_error(mysql_error(),E_USER_ERROR); 
?>
