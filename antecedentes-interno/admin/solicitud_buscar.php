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

$maxRows_Solicitud = 10;
$pageNum_Solicitud = 0;
if (isset($_GET['pageNum_Solicitud'])) {
  $pageNum_Solicitud = $_GET['pageNum_Solicitud'];
}
$startRow_Solicitud = $pageNum_Solicitud * $maxRows_Solicitud;

$colname_Solicitud = "-1";
if (isset($_POST['txtbuscar'])) {
  $colname_Solicitud = $_POST['txtbuscar'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitud = sprintf("SELECT * FROM solicitud WHERE dni = %s ORDER BY fecha_solicitada ASC", GetSQLValueString($colname_Solicitud, "text"));
$query_limit_Solicitud = sprintf("%s LIMIT %d, %d", $query_Solicitud, $startRow_Solicitud, $maxRows_Solicitud);
$Solicitud = mysql_query($query_limit_Solicitud, $antecedentes) or die(mysql_error());
$row_Solicitud = mysql_fetch_assoc($Solicitud);

if (isset($_GET['totalRows_Solicitud'])) {
  $totalRows_Solicitud = $_GET['totalRows_Solicitud'];
} else {
  $all_Solicitud = mysql_query($query_Solicitud);
  $totalRows_Solicitud = mysql_num_rows($all_Solicitud);
}
$totalPages_Solicitud = ceil($totalRows_Solicitud/$maxRows_Solicitud)-1;

$queryString_Solicitud = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Solicitud") == false && 
        stristr($param, "totalRows_Solicitud") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Solicitud = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Solicitud = sprintf("&totalRows_Solicitud=%d%s", $totalRows_Solicitud, $queryString_Solicitud);
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
  var caracteres = " abcdefghijklmn�opqrstuvwxyzABCDEFGHIJKLMN�OPQRSTUVWXYZ";
  var numeros_caracteres = numeros + caracteres;
  var teclas_especiales = [8, 37, 39, 45, 46];
  // 8 = BackSpace, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha
 
 
  // Seleccionar los caracteres a partir del par�metro de la funci�n
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
 <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
 <link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
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
				<div class="h_title">&#8250; Panel de Administraci�n</div>
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
            <h1>Buscar Solicitud</h1>
            <p>&nbsp;</p>
            <form id="form1" name="form1" method="post" action="">
              <h3>Ingrese Nro. de DNI: 
                <span id="sprytextfield1">
                <input type="text" name="txtbuscar" id="txtbuscar" />
                <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no v�lido.</span></span>
<input type="submit" name="button" id="button" value="Buscar" />
                <label for="txtbuscar"></label>
              </h3>
            </form>
            <h3>&nbsp;</h3>
            <?php if ($totalRows_Solicitud > 0) { // Show if recordset not empty ?>
  <table border="1">
    <tr>
      <td>Nro. DNI</td>
      <td>Apellido</td>
      <td>Nombre</td>
      <td>Prontuario A.G.</td>
      <td>Estado de la Solicitud</td>
      <td>Fecha Solicitada</td>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_Solicitud['dni']; ?></td>
        <td><?php echo $row_Solicitud['apellido']; ?></td>
        <td><?php echo $row_Solicitud['nombre']; ?></td>
        <td><?php echo $row_Solicitud['prontuario']; ?></td>
        <td><?php echo $row_Solicitud['estado_solicitud']; ?></td>
        <td>
		<?php 
$fecha = date_create($row_Solicitud['fecha_solicitada']); 
echo date_format($fecha, 'd/m/Y');
?>
        </td>
      </tr>
      <?php } while ($row_Solicitud = mysql_fetch_assoc($Solicitud)); ?>
  </table>
  
  		  <?php } else {
	  	if (isset($_POST['txtbuscar'])){ ?>
  		  <h3>No se encontraron resultados.</h3>
<?php }
	  }// Show if recordset not empty ?>
        
  <p>&nbsp;</p>
            <p>&nbsp;            
            
            <table border="0">
              <tr>
                <td><?php if ($pageNum_Solicitud > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Solicitud=%d%s", $currentPage, 0, $queryString_Solicitud); ?>">Primero</a>
                    <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_Solicitud > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Solicitud=%d%s", $currentPage, max(0, $pageNum_Solicitud - 1), $queryString_Solicitud); ?>">Anterior</a>
                    <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_Solicitud < $totalPages_Solicitud) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Solicitud=%d%s", $currentPage, min($totalPages_Solicitud, $pageNum_Solicitud + 1), $queryString_Solicitud); ?>">Siguiente</a>
                    <?php } // Show if not last page ?></td>
                <td><?php if ($pageNum_Solicitud < $totalPages_Solicitud) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Solicitud=%d%s", $currentPage, $totalPages_Solicitud, $queryString_Solicitud); ?>">&Uacute;ltimo</a>
                    <?php } // Show if not last page ?></td>
              </tr>
            </table>
            </p>
          </div>
	    </div>
		<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {validateOn:["change"]});
        </script>
		<!-- InstanceEndEditable --></div>
		</div>
		
      <p>&nbsp;</p>
      <div class="clear"></div>
	</div>

	<div id="footer">
		<div class="left">
			<p>� Policia de Catamarca - Desarrollado por �rea Inform�tica</p>
		</div>
		<div class="right">
			
		</div>
	</div>
</div>

</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($Solicitud);
?>
