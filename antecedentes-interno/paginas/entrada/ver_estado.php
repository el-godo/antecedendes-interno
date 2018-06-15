<?php require_once('../../Connections/antecedentes.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin,entrada";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "anularsolicitud")) {
  $updateSQL = sprintf("UPDATE solicitud SET estado_solicitud=%s WHERE id_solicitud=%s",
                       GetSQLValueString($_POST['estado_solicitud'], "text"),
                       GetSQLValueString($_POST['id_solicitud'], "int"));

  mysql_select_db($database_antecedentes, $antecedentes);
  $Result1 = mysql_query($updateSQL, $antecedentes) or die(mysql_error());
}

$maxRows_Solicitud = 10;
$pageNum_Solicitud = 0;
if (isset($_GET['pageNum_Solicitud'])) {
  $pageNum_Solicitud = $_GET['pageNum_Solicitud'];
}
$startRow_Solicitud = $pageNum_Solicitud * $maxRows_Solicitud;

$txtbuscar_Solicitud = "0";
if (isset($_POST['txtbuscar'])) {
  $txtbuscar_Solicitud = $_POST['txtbuscar'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitud = sprintf("SELECT * FROM solicitud WHERE solicitud.dni = %s AND solicitud.estado_solicitud != 'Completado' ORDER BY solicitud.fecha_solicitada DESC, solicitud.hora_solicitada DESC", GetSQLValueString($txtbuscar_Solicitud, "text"));
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

$maxRows_Historial = 10;
$pageNum_Historial = 0;
if (isset($_GET['pageNum_Historial'])) {
  $pageNum_Historial = $_GET['pageNum_Historial'];
}
$startRow_Historial = $pageNum_Historial * $maxRows_Historial;

$valorDNI_Historial = "0";
if (isset($_POST['txtbuscar'])) {
  $valorDNI_Historial = $_POST['txtbuscar'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Historial = sprintf("SELECT * FROM historial WHERE historial.dni = %s ORDER BY historial.fecha DESC", GetSQLValueString($valorDNI_Historial, "int"));
$query_limit_Historial = sprintf("%s LIMIT %d, %d", $query_Historial, $startRow_Historial, $maxRows_Historial);
$Historial = mysql_query($query_limit_Historial, $antecedentes) or die(mysql_error());
$row_Historial = mysql_fetch_assoc($Historial);

if (isset($_GET['totalRows_Historial'])) {
  $totalRows_Historial = $_GET['totalRows_Historial'];
} else {
  $all_Historial = mysql_query($query_Historial);
  $totalRows_Historial = mysql_num_rows($all_Historial);
}
$totalPages_Historial = ceil($totalRows_Historial/$maxRows_Historial)-1;

$queryString_Historial = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Historial") == false && 
        stristr($param, "totalRows_Historial") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Historial = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Historial = sprintf("&totalRows_Historial=%d%s", $totalRows_Historial, $queryString_Historial);
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
					<li class="b1"><a class="icon view_asignar" href="index.php">Generar Solicitud</a></li>
                    <li class="b2"><a class="icon view_asignar" href="ver_estado.php">Ver Estado</a></li>
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
					<li class="b1"><a class="icon view_imprimir" href="../confeccion/listado_solicitudes.php">Imprimir Ingresados por Sistema</a></li>
                    <li class="b2"><a class="icon view_reasignar" href="../confeccion/alta_solicitante.php">Alta de Solicitante</a></li>
                                     
                    <li class="b4"><a class="icon view_padron" href="../confeccion/listado_solicitante.php">Modificar Datos Solicitante</a></li>
                    <li class="b3"><a class="icon view_imprimir" href="../confeccion/listado_solicitudes_impresas.php">Certificados Impresos</a></li>
                    
                    <li class="b5"><a class="icon view_imprimir" href="../confeccion/configurar_margenes.php">Configurar Margenes</a></li>
                    
                    <li class="b6"><a class="icon view_imprimir" href="../confeccion/generar_certificado.php">Generar Certificados Nuevos, del Interior y Urgentes</a></li>
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
<script language="JavaScript">
function confirmar ( mensaje ) {
return confirm( mensaje );
} 
</script>

		    <h1>Ver Estado de la Solicitud</h1>
		    <p>&nbsp;</p>
          <form id="form1" name="form1" method="post" action="">
            <h3>Ingrese DNI:  
              <span id="sprytextfield1">
              <input name="txtbuscar" type="text" autofocus="autofocus" id="txtbuscar" />
              <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span>
<input type="submit" name="button" id="button" value="Buscar" />
<label for="txtbuscar"></label>
            </h3>
          </form>
          <?php if (isset($_POST['txtbuscar']) && $totalRows_Solicitud == 0) { // Show if recordset empty ?>
            <h3>No se encontr&oacute; ninguna solicitud con ese Nro de Documento.</h3>
            <?php } // Show if recordset empty ?>
<p>&nbsp;</p>
            <?php if ($totalRows_Solicitud > 0) { // Show if recordset not empty ?>
            <h3 class="subrayado">Ultimas 10 Solicitudes:</h3>
  <table border="1">
    <tr>
      <td>DNI:</td>
      <td>Apellido</td>
      <td>Nombre:</td>
      <td>Estado de Solicitud</td>
      <td>Fecha Solicitada:</td>
      <td>Hora Solicitada</td>
      <td>Anular Solicitud</td>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_Solicitud['dni']; ?></td>
        <td><?php echo $row_Solicitud['apellido']; ?></td>
        <td><?php echo $row_Solicitud['nombre']; ?></td>
        <td><?php echo $row_Solicitud['estado_solicitud']; ?></td>
        <td><?php echo $row_Solicitud['fecha_solicitada']; ?></td>
        <td><?php echo $row_Solicitud['hora_solicitada']; ?></td>
        <td>
		<?php if ($row_Solicitud['estado_solicitud'] != 'Anulado') { // Show if recordset empty ?>
            <form action="<?php echo $editFormAction; ?>" method="POST" name="anularsolicitud">
              <input name="id_solicitud" type="hidden" value="<?php echo $row_Solicitud['id_solicitud']; ?>" />
              <input name="estado_solicitud" type="hidden" value="Anulado" />
              <input name="txtbuscar" type="hidden" value="<?php echo $row_Solicitud['dni']; ?>" />
              <input type="hidden" name="MM_update" value="anularsolicitud" />
              <input type="submit" onclick="return confirmar('Esta seguro que desea anular la solicitud?')" value="Anular Solicitud" />
            </form>
            <?php } // Show if recordset empty ?></td>
      </tr>
      <?php } while ($row_Solicitud = mysql_fetch_assoc($Solicitud)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
<h2 class="subrayado">Estados:</h2>
<h4> * Pendiente = La solicitud se encuentra en Archivo.</h4>
<h4> * Confeccion = La solicitud se encuentra en proceso de Confecci&oacute;n e Impresi&oacute;n</h4>
<h4> * Completado = La solicitud ha sido procesada e impresa.</h4>
<h4>  (Puede faltar Fiscalizaci&oacute;n y/o Firmas para completar el proceso de entrega).</h4>
<p>&nbsp;</p>
<h4 class="ui-state-highlight">Se imprimieron <?php echo $totalRows_Historial ?> Certificados de esta persona.</h4>
<?php if ($totalRows_Historial > 0) { // Show if recordset not empty ?>
  <h2 class="subrayado">Historial de Certificados (Ultimos 10 Certificados Impresos):</h2>
  
  <table border="1">
    <tr>
      <td>Nombre</td>
      <td>Apellido</td>
      <td>Nro Certificado</td>
      <td>Capital o Interior</td>
      <td>Intervino</td>
      <td>Fecha de Impresión</td>
      <td>Hora</td>
      <td>Solicitado Por</td>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_Historial['nombre']; ?></td>
        <td><?php echo $row_Historial['apellido']; ?></td>
        <td><?php echo $row_Historial['nro_certificado']; ?></td>
        <td><?php echo $row_Historial['capitalinterior']; ?></td>
        <td><?php echo $row_Historial['intervino']; ?></td>
        <td><?php echo $row_Historial['fecha']; ?></td>
        <td><?php echo $row_Historial['hora']; ?></td>
        <td><?php echo $row_Historial['solicitado_por']; ?></td>
      </tr>
      <?php } while ($row_Historial = mysql_fetch_assoc($Historial)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
</p>
<head>
		  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {validateOn:["change"]});
          </script>
		  <!-- InstanceEndEditable --></div>
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
mysql_free_result($Solicitud);

mysql_free_result($Historial);
?>
