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

$currentPage = $_SERVER["PHP_SELF"];

$docu_Busqueda = "0";
if (isset($_POST["dni"])) {
  $docu_Busqueda = $_POST["dni"];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Busqueda = sprintf("SELECT * FROM solicitud WHERE solicitud.dni = %s", GetSQLValueString($docu_Busqueda, "int"));
$Busqueda = mysql_query($query_Busqueda, $antecedentes) or die(mysql_error());
$row_Busqueda = mysql_fetch_assoc($Busqueda);
$maxRows_Busqueda = 10;
$pageNum_Busqueda = 0;
if (isset($_GET['pageNum_Busqueda'])) {
  $pageNum_Busqueda = $_GET['pageNum_Busqueda'];
}
$startRow_Busqueda = $pageNum_Busqueda * $maxRows_Busqueda;

$colname_Busqueda = "-1";
if (isset($_POST['dni'])) {
  $colname_Busqueda = $_POST['dni'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Busqueda = sprintf("SELECT * FROM padron WHERE DocumentoNro = %s", GetSQLValueString($colname_Busqueda, "text"));
$query_limit_Busqueda = sprintf("%s LIMIT %d, %d", $query_Busqueda, $startRow_Busqueda, $maxRows_Busqueda);
$Busqueda = mysql_query($query_limit_Busqueda, $antecedentes) or die(mysql_error());
$row_Busqueda = mysql_fetch_assoc($Busqueda);

if (isset($_GET['totalRows_Busqueda'])) {
  $totalRows_Busqueda = $_GET['totalRows_Busqueda'];
} else {
  $all_Busqueda = mysql_query($query_Busqueda);
  $totalRows_Busqueda = mysql_num_rows($all_Busqueda);
}
$totalPages_Busqueda = ceil($totalRows_Busqueda/$maxRows_Busqueda)-1;

$IdProntuarioTipo_Padron = "0";
if (isset($row_Busqueda['IdProntuarioTipo'])) {
  $IdProntuarioTipo_Padron = $row_Busqueda['IdProntuarioTipo'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Padron = sprintf("SELECT * FROM prontuariostipos WHERE prontuariostipos.IdProntuarioTipo = %s", GetSQLValueString($IdProntuarioTipo_Padron, "int"),GetSQLValueString($IdProntuarioTipo_Padron, "int"));
$Padron = mysql_query($query_Padron, $antecedentes) or die(mysql_error());
$row_Padron = mysql_fetch_assoc($Padron);
$totalRows_Padron = mysql_num_rows($Padron);

$valordni_Solicitud = "0";
if (isset($row_Busqueda['DocumentoNro'])) {
  $valordni_Solicitud = $row_Busqueda['DocumentoNro'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitud = sprintf("SELECT * FROM solicitud WHERE solicitud.dni = %s AND solicitud.estado_solicitud != 'Anulado' AND solicitud.estado_solicitud != 'Completado'", GetSQLValueString($valordni_Solicitud, "int"));
$Solicitud = mysql_query($query_Solicitud, $antecedentes) or die(mysql_error());
$row_Solicitud = mysql_fetch_assoc($Solicitud);
$totalRows_Solicitud = mysql_num_rows($Solicitud);

$queryString_Busqueda = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Busqueda") == false && 
        stristr($param, "totalRows_Busqueda") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Busqueda = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Busqueda = sprintf("&totalRows_Busqueda=%d%s", $totalRows_Busqueda, $queryString_Busqueda);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
          <div class="h_title">Verificación de Solicitante</div>
          <div class="Table">
            <p>
              <?php if (isset($_POST["dni"])) { // Show if recordset not empty ?>
            </p>
            <p>&nbsp;</p>
            <p>&nbsp; </p>
            <table border="1" align="center">
              <tr>
      <td align="center">Documento</td>
      <td align="center">Tipo</td>
      <td align="center">Prontuario</td>
      <td align="center">Apellido</td>
      <td align="center">Nombres</td>
      <td align="center">Acción</td>
    </tr>
    <?php do { ?>
      <tr>
        <td align="center"><?php echo $row_Busqueda['DocumentoNro']; ?>&nbsp; </a></td>
        <td align="center"><?php echo $row_Padron['ProntuarioTipo']; ?></td>
        <td align="center"><?php echo $row_Busqueda['ProntuarioNro']; ?>&nbsp; </a></td>
        <td align="center"><?php echo $row_Busqueda['Apellido']; ?>&nbsp; </td>
        <td align="center"><?php echo $row_Busqueda['Nombre']; ?>&nbsp; </td>
        <td align="center">
                  <form id="form1" name="form1" method="post" action="solicitud.php">
              <input name="documento" type="hidden" id="documento" value="<?php echo $row_Busqueda['DocumentoNro']; ?>" />
              <input name="prontuario" type="hidden" id="prontuario" value="<?php echo $row_Busqueda['ProntuarioNro']; ?>" />
              <input name="apellido" type="hidden" id="apellido" value="<?php echo $row_Busqueda['Apellido']; ?>" />
              <input name="nombres" type="hidden" id="nombres" value="<?php echo $row_Busqueda['Nombre']; ?>" />
              <input type="hidden" name="documento" id="documento" value="<?php echo $_POST["dni"]; ?>" />
              <input name="button" type="submit" autofocus="autofocus" id="button" value="Generar Solicitud" />
          </form>

          <?php 
		  /* Cambie esta parte por el formulario q esta arriba para que las variables pasen por post y no por get
		  <a href="solicitud.php?documento=<?php echo $row_Busqueda['DocumentoNro']; ?>&prontuario=<?php echo $row_Busqueda['ProntuarioNro']; ?>&apellido=<?php echo $row_Busqueda['Apellido']; ?>&nombres=<?php echo $row_Busqueda['Nombre']; ?>"> <?php echo 'Continuar'; ?> */?>

          <?php 
		  /*
		  if ($row_Busqueda['Antecedente'] == "Si"){ ?>
          <div id="dialog-message" title="ATENCIÓN...!!!">
            <p> <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span> El solicitante figura en la base de datos con antecedentes personales </p>
            <p> Debe dirigirse a la <b>División de Antecedentes Personales</b> para iniciar trámite. </p>
          </div>
          <p>
		  
            <?php } */?>
          </p>
          </td>
      </tr>
      <?php } while ($row_Busqueda = mysql_fetch_assoc($Busqueda)); ?>
</table>
<?php
// Si no existe en la base de datos, hacer un insert a la tabla padron.
if ($totalRows_Busqueda == 0){ 
	  //Paso los valores de las cajas de texto y combo al insert
	  $documento=$_POST["dni"];

	  mysql_select_db($database_antecedentes, $antecedentes);
	  mysql_query("INSERT INTO padron (DocumentoNro) VALUES ('$documento') ", $antecedentes) or die("Error en consulta <br>MySQL 
dice: 
".mysql_error());
?>
						  <p>
								<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
								El solicitante no se encuentra en la base de datos. Proceda a generar la solicitud....</p> 
							<p>
							Hacer clic en &quot;Continuar&quot;. </p>
			        <p>
					          <?php  } ?>
					          <?php }?>
            </p>
            
            <?php if ($totalRows_Solicitud > 0) { // Show if recordset not empty ?>
  <h3><br />
    <span class="ui-state-error">Existen Solicitudes pendientes para esta Persona.</span></h3>
<h3><span class="ui-state-error">Debe completar el proceso de impresi&oacute;n o Anular la Solicitud.</span></h3>
                      

            <table border="1">
              <tr>
                <td>Apellido</td>
                <td>Nombre</td>
                <td>Estado de Solicitud</td>
                <td>Fecha Solicitada</td>
                <td>Hora Solicitada</td>
                <td>Anular</td>
              </tr>
              <?php do { ?>
                <tr>
                  <td><?php echo $row_Solicitud['apellido']; ?></td>
                  <td><?php echo $row_Solicitud['nombre']; ?></td>
                  <td><?php echo $row_Solicitud['estado_solicitud']; ?></td>
                  <td><?php echo $row_Solicitud['fecha_solicitada']; ?></td>
                  <td><?php echo $row_Solicitud['hora_solicitada']; ?></td>
                  <td>
			  <form action="<?php echo $editFormAction; ?>" method="POST" name="anularsolicitud">
              <input name="id_solicitud" type="hidden" value="<?php echo $row_Solicitud['id_solicitud']; ?>" />
              <input name="estado_solicitud" type="hidden" value="Anulado" />
              <input name="dni" type="hidden" value="<?php echo $_POST['dni']; ?>" />
              <input type="hidden" name="MM_update" value="anularsolicitud" />
              <input type="submit" onclick="return confirmar('Esta seguro que desea anular la solicitud?')" value="Anular Solicitud" />
              </form>
                 </td>
                </tr>
                <?php } while ($row_Solicitud = mysql_fetch_assoc($Solicitud)); ?>

            </table>
                                                    <?php } // Show if recordset not empty ?>

          </div>
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
mysql_free_result($Busqueda);

mysql_free_result($Padron);

mysql_free_result($Solicitud);
?>