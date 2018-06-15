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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO padron (Apellido, Nombre, Clase, DocumentoNro, IdPais, Genero, EstadoCivil, Profesion, Direccion, IdProntuarioTipo, ProntuarioNro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Apellido'], "text"),
                       GetSQLValueString($_POST['Nombre'], "text"),
                       GetSQLValueString($_POST['Clase'], "int"),
                       GetSQLValueString($_POST['DocumentoNro'], "int"),
                       GetSQLValueString($_POST['IdPais'], "int"),
                       GetSQLValueString($_POST['Genero'], "text"),
                       GetSQLValueString($_POST['EstadoCivil'], "text"),
                       GetSQLValueString($_POST['Profesion'], "text"),
                       GetSQLValueString($_POST['Direccion'], "text"),
                       GetSQLValueString($_POST['IdProntuarioTipo'], "int"),
                       GetSQLValueString($_POST['ProntuarioNro'], "int"));

  mysql_select_db($database_antecedentes, $antecedentes);
  $Result1 = mysql_query($insertSQL, $antecedentes) or die(mysql_error());
  $insertGoTo = "enviado.php?dni=" . $_POST["DocumentoNro"];
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE solicitud SET apellido=%s, nombre=%s WHERE id_solicitud=%s",
                       GetSQLValueString($_POST['Apellido'], "text"),
                       GetSQLValueString($_POST['Nombre'], "text"),
                       GetSQLValueString($_GET['id_solicitud'], "int"));

  mysql_select_db($database_antecedentes, $antecedentes);
  $Result1 = mysql_query($updateSQL, $antecedentes) or die(mysql_error());
}

mysql_select_db($database_antecedentes, $antecedentes);
$query_TipoProntuario = "SELECT * FROM prontuariostipos";
$TipoProntuario = mysql_query($query_TipoProntuario, $antecedentes) or die(mysql_error());
$row_TipoProntuario = mysql_fetch_assoc($TipoProntuario);
$totalRows_TipoProntuario = mysql_num_rows($TipoProntuario);

mysql_select_db($database_antecedentes, $antecedentes);
$query_IdPais = "SELECT * FROM paises";
$IdPais = mysql_query($query_IdPais, $antecedentes) or die(mysql_error());
$row_IdPais = mysql_fetch_assoc($IdPais);
$totalRows_IdPais = mysql_num_rows($IdPais);

