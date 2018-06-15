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

mysql_select_db($database_antecedentes, $antecedentes);
$query_UltimoNroCert = "SELECT * FROM nrocertificado WHERE nrocertificado.id = 1";
$UltimoNroCert = mysql_query($query_UltimoNroCert, $antecedentes) or die(mysql_error());
$row_UltimoNroCert = mysql_fetch_assoc($UltimoNroCert);
$totalRows_UltimoNroCert = mysql_num_rows($UltimoNroCert);
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
<?php
	  //Paso los valores de las cajas de texto y combo al insert
	  $documento=$_POST["dni"];
	  $pasaporte=$_POST["pasaporte"];
	  $tipo_prontuario=$_POST['tipo_prontuario'];
	  $prontuario=$_POST["prontuario"];
	  $nombres=$_POST["nombre"];
	  $clase=$_POST["clase"];
	  $apellido=$_POST["apellido"];
	  $capitalinterior=$_POST["capitalinterior"];
	  $intervino=$_POST["intervino"];
	  $solicitado_por=$_POST['solicitado_por'];
	  $ip_cliente=$_POST["ip_cliente"];
	  $idpais=$_POST['idpais'];
	  $genero=$_POST['genero'];
	  $profesion=$_POST['profesion'];
	  $domicilio=$_POST['domicilio'];
	  $observaciones=$_POST['observaciones'];
	  $estado_soli="Pendiente";
	  $fec= date("Y-m-d");
	  $hora= date("H:i:s");
	  $estadocivil=$_POST['EstadoCivil'];
	  $usuario= $_SESSION['MM_Username'];

// Inserta en la tabla historial
	  mysql_select_db($database_antecedentes, $antecedentes);
	  mysql_query("INSERT INTO historial (dni, pasaporte, apellido, nombre, capitalinterior, intervino, fecha, hora, solicitado_por) VALUES ('$documento', '$pasaporte', '$apellido', '$nombres', '$capitalinterior', '$intervino', '$fec', '$hora', '$solicitado_por') ", $antecedentes) or die("Error en consulta <br>MySQL 
dice: 
".mysql_error());
// Recupero el ultimo id:
$ultimo_id_historial = mysql_insert_id();
// Termina.

//Paso los valores para actualizar la tabla nro_certificado
$anioactual = date(y);
// Si El año de la base de datos es igual al año actual, incrementar el nro_certificado solamente.
	if ($row_UltimoNroCert['anio'] == $anioactual) {
		  $updateSQL = sprintf("UPDATE nrocertificado SET id=1, nro_certificado=%s + 1",
	      GetSQLValueString($row_UltimoNroCert['nro_certificado'], "int"));	   
	      mysql_select_db($database_antecedentes, $antecedentes);
	      mysql_query($updateSQL, $antecedentes) or die(mysql_error());
		  $ultimo_nro_certificado = $row_UltimoNroCert['nro_certificado']+1;
			// Actualizo el nro de certificado
			// Guardo el nro_certificado como str
$ultimo_nro_certificado_completo = $ultimo_nro_certificado . '/' . $row_UltimoNroCert['anio'];
  		  $updateSQL2 = sprintf("UPDATE historial SET nro_certificado=%s WHERE id_historial=%s",
	      GetSQLValueString($ultimo_nro_certificado_completo, "text"),
		  GetSQLValueString($ultimo_id_historial, "int"));
	      mysql_select_db($database_antecedentes, $antecedentes);
	      mysql_query($updateSQL2, $antecedentes) or die(mysql_error());

	}
// De lo contrario poner el nro_certificado a 1 y actualiza el año
	else {
		  $updateSQL = sprintf("UPDATE nrocertificado SET id=1, nro_certificado=1, anio=%s",
		  GetSQLValueString($anioactual, "int"));
	      mysql_select_db($database_antecedentes, $antecedentes);
	      mysql_query($updateSQL, $antecedentes) or die(mysql_error());
		  
		// Actualizo el nro de certificado
		// Guardo el nro_certificado como str
$nro_certificado1 = 1 . '/' . $anioactual;
  		  $updateSQL2 = sprintf("UPDATE historial SET nro_certificado=%s WHERE id_historial=%s",
	      GetSQLValueString($nro_certificado1, "text"),
		  GetSQLValueString($ultimo_id_historial, "int"));
	      mysql_select_db($database_antecedentes, $antecedentes);
	      mysql_query($updateSQL2, $antecedentes) or die(mysql_error());

	}
	
		// Hace un update del padron
		  $updateSQL = sprintf("UPDATE padron SET Apellido=%s, Nombre=%s, Clase=%s, Pasaporte=%s, IdPais=%s, Genero=%s, EstadoCivil=%s, Profesion=%s, Direccion=%s, IdProntuarioTipo=%s, ProntuarioNro=%s, Observaciones=%s WHERE DocumentoNro=%s",
                       GetSQLValueString($apellido, "text"),
                       GetSQLValueString($nombres, "text"),
                       GetSQLValueString($clase, "int"),
					   GetSQLValueString($pasaporte, "text"),
                       GetSQLValueString($idpais, "int"),
                       GetSQLValueString($genero, "text"),
                       GetSQLValueString($estadocivil, "text"),
                       GetSQLValueString($profesion, "text"),
                       GetSQLValueString($domicilio, "text"),
                       GetSQLValueString($tipo_prontuario, "int"),
                       GetSQLValueString($prontuario, "int"),
                       GetSQLValueString($observaciones, "text"),
                       GetSQLValueString($documento, "int"));
		  mysql_select_db($database_antecedentes, $antecedentes);
	      mysql_query($updateSQL, $antecedentes) or die(mysql_error());
		  // Termina
		  
// Traigo la variable de la id solicitud para hacer el update del Estado.
if (isset($_POST['id_solicitud'])){
	$updateSQL = sprintf("UPDATE solicitud SET estado_solicitud='Completado' WHERE id_solicitud=%s",
                       GetSQLValueString($_POST['id_solicitud'], "int"));
  mysql_select_db($database_antecedentes, $antecedentes);
  $Result1 = mysql_query($updateSQL, $antecedentes) or die(mysql_error());
}
// Termina.
?>          

		    <h2>Imprimir Certificado</h2>

		    <table width="1" border="1">
		      <tr>
		        <td><a href="imprimir_certificado_sinantecedentes.php?DocumentoNro=<?php echo $documento?>&id_historial=<?php echo $ultimo_id_historial?>" target="_blank" class="button">Sin Antecedentes</a></td>
		        <td><p><a href="imprimir_certificado_conantecedentes.php?DocumentoNro=<?php echo $documento?>&id_historial=<?php echo $ultimo_id_historial?>" target="_blank" class="button">Con Antecedentes</a></p></td>
	          </tr>
		      <tr>
		        <td><h3>Imprimir Otro certificado de la misma Persona:
		          </h3>
		          <form id="form1" name="form1" method="post" action="solicitud_procesar.php">
                  <input name="DocumentoNro" type="hidden" value="<?php echo $documento?>" />
	              <input type="submit" value="Imprimir Otro" />
		        </form></td>
		        <td>&nbsp;</td>
	          </tr>
          </table>
		    <p>&nbsp;</p>
		    
		    
	      <p>&nbsp;</p>
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
mysql_free_result($UltimoNroCert);
?>
