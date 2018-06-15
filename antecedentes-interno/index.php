<?php require_once('Connections/antecedentes.php'); ?>
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
// Defino la variable de session para tipo de usuario:
$_SESSION['tipo'] = $_POST['tipo'];

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['user'])) {
  $loginUsername=$_POST['user'];
  $password=$_POST['pass'];
  $MM_fldUserAuthorization = "tipo";
  $MM_redirectLoginSuccess = "paginas/index.php";
  $MM_redirectLoginFailed = "paginas/error.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_antecedentes, $antecedentes);
  	
  $LoginRS__query=sprintf("SELECT usuario, contrasena, tipo FROM usuario WHERE usuario=%s AND contrasena=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $antecedentes) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
	$loginStrGroup  = $_SESSION['tipo'];
    // reemplazo la session tipo $loginStrGroup  = mysql_result($LoginRS,0,'tipo');
    
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
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
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
					<label for="user">Usuario:</label>
					<input name="user" autofocus="autofocus" class="text" id="user"  />
					<label for="pass">Contraseña:</label>
					<input id="pass" name="pass" type="password" class="text" />
                  <label for="tipo">Tarea:</label>
                  <span id="spryselect1">
                    <label for="tipo2"></label>
                    <select name="tipo" id="tipo2">
                      <option>Seleccione una tarea</option>
                      <option value="entrada">Entrada</option>
                      <option value="archivo">Archivo</option>
                      <option value="confeccion">Confeccion</option>
                    </select>
                    <span class="selectRequiredMsg">Seleccione un elemento.</span></span>
                  <div class="sep"></div>
					<button type="submit" class="ok">Acceso</button> 
				</form>
			</div>
            <div class="entry">&raquo; © Policia de Catamarca - Desarrollado por Área Informática</div>
			<div>&raquo; <a href="admin/index.php" class="footer">Acceso Panel de Administración</a></div>
	  </div>
     
	</div>
</div>
<script type="text/javascript">
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
</script>
</body>
</html>