$valor_Padron = "0";
if (isset($_POST['txtbuscar'])) {
  $valor_Padron = $_POST['txtbuscar'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Padron = sprintf("SELECT padron.DocumentoNro FROM padron WHERE padron.DocumentoNro = %s", GetSQLValueString($valor_Padron, "int"));
$Padron = mysql_query($query_Padron, $antecedentes) or die(mysql_error());
$row_Padron = mysql_fetch_assoc($Padron);
$totalRows_Padron = mysql_num_rows($Padron);
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
          <!-- Script para transformar inputs en mayuscula -->
<script language=""="JavaScript">
    function conMayusculas(field) {
            field.value = field.value.toUpperCase()
 }
</script>
          <!-- aqui termina -->


           
	      <h1>Alta de Solicitante</h1>
	      <p>&nbsp;</p>
	      <form id="txtbuscar" name="txtbuscar" method="post" action="">
            <h3>Ingrese DNI:
              <label for="txtbuscar"></label>
              <span id="sprytextfield8">
              <input name="txtbuscar" type="text" autofocus="autofocus" id="txtbuscar" />
              <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span>
              <input type="submit" value="Buscar" />
            </h3>
          </form>
          <?php if (isset($_POST['txtbuscar']) && $totalRows_Padron > 0) { // Show if recordset not empty ?>
            <h3 class="ui-state-error">Ya existe una persona con el Nro. de DNI Ingresado.</h3>
            <h3>
              <?php } // Show if recordset not empty ?>
              <?php if (isset($_POST['txtbuscar']) && $totalRows_Padron == 0) { // Show if recordset empty ?>
            </h3>
          <form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">DNI:</td>
                <td><span id="sprytextfield1">
                <input name="DocumentoNro" type="text" value="<?php echo htmlentities($_POST['txtbuscar'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" readonly="readonly" />
                <br /><span class="textfieldRequiredMsg">Se necesita un valor.</span><br /><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Tipo de Prontuario:</td>
                <td><span id="spryselect3">
                  <label for="IdProntuarioTipo"></label>
<select name="IdProntuarioTipo" id="IdProntuarioTipo">
                    <option value="">SELECCIONE</option>
                    <?php
do {  
?>
                    <option value="<?php echo $row_TipoProntuario['IdProntuarioTipo']?>"><?php echo $row_TipoProntuario['ProntuarioTipo']?></option>
                    <?php
} while ($row_TipoProntuario = mysql_fetch_assoc($TipoProntuario));
  $rows = mysql_num_rows($TipoProntuario);
  if($rows > 0) {
      mysql_data_seek($TipoProntuario, 0);
	  $row_TipoProntuario = mysql_fetch_assoc($TipoProntuario);
  }
?>
                </select>
                <br /><span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
                <td>Nro. Prontuario:</td>
                <td><span id="sprytextfield5">
                <label for="ProntuarioNro"></label>
                <input type="text" name="ProntuarioNro" id="ProntuarioNro" />
                <br /><span class="textfieldRequiredMsg">Se necesita un valor.</span>
                <br /><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Apellido:</td>
                <td><span id="sprytextfield2">
                  <input type="text" name="Apellido" value="" size="32" style="text-transform:uppercase;" onchange="conMayusculas (this)"/>
                <br /><span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
                <td>Nombre:</td>
                <td><span id="sprytextfield3">
                  <input type="text" name="Nombre" value="" size="32" style="text-transform:uppercase;" onchange="conMayusculas (this)"/>
                <br /><span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Direcci&oacute;n:</td>
                <td><span id="sprytextfield4">
                  <input name="Direccion" type="text" style="text-transform:uppercase;" onchange="conMayusculas (this)" value="" size="40" maxlength="70"/>
                <br /><span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
                <td></td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Profesi&oacute;n:</td>
                <td><span id="sprytextfield6">
                  <input type="text" name="Profesion" value="" size="32" style="text-transform:uppercase;" onchange="conMayusculas (this)"/>
                <br /><span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
                <td>Estado Civil:</td>
                <td><span id="spryselect1">
                  <label for="EstadoCivil"></label>
<select name="EstadoCivil">
                    <option>SELECCIONE UNA OPCI&Oacute;N</option>
                    <option value="SOLTERO/A">SOLTERO/A</option>
                    <option value="CASADO/A">CASADO/A</option>
                    <option value="DIVORCIADO/A">DIVORCIADO/A</option>
                    <option value="VIUDO/A">VIUDO/A</option>
                  </select>
                <br /><span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Clase:</td>
                <td><span id="sprytextfield7">
                <label for="Clase"></label>
                <input type="text" name="Clase" id="Clase" />
                <br />
                <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Nacionalidad:</td>
                <td><span id="spryselect4">
                  <label for="IdPais2"></label>
<select name="IdPais" id="IdPais">
                    <option value="">SELECCIONE</option>
                    <?php
do {  
?>
                    <option value="<?php echo $row_IdPais['IdPais']?>"><?php echo $row_IdPais['Pais']?></option>
                    <?php
} while ($row_IdPais = mysql_fetch_assoc($IdPais));
  $rows = mysql_num_rows($IdPais);
  if($rows > 0) {
      mysql_data_seek($IdPais, 0);
	  $row_IdPais = mysql_fetch_assoc($IdPais);
  }
?>
                  </select>
                <br /><span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
                <td></td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Genero:</td>
                <td><span id="spryselect2">
                  <label for="Genero"></label>
<select name="Genero" id="Genero">
                    <option>SELECCIONE UNA OPCI&Oacute;N</option>
                    <option value="M">MASCULINO</option>
                    <option value="F">FEMENINO</option>
                  </select>
                <br /><span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><input type="submit" value="Agregar Solicitante" /></td>
              </tr>
            </table>
            <input type="hidden" name="MM_insert" value="form1" />
            <input type="hidden" name="MM_update" value="form1" />
          </form>
            <?php } // Show if recordset empty ?>
          <p>&nbsp;</p>
<p>&nbsp;</p>
		  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {validateOn:["change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");
          </script>
		  
<script src="../../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="../../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "integer", {validateOn:["change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "integer", {validateOn:["change"]});
var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4");
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2");
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "integer", {validateOn:["change"]});
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
mysql_free_result($TipoProntuario);

mysql_free_result($IdPais);

mysql_free_result($Padron);
?>
