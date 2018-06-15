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

$colname_Padron = "-1";
if (isset($_GET["DocumentoNro"])) {
  $colname_Padron = $_GET["DocumentoNro"];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Padron = sprintf("SELECT * FROM padron WHERE DocumentoNro = %s", GetSQLValueString($colname_Padron, "int"));
$Padron = mysql_query($query_Padron, $antecedentes) or die(mysql_error());
$row_Padron = mysql_fetch_assoc($Padron);
$totalRows_Padron = mysql_num_rows($Padron);

$colname_Solicitud = "-1";
if (isset($_GET['id_solicitud'])) {
  $colname_Solicitud = $_GET['id_solicitud'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitud = sprintf("SELECT * FROM solicitud WHERE id_solicitud = %s", GetSQLValueString($colname_Solicitud, "int"));
$Solicitud = mysql_query($query_Solicitud, $antecedentes) or die(mysql_error());
$row_Solicitud = mysql_fetch_assoc($Solicitud);
$totalRows_Solicitud = mysql_num_rows($Solicitud);

$IdProntuario_TipoProntuario = "0";
if (isset($row_Padron['IdProntuarioTipo'])) {
  $IdProntuario_TipoProntuario = $row_Padron['IdProntuarioTipo'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_TipoProntuario = sprintf("SELECT * FROM prontuariostipos WHERE prontuariostipos.IdProntuarioTipo = %s", GetSQLValueString($IdProntuario_TipoProntuario, "int"));
$TipoProntuario = mysql_query($query_TipoProntuario, $antecedentes) or die(mysql_error());
$row_TipoProntuario = mysql_fetch_assoc($TipoProntuario);
$totalRows_TipoProntuario = mysql_num_rows($TipoProntuario);

$IdPais_Nacionalidad = "0";
if (isset($row_Padron['IdPais'])) {
  $IdPais_Nacionalidad = $row_Padron['IdPais'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Nacionalidad = sprintf("SELECT * FROM paises WHERE IdPais = %s", GetSQLValueString($IdPais_Nacionalidad, "int"),GetSQLValueString($IdPais_Nacionalidad, "int"));
$Nacionalidad = mysql_query($query_Nacionalidad, $antecedentes) or die(mysql_error());
$row_Nacionalidad = mysql_fetch_assoc($Nacionalidad);
$totalRows_Nacionalidad = mysql_num_rows($Nacionalidad);

$valor_Historial = "0";
if (isset($_GET['id_historial'])) {
  $valor_Historial = $_GET['id_historial'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Historial = sprintf("SELECT * FROM historial WHERE historial.id_historial = %s", GetSQLValueString($valor_Historial, "int"));
$Historial = mysql_query($query_Historial, $antecedentes) or die(mysql_error());
$row_Historial = mysql_fetch_assoc($Historial);
$totalRows_Historial = mysql_num_rows($Historial);
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
<head>
<script language="Javascript">
function imprimir() {
if (window.print)
window.print()
else
alert("Para imprimir presione Crtl+P.");
}
</script>
          <link rel="stylesheet" type="text/css" href="../../css/imprimir.css" media="print" />
		    <table>
		      <tr>
		        <td height="60">
		          <p align="center"><img src="../img/escudo catamarca.jpg" width="53" height="57" /><br />
                </p></td>
		        <td><div class="titulo">
		          <h1>CERTIFICADO DE ANTECEDENTES</h1>
</div>
		          <div align="center">V&aacute;lido por seis meses</div></td>
		        <td><strong>POLICIA DE<br />
CATAMARCA</strong></td>
	          </tr>
	      </table>
          
          <div class="contenido">
            <p>Los funcionarios policiales que suscriben Certifican:</p>
            <p>Que 
              <label class="subrayado"><?php echo $row_Padron['Apellido']; ?>, </label>		    
              <span class="subrayado"><?php echo $row_Padron['Nombre']; ?></span> Clase:
              <label class="subrayado"><?php echo $row_Padron['Clase']; ?></label> 
              D.N.I. N&deg;: 
              <?php
			  // Este if es para que si hay datos en el campo pasaporte imprima el nro de pasaporte de lo conterario el dni.
			  if ($row_Padron['Pasaporte'] == "") { ?>
					<label class="subrayado"><?php echo $row_Padron['DocumentoNro']; ?></label><?php }
			else { ?>
					<label class="subrayado">PAS: <?php echo $row_Padron['Pasaporte']; ?></label><?php } ?>
                 
              de nacionalidad:              <span class="subrayado"><?php echo $row_Nacionalidad['Pais']; ?></span>  de estado civil:
              <label class="subrayado">
			  <?php if ($row_Padron['EstadoCivil'] == "S") {
				  echo "SOLTERO/A";
			  } elseif ($row_Padron['EstadoCivil'] == "C") {
				  echo "CASADO/A";
			  } elseif ($row_Padron['EstadoCivil'] == "D") {
				  echo "DIVORCIADO/A";
			  } elseif ($row_Padron['EstadoCivil'] == "V") {
				  echo "VIUDO/A";
			  }
			  
				  ?>
              </label> 
              de profesi&oacute;n: 
              <label class="subrayado"><?php echo $row_Padron['Profesion']; ?></label> 		    
              con domicilio: 
              <label class="subrayado"><?php echo $row_Padron['Direccion']; ?></label> 
              se encuentra identificado/a en esta polic&iacute;a bajo Prontuario: <span class="subrayado"><?php echo $row_TipoProntuario['ProntuarioTipo']; ?></span> 
              <label class="subrayado"><?php echo $row_Padron['ProntuarioNro']; ?></label>
            </p>
            <h3>Observaciones:<br />
              <label class="subrayado">
                <?php if ($row_Padron['Observaciones'] == ""){
				  			echo "NO REGISTRA ANTECEDENTES POLICIALES NI JUDICIALES EN LA PROVINCIA.";
			  		}
				    else {
							echo nl2br($row_Padron['Observaciones']);
					}
				  	?>
              </label> 
            </h3>
            <p>
              <label for="textarea"></label>
</p>
            <p>A solicitud de la parte interesada y al solo efecto de ser presentada en: 
              <label class="subrayado"><?php echo $row_Historial['solicitado_por']; ?></label> 
  expiden el presente en la Ciudad de San Fernando del Valle de Catamarca (R.A.), con fecha: <?php echo date("d/m/Y");?> .-		    </p>
<p>Cert. N&deg;: <?php echo $row_Historial['nro_certificado']; ?></p>
<p>Se expide conforme Ley N&deg; 4663, Art. 8 Inc. &quot;C&quot;.-</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table>
  <tr align="center">
    <td width="10">&nbsp;</td>
    <td width="10">&nbsp;</td>
    <td width="259">----------------------------------------------------<br />
      Jefe Ant. Personales</td>
    <td width="333">----------------------------------------------------<br />
    Jefe Dpto. Inv. Judiciales</td>
  </tr>
  <tr align="center">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="220" border="1">
            <tr>
                <td width="90" height="30" align="center">INTERVINO</td>
                <td width="130" rowspan="4">&nbsp;</td>
            </tr>
            <tr>
              <td height="30" align="center"><?php echo $row_Historial['intervino']; ?></td>
            </tr>
            <tr>
              <td height="30">&nbsp;</td>
            </tr>
            <tr>
              <td height="30">&nbsp;</td>
            </tr>
          </table>
<p>&nbsp;</p>
<p><a href="imprimir_certificado_conantecedentes_doc.php?DocumentoNro=<?php echo $_GET['DocumentoNro']; ?>&id_historial=<?php echo $_GET['id_historial']; ?>" class="button">Descargar a Word</a></p>
<?php
            /*
			<div id="imp"><p align="right"><input name="imprimir" type="button" onclick="javascript:imprimir();" value="Imprimir Certificado"></p>
            </div>
            <script>
            javascript:imprimir();
            </script>
          </div>*/
		  ?>
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

mysql_free_result($Solicitud);

mysql_free_result($TipoProntuario);

mysql_free_result($Nacionalidad);

mysql_free_result($Historial);
?>
