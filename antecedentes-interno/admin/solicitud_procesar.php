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
  $updateSQL = sprintf("UPDATE padron SET ProntuarioNro=%s, Apellido=%s, Nombre=%s WHERE DocumentoNro=%s",
                       GetSQLValueString($_POST['prontuario'], "text"),
                       GetSQLValueString($_POST['apellido'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['dni'], "text"));

  mysql_select_db($database_antecedentes, $antecedentes);
  $Result1 = mysql_query($updateSQL, $antecedentes) or die(mysql_error());

  $updateGoTo = "correcto.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE solicitud SET apellido=%s, nombre=%s, domicilio=%s, prontuario=%s, profesion=%s, estado_solicitud=%s, clase=%s, nacionalidad=%s, estado_civil=%s, solicitado_por=%s WHERE id_solicitud=%s",
                       //GetSQLValueString($_POST['dni'], "undefined"),
                       GetSQLValueString($_POST['apellido'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['domicilio'], "text"),
                       GetSQLValueString($_POST['prontuario'], "text"),
					   GetSQLValueString($_POST['profesion'], "text"),
                       GetSQLValueString($_POST['estado_solicitud'], "text"),
                       //GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['clase'], "text"),
                       GetSQLValueString($_POST['nacionalidad'], "text"),
                       GetSQLValueString($_POST['estado_civil'], "text"),
                       GetSQLValueString($_POST['solicitado_por'], "text"),
                       //GetSQLValueString($_POST['usuario'], "undefined"),
                       GetSQLValueString($_POST['id_solicitud'], "int"));

					   
  mysql_select_db($database_antecedentes, $antecedentes);
  $Result1 = mysql_query($updateSQL, $antecedentes) or die(mysql_error());

  $updateGoTo = "correcto.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Solicitud = "-1";
if (isset($_GET['id'])) {
  $colname_Solicitud = $_GET['id'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitud = sprintf("SELECT * FROM solicitud WHERE id_solicitud = %s", GetSQLValueString($colname_Solicitud, "int"));
$Solicitud = mysql_query($query_Solicitud, $antecedentes) or die(mysql_error());
$row_Solicitud = mysql_fetch_assoc($Solicitud);
$totalRows_Solicitud = mysql_num_rows($Solicitud);
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
<link rel="stylesheet" type="text/css" media="all" href="../css/estilo_formulario.css" />

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
 <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
 <script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
 <link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
 <link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
 <script language=""="JavaScript">
    function conMayusculas(field) {
            field.value = field.value.toUpperCase()
 }
 </script>
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
        <script language=""="JavaScript">
    function conMayusculas(field) {
            field.value = field.value.toUpperCase()
}
</script>
		<div id="main">
		  <div class="full_w">
           
		   <h1>Procesar Solicitud Pendiente</h1>
		   
		    <h3>Complete el formulario con los datos correspondientes.</h3>
    
           	<form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
            <table width="100%" border="0" align="center">
  <tr>
    <td width="123"><div align="right">DNI:</div></td>
    <td width="230" align="left"><?php echo $row_Solicitud['dni']; ?></td>
    <td width="176">&nbsp;</td>
    <td width="103">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right">Apellido:</div></td>
    <td><span id="sprytextfield1">
                  <input type="text" name="apellido" value="<?php echo htmlentities($row_Solicitud['apellido'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32"  style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
                  <br />
                  <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
    <td><div align="right">Nombre:</div></td>
    <td><span id="sprytextfield2">
                    <input type="text" name="nombre" value="<?php echo htmlentities($row_Solicitud['nombre'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
                    <br />
                  <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
  </tr>
  <tr>
    <td><div align="right">Domicilio:</div></td>
    <td><span id="sprytextfield3">
                    <input type="text" name="domicilio" value="<?php echo htmlentities($row_Solicitud['domicilio'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
                    <br />
                  <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
    <td><div align="right">Prontuario A.G:</div></td>
    <td><span id="sprytextfield4">
                    <input type="text" name="prontuario" value="<?php echo htmlentities($row_Solicitud['prontuario'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32"style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
                    <br />
                  <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
  </tr>
  <tr>
    <td><div align="right">Profesión:</div></td>
    <td><span id="sprytextfield8">
    <input name="profesion" type="text" id="profesion" style="text-transform:uppercase;" onChange="conMayusculas (this)" value="<?php echo htmlentities($row_Solicitud['profesion'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" maxlength="50" />
    <br />
    <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
    <td><div align="right">Estado Civil:</div></td>
    <td align="left"><span id="spryselect1">
                    <select name="estado_civil" id="estado_civil">
                      <option>SELECCIONE UNA OPCI&Oacute;N</option>
                      <option value="SOLTERO/A">SOLTERO/A</option>
                      <option value="CASADO/A">CASADO/A</option>
                      <option value="DIVORCIADO/A">DIVORCIADO/A</option>
                      <option value="VIUDO/A">VIUDO/A</option>
                    </select>
                    <span class="selectRequiredMsg">Seleccione un elemento.</span><br />
</span></td>
  </tr>
  <tr>
    <td><div align="right">Clase</div></td>
    <td><span id="sprytextfield5">
                    <input type="text" name="clase" value="<?php echo htmlentities($row_Solicitud['clase'], ENT_COMPAT, 'iso-8859-1'); ?>" size="32" style="text-transform:uppercase;" />
                    <br />
                  <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
    <td><div align="right">Usuario Solicitante:</div></td>
    <td align="left"><?php echo htmlentities($row_Solicitud['usuario'], ENT_COMPAT, 'iso-8859-1'); ?></td>
  </tr>
  <tr>
    <td><div align="right">Nacionalidad:</div></td>
    <td><span id="sprytextfield6">
                    <input type="text" name="nacionalidad" value="ARGENTINA" size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
                    <br />
                  <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right">Solicitado por:</div></td>
    <td><span id="sprytextfield7">
                    <input type="text" name="solicitado_por" value="<?php echo $row_Solicitud['solicitado_por']; ?>" size="32" style="text-transform:uppercase;" onChange="conMayusculas (this)"/>
                    <br />
                  <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
    <td><div align="right">Estado Solicitud:</div></td>
    <td align="left"><?php echo $row_Solicitud['estado_solicitud']; ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="submit" value="Procesar Solicitud" /></td>
  </tr>
</table>
 <input type="hidden" name="MM_update" value="form1" />
              <input type="hidden" name="id_solicitud" value="<?php echo $row_Solicitud['id_solicitud']; ?>" />
              <input type="hidden" name="dni" value="<?php echo $row_Solicitud['dni']; ?>" />
           <input type="hidden" name="fecha" value="<?php echo $row_Solicitud['fecha_solicitada']; ?>" />
              <input type="hidden" name="estado_solicitud" value="Apto" />
            </form>
 
         <p>&nbsp;</p>
            
            <p>&nbsp;</p>
          </div>
	    </div>
		<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8");
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
mysql_free_result($Solicitud);
?>
