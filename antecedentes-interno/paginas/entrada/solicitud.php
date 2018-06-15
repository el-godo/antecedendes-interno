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

$colname_Padron = "-1";
if (isset($_POST['documento'])) {
  $colname_Padron = $_POST['documento'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Padron = sprintf("SELECT * FROM padron WHERE DocumentoNro = %s", GetSQLValueString($colname_Padron, "int"));
$Padron = mysql_query($query_Padron, $antecedentes) or die(mysql_error());
$row_Padron = mysql_fetch_assoc($Padron);
$totalRows_Padron = mysql_num_rows($Padron);

mysql_select_db($database_antecedentes, $antecedentes);
$query_TipoProntuario = "SELECT * FROM prontuariostipos";
$TipoProntuario = mysql_query($query_TipoProntuario, $antecedentes) or die(mysql_error());
$row_TipoProntuario = mysql_fetch_assoc($TipoProntuario);
$totalRows_TipoProntuario = mysql_num_rows($TipoProntuario);

mysql_select_db($database_antecedentes, $antecedentes);
$query_NroCertificado = "SELECT * FROM nrocertificado WHERE nrocertificado.id = 1";
$NroCertificado = mysql_query($query_NroCertificado, $antecedentes) or die(mysql_error());
$row_NroCertificado = mysql_fetch_assoc($NroCertificado);
$totalRows_NroCertificado = mysql_num_rows($NroCertificado);


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
          <head>
          <script language=""="JavaScript">
    function conMayusculas(field) {
            field.value = field.value.toUpperCase()
}
</script>

          <div id=”stylized” class=”myform”>
<form id=”form” name=”form” action="../../consultas_sql/altasolicitud.php" method="POST">
<h1>Formulario de Solicitud de Certificados de Antecedentes</h1>
<table width="100%" border="0">
  <tr>
    <td width="167"><label>DNI
    
  </label>
      <span id="sprytextfield2">
      <input name="dni" type="text" id=&rdquo;name&rdquo;2 value="<?php echo $_POST['documento'] ?>" maxlength="20" readonly="readonly"/>
      <span class="textfieldRequiredMsg"><br />Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
    <td width="208"><label>Tipo de Prontuario</label>
      <label for="tipo_prontuario"></label>
      <select name="tipo_prontuario" id="tipo_prontuario">
        <option value="" <?php if (!(strcmp("", $row_Padron['IdProntuarioTipo']))) {echo "selected=\"selected\"";} ?>>SELECCIONE</option>
        <?php
do {  
?>
        <option value="<?php echo $row_TipoProntuario['IdProntuarioTipo']?>"<?php if (!(strcmp($row_TipoProntuario['IdProntuarioTipo'], $row_Padron['IdProntuarioTipo']))) {echo "selected=\"selected\"";} ?>><?php echo $row_TipoProntuario['ProntuarioTipo']?></option>
        <?php
} while ($row_TipoProntuario = mysql_fetch_assoc($TipoProntuario));
  $rows = mysql_num_rows($TipoProntuario);
  if($rows > 0) {
      mysql_data_seek($TipoProntuario, 0);
	  $row_TipoProntuario = mysql_fetch_assoc($TipoProntuario);
  }
?>
      </select>
(opcional)</td>
<?php
if (isset($row_Padron['ProntuarioNro'])){
	$nro_prontuario = $row_Padron['ProntuarioNro'];
}
else {
		$nro_prontuario = 0;
}
?>
   <td width="261"><label>Prontuario Nro (Nro AG)</label>
     <span id="sprytextfield5">
     <input type="text" name="prontuario" id=name value="<?php echo $nro_prontuario; ?>" style="text-transform:uppercase;" onchange="conMayusculas (this)"/>
     <span class="textfieldInvalidFormatMsg">Formato no válido.</span></span> (opcional)</td>
  </tr>
  <tr>
  <td width="167"><label>Apellido
    
  </label>
  <?php echo $row_Padron['Apellido']; ?>
  </td>
    <td width="208"><label>Nombres</label>
    <?php echo $row_Padron['Nombre']; ?>
    </td>
   <td width="261">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

  
</p>
<p><?php /* Obtener Ip del cliente  */?>
  <input name="ip_cliente" type="hidden" id="ip_cliente" value="<?php echo $ipcliente ?>" />
</p>

</p>
<p>
  <input name="id_certificado" type="hidden" id="id" value="<?php echo $id ?>" />
  <input name="nro_certificado" type="hidden" id="nro_certificado" value="<?php echo $nro_certificado ?>" />
  <input name="anio" type="hidden" id="anio" value="<?php echo $anio ?>" />
  <input name="nombres" type="hidden" value="<?php echo $row_Padron['Nombre']; ?>" />
  <input name="apellido" type="hidden" value="<?php echo $row_Padron['Apellido']; ?>" />
</p>
<p>&nbsp;</p>
<p><button type=”submit” autofocus="autofocus">
<div align="right">Enviar</div>
</button></p>
<div class=”spacer”></div>

</form>

</div>
          <script type="text/javascript">
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer", {validateOn:["change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "integer", {validateOn:["change"]});
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
mysql_free_result($Padron);

mysql_free_result($TipoProntuario);

mysql_free_result($NroCertificado);
?>
