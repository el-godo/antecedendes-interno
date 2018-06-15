<?php require_once('../Connections/antecedentes.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['user'])) {
  $loginUsername=$_POST['user'];
  $password=$_POST['pass'];
  $MM_fldUserAuthorization = "tipo";
  $MM_redirectLoginSuccess = "admin.php";
  $MM_redirectLoginFailed = "error.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_antecedentes, $antecedentes);
  	
  $LoginRS__query=sprintf("SELECT usuario, contrasena, tipo FROM usuario WHERE usuario=%s AND contrasena=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $antecedentes) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'tipo');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Paweł 'kilab' Balicki - kilab.pl" />
<title>Sistema de Solicitud de Antecedentes Personales **** Polic&iacute;a de Catamarca - Ministerio de Gobierno y Justicia</title>
<link rel="stylesheet" type="text/css" href="css/login.css" media="screen" />
<style type="text/css">
body {
	background-color: #999999;
}
</style>
</head>
<body bgcolor="#D6D6D6" background="img/body_bg.gif">
<div class="wrap">

  <div id="content">
		
        
    <div id="main">
        <div><h2 align="center">&nbsp;</h2>
<h3 align="center">&nbsp;</h3>
        <h3 align="center">&nbsp;</h3>
        <h3 align="center">&nbsp;</h3>
        <h3 align="center">&nbsp;</h3>
        <h3 align="center">&nbsp;</h3>
        <h3 align="center">&nbsp;</h3>
        <h3 align="center">&nbsp;</h3>
        <h3 align="center">&nbsp;</h3>
        <p align="center"><br />
        </p>
</div>
        <div align="center"></div>
			<div class="full_w">
				<form action="<?php echo $loginFormAction; ?>" method="POST" id="login">
					<label for="user">Acceso a Panel de Administración:<br />
					  <br />
				    Usuario:</label>
					<input id="user" name="user" class="text" />
					<label for="pass">Contraseña:</label>
					<input id="pass" name="pass" type="password" class="text" />
					<div class="sep"></div>
					<button type="submit" class="ok">Acceso</button> 
				</form>
			</div>
			<div class="footer">&raquo; © Policia de Catamarca - Desarrollado por Área Informática</div>
	  </div>
     
	</div>
</div>

</body>
</html>
