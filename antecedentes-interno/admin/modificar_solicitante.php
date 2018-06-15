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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE padron SET Apellido=%s, Nombre=%s, Clase=%s, DocumentoNro=%s, IdPais=%s, Genero=%s, EstadoCivil=%s, Profesion=%s, Direccion=%s, IdProntuarioTipo=%s, ProntuarioNro=%s, Observaciones=%s WHERE IdPadron=%s",
                       GetSQLValueString($_POST['Apellido'], "text"),
                       GetSQLValueString($_POST['Nombre'], "text"),
                       GetSQLValueString($_POST['Clase'], "int"),
                       GetSQLValueString($_POST['DocumentoNro'], "int"),
                       GetSQLValueString($_POST['IdPais'], "int"),
                       GetSQLValueString($_POST['Genero'], "text"),
                       GetSQLValueString($_POST['EstadoCivil'], "text"),
                       GetSQLValueString($_POST['Profesion'], "text"),
                       GetSQLValueString($_POST['Direccion'], "text"),
                       GetSQLValueString($_POST['tipo_prontuario'], "int"),
                       GetSQLValueString($_POST['ProntuarioNro'], "int"),
                       GetSQLValueString($_POST['Observaciones'], "text"),
                       GetSQLValueString($_POST['IdPadron'], "int"));

  mysql_select_db($database_antecedentes, $antecedentes);
  $Result1 = mysql_query($updateSQL, $antecedentes) or die(mysql_error());

  $updateGoTo = "correcto.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$id_Editar_Solicitante = "0";
