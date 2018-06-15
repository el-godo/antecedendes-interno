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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_Padron = "0";
if (isset($_POST['DocumentoNro'])) {
  $colname_Padron = $_POST['DocumentoNro'];
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
$query_Nacionalidad = "SELECT * FROM paises";
$Nacionalidad = mysql_query($query_Nacionalidad, $antecedentes) or die(mysql_error());
$row_Nacionalidad = mysql_fetch_assoc($Nacionalidad);
$totalRows_Nacionalidad = mysql_num_rows($Nacionalidad);

$valor_Intervino = "0";
if (isset($_SESSION['MM_Username'])) {
  $valor_Intervino = $_SESSION['MM_Username'];;
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Intervino = sprintf("SELECT usuario.iniciales FROM usuario WHERE usuario.usuario = %s", GetSQLValueString($valor_Intervino, "text"));
$Intervino = mysql_query($query_Intervino, $antecedentes) or die(mysql_error());
$row_Intervino = mysql_fetch_assoc($Intervino);
$totalRows_Intervino = mysql_num_rows($Intervino);
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
          <!-- Script para que funcione las mayusculas --><head>
<script language=""="JavaScript">
    function conMayusculas(field) {
            field.value = field.value.toUpperCase()
 }
</script>
          <!-- Termina Script -->
          <?php
		  echo $_POST['id_historial'];
		  ?>
          <h1>Procesar Solicitud Pendiente</h1>
		   
		    <h3>Complete el formulario con los datos correspondientes.</h3>
            <?php if ($totalRows_Padron == 0) { // Show if recordset empty ?>
            <h3 class="ui-state-error">La persona no existe en el Sistema. DNI: <?php echo $_POST['DocumentoNro'] ;?></h3>
            <form action="alta_solicitante.php" method="post">
            <input name="txtbuscar" type="hidden" value="<?php echo $_POST['DocumentoNro'] ;?>" />
            <input type="submit" name="button" id="button" value="Alta de Solicitante" />
            </form>
            <?php } // Show if recordset empty ?>
  <p>&nbsp;</p>
  <?php if ($totalRows_Padron > 0) { // Show if recordset not empty ?>
  <form action="imprimir_certificado.php" method="POST" name="form1" id="form1">
    <table width="100%" border="0" align="center">
      <tr>
        <td width="123"><div align="center">DNI: </div></td>
        <td width="230" align="left"><p>
          <label for="dni">
              <input name="dni" type="text" id="dni" value="<?php echo $_POST['DocumentoNro']; ?>" readonly="readonly" />
            </label>
      </p></td>
        <td><div align="center">Pasaporte: </div></td>
        <td><label for="pasaporte"></label>
          <input name="pasaporte" type="text" id="pasaporte" style="text-transform:uppercase;" onChange="conMayusculas (this)" value="<?php echo $row_Padron['Pasaporte']; ?>"/></td>
      </tr>
      <tr>
        <td><div align="center">Tipo de Prontuario:</div></td>
        <td align="left"><p><span id="spryselect2">
          <label for="tipo_prontuario2"></label>
<select name="tipo_prontuario" id="select3">
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
          </select>          <br /><span class="selectRequiredMsg">Seleccione un elemento.</span></span></p></td>
        <td><div align="right">Prontuario A.G:</div></td>
        <td><span id="sprytextfield6">
        <input name="prontuario" type="text" id="prontuario"style="text-transform:uppercase;" onchange="conMayusculas (this)" value="<?php echo htmlentities($row_Padron['ProntuarioNro'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" />
        <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
      </tr>
      <tr>
        <td><div align="right">Apellido:</div></td>
        <td><span id="sprytextfield1">
          <input type="text" name="apellido" value="<?php echo htmlentities($row_Padron['Apellido'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32"  style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
          <br />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
        <td><div align="right">Nombre:</div></td>
        <td><span id="sprytextfield2">
          <input type="text" name="nombre" value="<?php echo htmlentities($row_Padron['Nombre'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
          <br />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
      </tr>
      <tr>
        <td><div align="right">Domicilio:</div></td>
        <td><span id="sprytextfield3">
          <input name="domicilio" type="text" style="text-transform:uppercase;" onChange="conMayusculas (this)" value="<?php echo htmlentities($row_Padron['Direccion'], ENT_COMPAT, 'iso-8859-1'); ?>" size="40" maxlength="70"/>
          <br />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><div align="right">Profesión:</div></td>
        <td><span id="sprytextfield8">
          <input name="profesion" type="text" id="profesion" style="text-transform:uppercase;" onChange="conMayusculas (this)" value="<?php echo htmlentities($row_Padron['Profesion'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" maxlength="67" />
          <br />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
        <td><div align="right">Estado Civil:</div></td>
        <td align="left"><span id="spryselect1">
          <label for="EstadoCivil"></label>
<select name="EstadoCivil" id="EstadoCivil">
  <option value="" <?php if (!(strcmp("", $row_Padron['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>SELECCIONE UNA OPCI&Oacute;N</option>
  <option value="SOLTERO/A" <?php if (!(strcmp("S", $row_Padron['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>SOLTERO/A</option>
  <option value="CASADO/A" <?php if (!(strcmp("C", $row_Padron['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>CASADO/A</option>
  <option value="DIVORCIADO/A" <?php if (!(strcmp("D", $row_Padron['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>DIVORCIADO/A</option>
  <option value="VIUDO/A" <?php if (!(strcmp("V", $row_Padron['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>VIUDO/A</option>
</select>
          <br /><span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
      </tr>
      <tr>
        <td><div align="right">Clase</div></td>
        <td><span id="sprytextfield5">
          <input type="text" name="clase" value="<?php echo htmlentities($row_Padron['Clase'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" style="text-transform:uppercase;" />
          <br />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
        <td>&nbsp;</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td><div align="right">Nacionalidad:</div></td>
        <td><span id="spryselect3">
          <label for="idpais"></label>
<select name="idpais" id="idpais">
            <option value="" <?php if (!(strcmp("", $row_Padron['IdPais']))) {echo "selected=\"selected\"";} ?>>SELECCIONE</option>
            <?php
do {  
?>
            <option value="<?php echo $row_Nacionalidad['IdPais']?>"<?php if (!(strcmp($row_Nacionalidad['IdPais'], $row_Padron['IdPais']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Nacionalidad['Pais']?></option>
            <?php
} while ($row_Nacionalidad = mysql_fetch_assoc($Nacionalidad));
  $rows = mysql_num_rows($Nacionalidad);
  if($rows > 0) {
      mysql_data_seek($Nacionalidad, 0);
	  $row_Nacionalidad = mysql_fetch_assoc($Nacionalidad);
  }
?>
        </select>
          <br /><span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><div align="right">Solicitado por:</div></td>
        <td><span id="sprytextfield7">
          <input type="text" name="solicitado_por" value="ANTE LAS AUTORIDADES QUE LO REQUIERAN." size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
          <br />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
        <td><div align="right">Intervino:</div></td>
        <td align="left"><span id="sprytextfield4">
          <input name="intervino" type="text" id="intervino" style="text-transform:uppercase;" onchange="conMayusculas (this)" value="<?php echo $row_Intervino['iniciales']; ?>" size="4" maxlength="4" readonly="readonly"/>
          <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>Capital 
          <input name="capitalinterior" type="radio" id="radio3" value="CAPITAL" checked="checked" /></td>
        <td align="left">Interior 
          <input type="radio" name="capitalinterior" id="radio4" value="INTERIOR" />
          <label for="radio4"></label></td>
      </tr>
      <tr>
        <td><div align="right">Tiene Antecedentes:</div></td>
        <td>&nbsp;</td>
        <td align="left">No 
          <input name="ant" type="radio" id="radio2" value="No" checked="checked" />
          <label for="ant"></label></td>
        <td align="left">Si 
          <input name="ant" type="radio" id="radio" value="Si" />
          <label for="ant"></label></td>
      </tr>
      <tr>
        <td><div id="observaciones2" align="right">Antecedentes:</div></td>
        <td colspan="2"><label for="observaciones"></label>
          <span id="sprytextarea1">
          
          <textarea name="observaciones" id="observaciones1" cols="88" rows="15" style="text-transform:uppercasen;" onChange="conMayusculas (this)"><?php echo $row_Padron['Observaciones']; ?></textarea>
<br />
          </span><br /></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><input name="procconant" type="submit" autofocus="autofocus" id="procesar" value="Procesar Solicitud" /></td>
      </tr>
    </table>
    <p>
      <input type="hidden" name="id_solicitud" value="<?php echo $_POST['id_solicitud']; ?>" />
      <input type="hidden" name="id_historial" value="<?php echo $_POST['id_historial']; ?>" />
      <input type="hidden" name="fecha" value="<?php echo $row_Padron['fecha_solicitada']; ?>" />
      <input type="hidden" name="genero" value="<?php echo $row_Padron['Genero']; ?>" />
      <input type="hidden" name="estado_solicitud" value="Completado" />
      <input type="hidden" name="ip_confeccion" value="<?php echo $ipcliente; ?>" />
    </p>
    <input type="hidden" name="MM_update" value="form1" />
  </form>
  <?php } // Show if recordset not empty ?>
<p></p>
	      <p>&nbsp;</p>
          
          <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7");
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {isRequired:false});
          </script>
		  
<script src="../../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="../../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="../../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "integer", {validateOn:["change"]});
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

mysql_free_result($Nacionalidad);

mysql_free_result($Intervino);
?>
