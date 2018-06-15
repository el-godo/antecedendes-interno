<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_antecedentes = "localhost";
$database_antecedentes = "antecedentesinterno";
$username_antecedentes = "root";
$password_antecedentes = "informatica_0";
$antecedentes = mysql_pconnect($hostname_antecedentes, $username_antecedentes, $password_antecedentes) or trigger_error(mysql_error(),E_USER_ERROR); 
?>