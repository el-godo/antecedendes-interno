<?php require_once('../Connections/antecedentes.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "super";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "error.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_Solicitudes = 10;
$pageNum_Solicitudes = 0;
if (isset($_GET['pageNum_Solicitudes'])) {
  $pageNum_Solicitudes = $_GET['pageNum_Solicitudes'];
}
$startRow_Solicitudes = $pageNum_Solicitudes * $maxRows_Solicitudes;

mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitudes = "SELECT * FROM solicitud WHERE estado_solicitud = 'Pendiente' ORDER BY fecha_solicitada ASC";
$query_limit_Solicitudes = sprintf("%s LIMIT %d, %d", $query_Solicitudes, $startRow_Solicitudes, $maxRows_Solicitudes);
$Solicitudes = mysql_query($query_limit_Solicitudes, $antecedentes) or die(mysql_error());
$row_Solicitudes = mysql_fetch_assoc($Solicitudes);

if (isset($_GET['totalRows_Solicitudes'])) {
  $totalRows_Solicitudes = $_GET['totalRows_Solicitudes'];
} else {
  $all_Solicitudes = mysql_query($query_Solicitudes);
  $totalRows_Solicitudes = mysql_num_rows($all_Solicitudes);
}
$totalPages_Solicitudes = ceil($totalRows_Solicitudes/$maxRows_Solicitudes)-1;

$queryString_Solicitudes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Solicitudes") == false && 
        stristr($param, "totalRows_Solicitudes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Solicitudes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Solicitudes = sprintf("&totalRows_Solicitudes=%d%s", $totalRows_Solicitudes, $queryString_Solicitudes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl"><!-- InstanceBegin template="/Templates/plantillaadmin.dwt.php" codeOutsideHTMLIsLocked="false" -->
<?php 
//Funcion para obtener la IP del Cliente
function ObtenerRealIP() {
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
		return $_SERVER['HTTP_CLIENT_IP'];
	   
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
   
	return $_SERVER['REMOTE_ADDR'];
}
?>



<script type="text/javascript" src="js/funciones.js"></script>

<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="author" content="Pawel 'kilab' Balicki - kilab.pl" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Sistema de Solicitud de Antecedentes Personales **** Polic&iacute;a de Catamarca - Ministerio de Gobierno y Justicia</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/navi.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/tcal.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" media="screen" />

<!--<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>-->
<!--<script type="text/javascript" src="../js/tcal.js"></script>-->

<script type="text/javascript" src="js/ui/1.9.1/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/ui/1.10.3/jquery-ui.js"></script>


 <script>
$(function() {
$( "#dialog-message" ).dialog({
modal: true,
buttons: {
Ok: function() {
$( this ).dialog( "close" );
}
}
});
});
</script>


<script type="text/javascript"> 
$(function(){
	$(".box .h_title").not(this).next("ul").hide("normal");
	$(".box .h_title").not(this).next("#home").show("normal");
	$(".box").children(".h_title").click( function() { $(this).next("ul").slideToggle(); });
});
</script>

<!-- Funcion para solo numeros y/o letras-->

 <script>
          function permite(elEvento, permitidos) {
  // Variables que definen los caracteres permitidos
  var numeros = "0123456789";
  var caracteres = " abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
  var numeros_caracteres = numeros + caracteres;
  var teclas_especiales = [8, 37, 39, 45, 46];
  // 8 = BackSpace, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha
 
 
  // Seleccionar los caracteres a partir del parámetro de la función
  switch(permitidos) {
    case 'num':
      permitidos = numeros;
      break;
    case 'car':
      permitidos = caracteres;
      break;
    case 'num_car':
      permitidos = numeros_caracteres;
      break;
  }
 
  // Obtener la tecla pulsada 
  var evento = elEvento || window.event;
  var codigoCaracter = evento.charCode || evento.keyCode;
  var caracter = String.fromCharCode(codigoCaracter);
 
  // Comprobar si la tecla pulsada es alguna de las teclas especiales
  // (teclas de borrado y flechas horizontales)
  var tecla_especial = false;
  for(var i in teclas_especiales) {
    if(codigoCaracter == teclas_especiales[i]) {
      tecla_especial = true;
      break;
    }
  }
 
  // Comprobar si la tecla pulsada se encuentra en los caracteres permitidos
  // o si es una tecla especial
  return permitidos.indexOf(caracter) != -1 || tecla_especial;
}
          </script>
 <!-- InstanceBeginEditable name="head" -->
 <!-- InstanceEndEditable -->
</head>
<body>
<?php 
		$ipcliente = ObtenerRealIP(); 
		?>
         <?php $fecha = date("d/m/Y"); ?>
<div class="wrap">
	<div id="header">
		<div id="top">
			
			<div class="right">
				<div class="align-right">
					<p>Sistema de Solicitud de Antecedentes Personales **** Polic&iacute;a de Catamarca - Ministerio de Gobierno y Justicia</p>
				</div>
			</div>
		</div> 
		<div id="nav">
			<ul>
				
				<li class="upp"><a href="admin.php">Inicio</a></li>
                
				<li class="upp"><a href="<?php echo $logoutAction ?>">Desconectar</a></li>
                
                <li class="upp"><a href="#">Usuario Logueado: <?php echo $_SESSION['MM_Username']; ?></a></li>
                
                 <li class="upp"><a href="#">Su IP es: <?php echo $ipcliente; ?></a></li>
                 
                 <li class="upp"><a href="#">Hoy es: <?php echo $fecha; ?></a></li>
                               
               
			</ul>
		</div>
        
    <span class="active"><span class="Bandita"></div>
	
	<div id="content">
		<div id="sidebar">
			<div class="box">
				<div class="h_title">&#8250; Panel de Administración</div>
				<ul id="home">
					<li class="b1"><a class="icon view_asignar" href="admin.php">Administraci&oacute;n</a></li>
                    <li class="b2"><a class="icon view_deposito" href="<?php echo $logoutAction ?>">Salir del Sistema</a></li> 
                    
				</ul>
			</div>

            
            
            
		</div>
        <div id="main">
		  <div class="full_w"><!-- InstanceBeginEditable name="EditRegion3" -->
		<div id="main">
		  <div class="full_w">
		    <div id="&rdquo;stylized&rdquo;" class="&rdquo;myform&rdquo;"></div>
            <h1>Listado de Solicitudes Pendientes</h1>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <table border="1">
              <tr>
                <td align="center">DNI</td>
                <td align="center">Apellido</td>
                <td align="center">Nombres</td>
                <td align="center">Prontuario</td>
                <td align="center">Fecha<br />
                  Solicitada</td>
              </tr>
              <?php do { ?>
                <tr>
                  <td align="justify"><?php echo $row_Solicitudes['dni']; ?></td>
                  <td align="justify"><?php echo $row_Solicitudes['apellido']; ?></td>
                  <td align="justify"><?php echo $row_Solicitudes['nombre']; ?></td>
                  <td align="center"><?php echo $row_Solicitudes['prontuario']; ?></td>
                  <td align="center"><p>
                    <?php 
				  $fecha = date_create($row_Solicitudes['fecha_solicitada']); 
				  echo date_format($fecha, 'd/m/Y');
				  ?>
                    <br />
                  </p></td>
                </tr>
                <?php } while ($row_Solicitudes = mysql_fetch_assoc($Solicitudes)); ?>
            </table>
            <p>&nbsp;            
              
            <table border="0">
              <tr>
                <td><?php if ($pageNum_Solicitudes > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Solicitudes=%d%s", $currentPage, 0, $queryString_Solicitudes); ?>">Primero</a>
                    <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_Solicitudes > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Solicitudes=%d%s", $currentPage, max(0, $pageNum_Solicitudes - 1), $queryString_Solicitudes); ?>">Anterior</a>
                    <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_Solicitudes < $totalPages_Solicitudes) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Solicitudes=%d%s", $currentPage, min($totalPages_Solicitudes, $pageNum_Solicitudes + 1), $queryString_Solicitudes); ?>">Siguiente</a>
                    <?php } // Show if not last page ?></td>
                <td><?php if ($pageNum_Solicitudes < $totalPages_Solicitudes) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Solicitudes=%d%s", $currentPage, $totalPages_Solicitudes, $queryString_Solicitudes); ?>">&Uacute;ltimo</a>
                    <?php } // Show if not last page ?></td>
              </tr>
            </table>
            <p>&nbsp;
Solicitudes <?php echo ($startRow_Solicitudes + 1) ?> a <?php echo min($startRow_Solicitudes + $maxRows_Solicitudes, $totalRows_Solicitudes) ?> de <?php echo $totalRows_Solicitudes ?> </p>
            </p>
          </div>
	    </div>
		<!-- InstanceEndEditable --></div>
		</div>
		
      <p>&nbsp;</p>
      <div class="clear"></div>
	</div>

	<div id="footer">
		<div class="left">
			<p>© Policia de Catamarca - Desarrollado por Área Informática</p>
		</div>
		<div class="right">
			
		</div>
	</div>
</div>

</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($Solicitudes);

?>
