<?php require_once('../../Connections/antecedentes.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin,confeccion";
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

$MM_restrictGoTo = "../error.php";
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
if (!isset($_POST['txtbuscar'])){
	$_POST['txtbuscar'] = "";
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

mysql_select_db($database_antecedentes, $antecedentes);
$query_Usuario = "SELECT id_dependencia, usuario FROM usuario WHERE usuario = '" .$_SESSION['MM_Username']. "'";
$Usuario = mysql_query($query_Usuario, $antecedentes) or die(mysql_error());
$row_Usuario = mysql_fetch_assoc($Usuario);
$totalRows_Usuario = mysql_num_rows($Usuario);

$maxRows_Lista_Solicitudes = 10;
$pageNum_Lista_Solicitudes = 0;
if (isset($_GET['pageNum_Lista_Solicitudes'])) {
  $pageNum_Lista_Solicitudes = $_GET['pageNum_Lista_Solicitudes'];
}
$startRow_Lista_Solicitudes = $pageNum_Lista_Solicitudes * $maxRows_Lista_Solicitudes;

$colname_Lista_Solicitudes = "-1";
if (isset($_POST['txtbuscar'])) {
  $colname_Lista_Solicitudes = $_POST['txtbuscar'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Lista_Solicitudes = sprintf("SELECT * FROM solicitud WHERE dni = %s AND solicitud.estado_solicitud = 'Confeccion' ORDER BY fecha_solicitada ASC, solicitud.hora_solicitada ASC", GetSQLValueString($colname_Lista_Solicitudes, "text"));
$query_limit_Lista_Solicitudes = sprintf("%s LIMIT %d, %d", $query_Lista_Solicitudes, $startRow_Lista_Solicitudes, $maxRows_Lista_Solicitudes);
$Lista_Solicitudes = mysql_query($query_limit_Lista_Solicitudes, $antecedentes) or die(mysql_error());
$row_Lista_Solicitudes = mysql_fetch_assoc($Lista_Solicitudes);

if (isset($_GET['totalRows_Lista_Solicitudes'])) {
  $totalRows_Lista_Solicitudes = $_GET['totalRows_Lista_Solicitudes'];
} else {
  $all_Lista_Solicitudes = mysql_query($query_Lista_Solicitudes);
  $totalRows_Lista_Solicitudes = mysql_num_rows($all_Lista_Solicitudes);
}
$totalPages_Lista_Solicitudes = ceil($totalRows_Lista_Solicitudes/$maxRows_Lista_Solicitudes)-1;

$maxRows_Lista_Tabla2 = 10;
$pageNum_Lista_Tabla2 = 0;
if (isset($_GET['pageNum_Lista_Tabla2'])) {
  $pageNum_Lista_Tabla2 = $_GET['pageNum_Lista_Tabla2'];
}
$startRow_Lista_Tabla2 = $pageNum_Lista_Tabla2 * $maxRows_Lista_Tabla2;

mysql_select_db($database_antecedentes, $antecedentes);
$query_Lista_Tabla2 = "SELECT * FROM solicitud WHERE estado_solicitud = 'Confeccion' ORDER BY fecha_solicitada ASC, solicitud.hora_solicitada ASC";
$query_limit_Lista_Tabla2 = sprintf("%s LIMIT %d, %d", $query_Lista_Tabla2, $startRow_Lista_Tabla2, $maxRows_Lista_Tabla2);
$Lista_Tabla2 = mysql_query($query_limit_Lista_Tabla2, $antecedentes) or die(mysql_error());
$row_Lista_Tabla2 = mysql_fetch_assoc($Lista_Tabla2);

if (isset($_GET['totalRows_Lista_Tabla2'])) {
  $totalRows_Lista_Tabla2 = $_GET['totalRows_Lista_Tabla2'];
} else {
  $all_Lista_Tabla2 = mysql_query($query_Lista_Tabla2);
  $totalRows_Lista_Tabla2 = mysql_num_rows($all_Lista_Tabla2);
}
$totalPages_Lista_Tabla2 = ceil($totalRows_Lista_Tabla2/$maxRows_Lista_Tabla2)-1;

$queryString_Lista_Tabla2 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Lista_Tabla2") == false && 
        stristr($param, "totalRows_Lista_Tabla2") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Lista_Tabla2 = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Lista_Tabla2 = sprintf("&totalRows_Lista_Tabla2=%d%s", $totalRows_Lista_Tabla2, $queryString_Lista_Tabla2);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl"><!-- InstanceBegin template="/Templates/plantillasitio.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="author" content="Pawel 'kilab' Balicki - kilab.pl" />
<title>Sistema de Solicitud de Antecedentes Personales **** Polic&iacute;a de Catamarca - Ministerio de Gobierno y Justicia</title>
<link rel="stylesheet" type="text/css" href="../../css/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../../css/navi.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../../css/tcal.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css" media="screen" />
<!--<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>-->
<!--<script type="text/javascript" src="../js/tcal.js"></script>-->
<!--<script type="text/javascript" src="../js/ui/1.9.1/jquery-1.9.1.js"></script>-->
<!--<script type="text/javascript" src="../js/ui/1.10.3/jquery-ui.js"></script>-->

<script src="../../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />

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
</head>
<body>
<?php 
		$ipcliente = ObtenerRealIP(); 
?>
<?php 
$fecha = date("d/m/Y"); 
?>

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
				
				
                
								<li class="upp">Cerrar Sesión
<ul>
		    <li>&#8250; <a href="../logout.php">Salir del sistema</a></li>
						
			</ul>
				</li>
                
                <li class="upp"><a href="#">Usuario Logueado: <?php echo $_SESSION['MM_Username']; ?></a></li>
                
                 <li class="upp"><a href="#">Su IP es: <?php echo $ipcliente; ?></a></li>
                 
                 <li class="upp"><a href="#">Hoy es: <?php echo $fecha; ?></a></li>
			</ul>
</div>
	</div>
	
	<div id="content">
		<div id="sidebar">
        <?php
			// Aqui comienza el if que muestra el panel solo si sos user super
			if ($_SESSION['MM_UserGroup'] == 'entrada' OR $_SESSION['MM_UserGroup'] == 'admin') { // Show if recordset empty ?>
			<div class="box">
				<div class="h_title">&#8250; Mesa de Entrada</div>
				<ul id="home">
					<li class="b1"><a class="icon view_asignar" href="../entrada/index.php">Generar Solicitud</a></li>
                    <li class="b2"><a class="icon view_asignar" href="../entrada/ver_estado.php">Ver Estado</a></li>
				</ul>
			</div>
        <?php } ?>

            
         <?php
			// Aqui comienza el if que muestra el panel solo si sos user super
			if ($_SESSION['MM_UserGroup'] == 'archivo' OR $_SESSION['MM_UserGroup'] == 'admin') { // Show if recordset empty ?>   
            <div class="box">
				<div class="h_title">&#8250; Archivo</div>
				<ul id="home">
					<li class="b1"><a class="icon view_asignar" href="../archivo/solicitud_pendiente.php">Solicitudes Pendientes</a></li>
				</ul>
			</div>
			<?php }?>
            
            
            <?php
			// Aqui comienza el if que muestra el panel solo si sos user super
			if ($_SESSION['MM_UserGroup'] == 'confeccion' OR $_SESSION['MM_UserGroup'] == 'admin') { // Show if recordset empty ?>
			<div class="box">
				<div class="h_title">&#8250; Confección e Impresión</div>
				<ul>
					<li class="b1"><a class="icon view_imprimir" href="listado_solicitudes.php">Imprimir Ingresados por Sistema</a></li>
                    <li class="b2"><a class="icon view_reasignar" href="alta_solicitante.php">Alta de Solicitante</a></li>
                                     
                    <li class="b4"><a class="icon view_padron" href="listado_solicitante.php">Modificar Datos Solicitante</a></li>
                    <li class="b3"><a class="icon view_imprimir" href="listado_solicitudes_impresas.php">Certificados Impresos</a></li>
                    
                    <li class="b5"><a class="icon view_imprimir" href="configurar_margenes.php">Configurar Margenes</a></li>
                    
                    <li class="b6"><a class="icon view_imprimir" href="generar_certificado.php">Generar Certificados Nuevos, del Interior y Urgentes</a></li>
				</ul>
			</div>
            <?php }?>
            
            
            <div class="box">
				<div class="h_title">&#8250; Sistema</div>
				<ul>
                
                <li class="b1"><a class="icon view_help" href="../manual_usuario.php">Manual de Usuario</a></li>
				<li class="b2"><a class="icon view_deposito" href="../logout.php">Salir del Sistema</a></li>
                    
                    
                   
				</ul>
			</div>
            
		</div>
		<div id="main">
		  <div class="full_w"><!-- InstanceBeginEditable name="EditRegion1" -->
		    <h1>Imprimir Certificados Ingresados por Mesa de Entrada</h1>
		    <p></p>
		    <h3>&nbsp;</h3>
		    <form id="form1" name="form1" method="post" action="">
		      <h3>Ingrese el DNI:
	            
	            <span id="sprytextfield1">
                <input name="txtbuscar" type="text" autofocus="autofocus" id="txtbuscar" />
                <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span>
<input type="submit" name="button" id="button" value="Buscar" />
		      </h3>
	      </form>
            <?php if ($totalRows_Lista_Solicitudes == 0 && $_POST['txtbuscar'] != '') { // Show if recordset empty ?>
  <h3>No Existe ninguna solicitud para ese Nro. de DNI. Debe ingresar por mesa de entrada o Ir a Generar Certificado</h3>
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_Lista_Solicitudes > 0) { // Show if recordset not empty ?>
              <table border="1">
                <tr align="center">
                  <td>DNI</td>
                  <td>Apellido</td>
                  <td>Nombre</td>
                  <td>Fecha Solicitada</td>
                  <td>Hora Solicitada</td>
                  <td>Acci&oacute;n</td>
                </tr>
                <?php do { ?>
                  <tr align="center">
                    <td><?php echo $row_Lista_Solicitudes['dni']; ?></td>
                    <td><?php echo $row_Lista_Solicitudes['apellido']; ?></td>
                    <td><?php echo $row_Lista_Solicitudes['nombre']; ?></td>
                    <td><?php 
$fecha = date_create($row_Lista_Solicitudes['fecha_solicitada']); 
echo date_format($fecha, 'd/m/Y');
?>
                    <td><?php echo $row_Lista_Solicitudes['hora_solicitada']; ?>                    
                    <td><form action="solicitud_procesar.php" method="post">
                    <input name="DocumentoNro" type="hidden" value="<?php echo $row_Lista_Solicitudes['dni']; ?>" />
                    <input name="id_solicitud" type="hidden" value="<?php echo $row_Lista_Solicitudes['id_solicitud']; ?>" />
                    <input name="" type="submit" value="Procesar"/>
                    </form>
                    </td>
                  </tr>
                  <?php } while ($row_Lista_Solicitudes = mysql_fetch_assoc($Lista_Solicitudes)); ?>
              </table>
              <?php } // Show if recordset not empty ?>
          <p>&nbsp;</p>
          <table border="1">
              <tr align="center">
                <td>DNI</td>
                <td>Apellido</td>
                <td>Nombre</td>
                <td>Fecha Solicitada</td>
                <td>Hora Solicitada</td>
                <td>Acci&oacute;n</td>
              </tr>
              <?php do { ?>
                <tr align="center">
                  <td><?php echo $row_Lista_Tabla2['dni']; ?></td>
                  <td><?php echo $row_Lista_Tabla2['apellido']; ?></td>
                  <td><?php echo $row_Lista_Tabla2['nombre']; ?></td>
                  <td><?php $fecha = date_create($row_Lista_Tabla2['fecha_solicitada']);
				  echo date_format($fecha,'d/m/Y');
				   ?></td>
                  <td><?php echo $row_Lista_Tabla2['hora_solicitada']; ?></td>
                  <td><form action="solicitud_procesar.php" method="post">
                    <input name="DocumentoNro" type="hidden" value="<?php echo $row_Lista_Tabla2['dni']; ?>" />
                    <input name="id_solicitud" type="hidden" value="<?php echo $row_Lista_Tabla2['id_solicitud']; ?>" />
                    <input name="" type="submit" value="Procesar"/>
                    </form></td>
                </tr>
                <?php } while ($row_Lista_Tabla2 = mysql_fetch_assoc($Lista_Tabla2)); ?>
            </table>
            <table border="0">
              <tr>
                <td><?php if ($pageNum_Lista_Tabla2 > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Lista_Tabla2=%d%s", $currentPage, 0, $queryString_Lista_Tabla2); ?>">Primero</a>
                    <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_Lista_Tabla2 > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Lista_Tabla2=%d%s", $currentPage, max(0, $pageNum_Lista_Tabla2 - 1), $queryString_Lista_Tabla2); ?>">Anterior</a>
                    <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_Lista_Tabla2 < $totalPages_Lista_Tabla2) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Lista_Tabla2=%d%s", $currentPage, min($totalPages_Lista_Tabla2, $pageNum_Lista_Tabla2 + 1), $queryString_Lista_Tabla2); ?>">Siguiente</a>
                    <?php } // Show if not last page ?></td>
                <td><?php if ($pageNum_Lista_Tabla2 < $totalPages_Lista_Tabla2) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Lista_Tabla2=%d%s", $currentPage, $totalPages_Lista_Tabla2, $queryString_Lista_Tabla2); ?>">&Uacute;ltimo</a>
                    <?php } // Show if not last page ?></td>
              </tr>
          </table>
            </p>
          <h4>&nbsp;
Registros <?php echo ($startRow_Lista_Tabla2 + 1) ?> a <?php echo min($startRow_Lista_Tabla2 + $maxRows_Lista_Tabla2, $totalRows_Lista_Tabla2) ?> de <?php echo $totalRows_Lista_Tabla2 ?> </h4>
          <h4>&nbsp;</h4>
		  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {validateOn:["change"]});
          </script>
		  <head><!-- InstanceEndEditable --></div>
		</div>
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
mysql_free_result($Usuario);

mysql_free_result($Lista_Solicitudes);

mysql_free_result($Lista_Tabla2);
?>