if (isset($_GET['id'])) {
  $id_Editar_Solicitante = $_GET['id'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Editar_Solicitante = sprintf("SELECT * FROM padron WHERE padron.IdPadron = %s", GetSQLValueString($id_Editar_Solicitante, "int"));
$Editar_Solicitante = mysql_query($query_Editar_Solicitante, $antecedentes) or die(mysql_error());
$row_Editar_Solicitante = mysql_fetch_assoc($Editar_Solicitante);
$totalRows_Editar_Solicitante = mysql_num_rows($Editar_Solicitante);

mysql_select_db($database_antecedentes, $antecedentes);
$query_Nacionalidad = "SELECT * FROM paises";
$Nacionalidad = mysql_query($query_Nacionalidad, $antecedentes) or die(mysql_error());
$row_Nacionalidad = mysql_fetch_assoc($Nacionalidad);
$totalRows_Nacionalidad = mysql_num_rows($Nacionalidad);

mysql_select_db($database_antecedentes, $antecedentes);
$query_TipoProntuario = "SELECT * FROM prontuariostipos";
$TipoProntuario = mysql_query($query_TipoProntuario, $antecedentes) or die(mysql_error());
$row_TipoProntuario = mysql_fetch_assoc($TipoProntuario);
$totalRows_TipoProntuario = mysql_num_rows($TipoProntuario);
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
 <script language=""="JavaScript">
    function conMayusculas(field) {
            field.value = field.value.toUpperCase()
}
</script>

 <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
 <script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
 <link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
 <link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
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
		    <h3>Editar Datos del Solicitante	      </h3>
	      <p>&nbsp;</p>
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table width="643" align="center">
              <tr valign="baseline">
                <td width="98" align="right" nowrap="nowrap">Apellido:</td>
                <td width="288"><span id="sprytextfield2">
                  <input type="text" name="Apellido" value="<?php echo htmlentities($row_Editar_Solicitante['Apellido'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
                <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
                <td width="58">Nombre:</td>
                <td width="186"><span id="sprytextfield3">
                  <input type="text" name="Nombre" value="<?php echo htmlentities($row_Editar_Solicitante['Nombre'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
                <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Clase:</td>
                <td><span id="sprytextfield4">
                <input type="text" name="Clase" value="<?php echo htmlentities($row_Editar_Solicitante['Clase'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" />
                <br /><span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">DocumentoNro:</td>
                <td><span id="sprytextfield1">
                <input type="text" name="DocumentoNro" value="<?php echo htmlentities($row_Editar_Solicitante['DocumentoNro'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" />
                <br /><span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
                <td>Nacionalidad:</td>
                <td><select name="IdPais" id="IdPais">
                  <?php
do {  
?>
                  <option value="<?php echo $row_Nacionalidad['IdPais']?>"<?php if (!(strcmp($row_Nacionalidad['IdPais'], $row_Editar_Solicitante['IdPais']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Nacionalidad['Pais']?></option>
                  <?php
} while ($row_Nacionalidad = mysql_fetch_assoc($Nacionalidad));
  $rows = mysql_num_rows($Nacionalidad);
  if($rows > 0) {
      mysql_data_seek($Nacionalidad, 0);
	  $row_Nacionalidad = mysql_fetch_assoc($Nacionalidad);
  }
?>
                </select></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Genero:</td>
                <td><span id="spryselect1">
                  <label for="Genero"></label>
                  <select name="Genero" id="Genero">
                    <option value="M" <?php if (!(strcmp("M", $row_Editar_Solicitante['Genero']))) {echo "selected=\"selected\"";} ?>>MASCULINO</option>
                    <option value="F" <?php if (!(strcmp("F", $row_Editar_Solicitante['Genero']))) {echo "selected=\"selected\"";} ?>>FEMENINO</option>
                </select>
                <span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
                <td>EstadoCivil:</td>
                <td><select name="EstadoCivil" id="EstadoCivil">
  <option value="" <?php if (!(strcmp("", $row_Editar_Solicitante['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>SELECCIONE UNA OPCI&Oacute;N</option>
  <option value="SOLTERO/A" <?php if (!(strcmp("S", $row_Editar_Solicitante['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>SOLTERO/A</option>
  <option value="CASADO/A" <?php if (!(strcmp("C", $row_Editar_Solicitante['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>CASADO/A</option>
  <option value="DIVORCIADO/A" <?php if (!(strcmp("D", $row_Editar_Solicitante['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>DIVORCIADO/A</option>
  <option value="VIUDO/A" <?php if (!(strcmp("V", $row_Editar_Solicitante['EstadoCivil']))) {echo "selected=\"selected\"";} ?>>VIUDO/A</option>
</select></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Profesion:</td>
                <td><input type="text" name="Profesion" value="<?php echo htmlentities($row_Editar_Solicitante['Profesion'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Direccion:</td>
                <td><input type="text" name="Direccion" value="<?php echo htmlentities($row_Editar_Solicitante['Direccion'], ENT_COMPAT, 'iso-8859-1'); ?>" size="40" style="text-transform:uppercase;" onChange="conMayusculas (this)"/></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">IdProntuarioTipo:</td>
                <td><select name="tipo_prontuario" id="select3">
                  <option value="" <?php if (!(strcmp("", $row_Editar_Solicitante['IdProntuarioTipo']))) {echo "selected=\"selected\"";} ?>>SELECCIONE</option>
                  <?php
do {  
?>
<option value="<?php echo $row_TipoProntuario['IdProntuarioTipo']?>"<?php if (!(strcmp($row_TipoProntuario['IdProntuarioTipo'], $row_Editar_Solicitante['IdProntuarioTipo']))) {echo "selected=\"selected\"";} ?>><?php echo $row_TipoProntuario['ProntuarioTipo']?></option>
                  <?php
} while ($row_TipoProntuario = mysql_fetch_assoc($TipoProntuario));
  $rows = mysql_num_rows($TipoProntuario);
  if($rows > 0) {
      mysql_data_seek($TipoProntuario, 0);
	  $row_TipoProntuario = mysql_fetch_assoc($TipoProntuario);
  }
?>
                </select></td>
                <td>ProntuarioNro:</td>
                <td><input type="text" name="ProntuarioNro" value="<?php echo htmlentities($row_Editar_Solicitante['ProntuarioNro'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right" valign="top">Observaciones:</td>
                <td><textarea name="Observaciones" cols="50" rows="5" style="text-transform:uppercase;" onChange="conMayusculas (this)"><?php echo htmlentities($row_Editar_Solicitante['Observaciones'], ENT_COMPAT, 'iso-8859-1'); ?></textarea></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Actualizar registro" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            <input type="hidden" name="MM_update" value="form1" />
            <input type="hidden" name="IdPadron" value="<?php echo $row_Editar_Solicitante['IdPadron']; ?>" />
          </form>
          <p>&nbsp;</p>
<p>&nbsp;</p>
		  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {validateOn:["change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "integer", {validateOn:["change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
          </script>
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
mysql_free_result($Editar_Solicitante);

mysql_free_result($Nacionalidad);

mysql_free_result($TipoProntuario);
?>
