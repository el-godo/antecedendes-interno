<?php require_once('../../Connections/antecedentes.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin,archivo";
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE solicitud SET estado_solicitud=%s WHERE id_solicitud=%s",
                       GetSQLValueString($_POST['estado_solicitud'], "text"),
                       GetSQLValueString($_POST['id_solicitud'], "int"));

  mysql_select_db($database_antecedentes, $antecedentes);
  $Result1 = mysql_query($updateSQL, $antecedentes) or die(mysql_error());

  $updateGoTo = "solicitud_pendiente.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$maxRows_Solicitudes = 10;
$pageNum_Solicitudes = 0;
if (isset($_GET['pageNum_Solicitudes'])) {
  $pageNum_Solicitudes = $_GET['pageNum_Solicitudes'];
}
$startRow_Solicitudes = $pageNum_Solicitudes * $maxRows_Solicitudes;

mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitudes = "SELECT * FROM solicitud WHERE estado_solicitud = 'Pendiente' ORDER BY fecha_solicitada ASC";
$query_limit_Solicitudes = sprintf("%s LIMIT %d, %d", $query_Solicitudes, $startRow_Solicitudes, $maxRows_Solicitudes);
$Solicitudes = mysql_query($query_limit_Solicitudes, $antecedentes) or die(mysql_error());
$row_Solicitudes = mysql_fetch_assoc($Solicitudes);

if (isset($_GET['totalRows_Solicitudes'])) {
  $totalRows_Solicitudes = $_GET['totalRows_Solicitudes'];
} else {
  $all_Solicitudes = mysql_query($query_Solicitudes);
  $totalRows_Solicitudes = mysql_num_rows($all_Solicitudes);
}
$totalPages_Solicitudes = ceil($totalRows_Solicitudes/$maxRows_Solicitudes)-1;

$queryString_Solicitudes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Solicitudes") == false && 
        stristr($param, "totalRows_Solicitudes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Solicitudes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Solicitudes = sprintf("&totalRows_Solicitudes=%d%s", $totalRows_Solicitudes, $queryString_Solicitudes);

$Id_TipoProntuario = "0";
if (isset($row_Solicitudes['tipo_prontuario'])) {
  $Id_TipoProntuario = $row_Solicitudes['tipo_prontuario'];
}

/* Funcion para obtener datos de una tabla
Para usarla se la debe llamar de la siguiente forma:
Obtener("nombre_de_la_tabla", "dato_que_quiero_obtener", "idproducto", "identificador"); 
Lectura de la funcion:

Obtener de la $tabla el $dato 
donde el $idproducto es igual a $identificador
echo Obtener($tabla, $dato, $idproducto, $identificador);
?> 
Creado por: Emmanuel_Ar*/

function Obtener($tabla, $dato, $idproducto, $identificador)
{
    if ($identificador != ""){
        global $database_antecedentes, $antecedentes;
        

mysql_select_db($database_antecedentes, $antecedentes);
$query_ConsultaFuncion = sprintf("SELECT * FROM $tabla WHERE $idproducto = %s", $identificador);
$ConsultaFuncion = mysql_query($query_ConsultaFuncion, $antecedentes) or die(mysql_error());
$row_ConsultaFuncion = mysql_fetch_assoc($ConsultaFuncion);
$totalRows_ConsultaFuncion = mysql_num_rows($ConsultaFuncion);

return $row_ConsultaFuncion["$dato"];
        

mysql_free_result($ConsultaFuncion);

}
    else
    {
        echo "No se encontro un valor.";
    }
}
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
					<li class="b1"><a class="icon view_asignar" href="solicitud_pendiente.php">Solicitudes Pendientes</a></li>
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
	      <div class="full_w">
		    <div id="&rdquo;stylized&rdquo;" class="&rdquo;myform&rdquo;"></div>
            <h1>Listado de Solicitudes Pendientes</h1>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <?php if ($totalRows_Solicitudes > 0) { // Show if recordset not empty ?>
  <table border="1">
    <tr>
      <td align="center"><p>DNI</p></td>
      <td align="center">Apellido</td>
      <td align="center">Nombres</td>
      <td align="center">Tipo</td>
      <td align="center">Prontuario</td>
      <td align="center">Fecha<br />
        Solicitada</td>
      <td align="center">Acci&oacute;n</td>
    </tr>
    <?php do { ?>
      <tr>
        <td align="justify"><?php echo $row_Solicitudes['dni']; ?></td>
        <td align="justify"><?php echo $row_Solicitudes['apellido']; ?></td>
        <td align="justify"><p><?php echo $row_Solicitudes['nombre']; ?></p></td>
        <td align="justify"><p><?php echo Obtener(prontuariostipos, ProntuarioTipo, IdProntuarioTipo, $row_Solicitudes['tipo_prontuario']); ?></p></td>
        <td align="center"><?php echo $row_Solicitudes['prontuario']; ?></td>
        <td align="center"><p>
          <?php 
				  $fecha = date_create($row_Solicitudes['fecha_solicitada']); 
				  echo date_format($fecha, 'd/m/Y');
				  ?>
          <br />
        </p></td>
        <td align="center"><form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
          <p>
            <input name="id_solicitud" type="hidden" value="<?php echo $row_Solicitudes['id_solicitud']; ?>" />
            <input name="estado_solicitud" type="hidden" value="Confeccion" />
            </p>
          <p>
            <input name="button" type="submit" id="button" value="Procesar" />
            </p>
          <input type="hidden" name="MM_update" value="form1" />
        </form>
          <p>&nbsp;</p></td>
      </tr>
      <?php } while ($row_Solicitudes = mysql_fetch_assoc($Solicitudes)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
<p>&nbsp;
            <table border="0">
              <tr>
                <td><?php if ($pageNum_Solicitudes > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Solicitudes=%d%s", $currentPage, 0, $queryString_Solicitudes); ?>">Primero</a>
                <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_Solicitudes > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Solicitudes=%d%s", $currentPage, max(0, $pageNum_Solicitudes - 1), $queryString_Solicitudes); ?>">Anterior</a>
                <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_Solicitudes < $totalPages_Solicitudes) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Solicitudes=%d%s", $currentPage, min($totalPages_Solicitudes, $pageNum_Solicitudes + 1), $queryString_Solicitudes); ?>">Siguiente</a>
                <?php } // Show if not last page ?></td>
                <td><?php if ($pageNum_Solicitudes < $totalPages_Solicitudes) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Solicitudes=%d%s", $currentPage, $totalPages_Solicitudes, $queryString_Solicitudes); ?>">&Uacute;ltimo</a>
                <?php } // Show if not last page ?></td>
              </tr>
            </table>
            </p>
            <p>&nbsp;
              Registros <?php echo ($startRow_Solicitudes + 1) ?> a <?php echo min($startRow_Solicitudes + $maxRows_Solicitudes, $totalRows_Solicitudes) ?> de <?php echo $totalRows_Solicitudes ?></p>
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
mysql_free_result($Solicitudes);
?>
