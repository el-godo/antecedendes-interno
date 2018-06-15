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

$fecha_desde_Estadistica_Solicitud = "0";
if (isset($_POST['fecha_desde'])) {
  $fecha_desde_Estadistica_Solicitud = $_POST['fecha_desde'];
}
$fecha_hasta_Estadistica_Solicitud = "0";
if (isset($_POST['fecha_hasta'])) {
  $fecha_hasta_Estadistica_Solicitud = $_POST['fecha_hasta'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Estadistica_Solicitud = sprintf("SELECT * FROM solicitud WHERE solicitud.fecha_solicitada >= %s AND solicitud.fecha_solicitada <= %s", GetSQLValueString($fecha_desde_Estadistica_Solicitud, "date"),GetSQLValueString($fecha_hasta_Estadistica_Solicitud, "date"));
$Estadistica_Solicitud = mysql_query($query_Estadistica_Solicitud, $antecedentes) or die(mysql_error());
$row_Estadistica_Solicitud = mysql_fetch_assoc($Estadistica_Solicitud);
$totalRows_Estadistica_Solicitud = mysql_num_rows($Estadistica_Solicitud);

$fecha_desde_Solicitudes_Pendientes = "0";
if (isset($_POST['fecha_desde'])) {
  $fecha_desde_Solicitudes_Pendientes = $_POST['fecha_desde'];
}
$fecha_hasta_Solicitudes_Pendientes = "0";
if (isset($_POST['fecha_hasta'])) {
  $fecha_hasta_Solicitudes_Pendientes = $_POST['fecha_hasta'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitudes_Pendientes = sprintf("SELECT * FROM solicitud WHERE solicitud.fecha_solicitada >= %s AND solicitud.fecha_solicitada <= %s AND solicitud.estado_solicitud = 'Pendiente'", GetSQLValueString($fecha_desde_Solicitudes_Pendientes, "date"),GetSQLValueString($fecha_hasta_Solicitudes_Pendientes, "date"));
$Solicitudes_Pendientes = mysql_query($query_Solicitudes_Pendientes, $antecedentes) or die(mysql_error());
$row_Solicitudes_Pendientes = mysql_fetch_assoc($Solicitudes_Pendientes);
$totalRows_Solicitudes_Pendientes = mysql_num_rows($Solicitudes_Pendientes);

$fecha_desde_Solicitudes_Confeccion = "0";
if (isset($_POST['fecha_desde'])) {
  $fecha_desde_Solicitudes_Confeccion = $_POST['fecha_desde'];
}
$fecha_hasta_Solicitudes_Confeccion = "0";
if (isset($_POST['fecha_hasta'])) {
  $fecha_hasta_Solicitudes_Confeccion = $_POST['fecha_hasta'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitudes_Confeccion = sprintf("SELECT * FROM solicitud WHERE solicitud.fecha_solicitada >= %s AND solicitud.fecha_solicitada <= %s AND solicitud.estado_solicitud = 'Confeccion'", GetSQLValueString($fecha_desde_Solicitudes_Confeccion, "date"),GetSQLValueString($fecha_hasta_Solicitudes_Confeccion, "date"));
$Solicitudes_Confeccion = mysql_query($query_Solicitudes_Confeccion, $antecedentes) or die(mysql_error());
$row_Solicitudes_Confeccion = mysql_fetch_assoc($Solicitudes_Confeccion);
$totalRows_Solicitudes_Confeccion = mysql_num_rows($Solicitudes_Confeccion);

$fecha_desde_Solicitudes_Completadas = "0";
if (isset($_POST['fecha_desde'])) {
  $fecha_desde_Solicitudes_Completadas = $_POST['fecha_desde'];
}
$fecha_hasta_Solicitudes_Completadas = "0";
if (isset($_POST['fecha_hasta'])) {
  $fecha_hasta_Solicitudes_Completadas = $_POST['fecha_hasta'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitudes_Completadas = sprintf("SELECT * FROM solicitud WHERE solicitud.fecha_solicitada >= %s AND solicitud.fecha_solicitada <= %s AND solicitud.estado_solicitud = 'Completado'", GetSQLValueString($fecha_desde_Solicitudes_Completadas, "date"),GetSQLValueString($fecha_hasta_Solicitudes_Completadas, "date"));
$Solicitudes_Completadas = mysql_query($query_Solicitudes_Completadas, $antecedentes) or die(mysql_error());
$row_Solicitudes_Completadas = mysql_fetch_assoc($Solicitudes_Completadas);
$totalRows_Solicitudes_Completadas = mysql_num_rows($Solicitudes_Completadas);

$fecha_desde_Solicitudes_GeneroM = "0";
if (isset($_POST['fecha_desde'])) {
  $fecha_desde_Solicitudes_GeneroM = $_POST['fecha_desde'];
}
$fecha_hasta_Solicitudes_GeneroM = "0";
if (isset($_POST['fecha_hasta'])) {
  $fecha_hasta_Solicitudes_GeneroM = $_POST['fecha_hasta'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitudes_GeneroM = sprintf("SELECT * FROM solicitud WHERE solicitud.fecha_solicitada >= %s AND solicitud.fecha_solicitada <= %s AND solicitud.genero = 'M'", GetSQLValueString($fecha_desde_Solicitudes_GeneroM, "date"),GetSQLValueString($fecha_hasta_Solicitudes_GeneroM, "date"));
$Solicitudes_GeneroM = mysql_query($query_Solicitudes_GeneroM, $antecedentes) or die(mysql_error());
$row_Solicitudes_GeneroM = mysql_fetch_assoc($Solicitudes_GeneroM);
$totalRows_Solicitudes_GeneroM = mysql_num_rows($Solicitudes_GeneroM);

$fecha_desde_Solicitudes_GeneroF = "0";
if (isset($_POST['fecha_desde'])) {
  $fecha_desde_Solicitudes_GeneroF = $_POST['fecha_desde'];
}
$fecha_hasta_Solicitudes_GeneroF = "0";
if (isset($_POST['fecha_hasta'])) {
  $fecha_hasta_Solicitudes_GeneroF = $_POST['fecha_hasta'];
}
mysql_select_db($database_antecedentes, $antecedentes);
$query_Solicitudes_GeneroF = sprintf("SELECT * FROM solicitud WHERE solicitud.fecha_solicitada >= %s AND solicitud.fecha_solicitada <= %s AND solicitud.genero = 'F'", GetSQLValueString($fecha_desde_Solicitudes_GeneroF, "date"),GetSQLValueString($fecha_hasta_Solicitudes_GeneroF, "date"));
$Solicitudes_GeneroF = mysql_query($query_Solicitudes_GeneroF, $antecedentes) or die(mysql_error());
$row_Solicitudes_GeneroF = mysql_fetch_assoc($Solicitudes_GeneroF);
$totalRows_Solicitudes_GeneroF = mysql_num_rows($Solicitudes_GeneroF);
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
		    <h1>Estadisticas</h1>
	      <p>&nbsp;</p>
		    <p>&nbsp;</p>
		    <form id="form1" name="form1" method="post" action="">
		      <h3>
		        <label for="fecha_desde"></label>
	          Ingrese Fecha:</h3>
		      <h3> Desde:
		        <input type="text" name="fecha_desde" id="fecha_desde" />
		        Hasta: 
		        <input type="text" name="fecha_hasta" id="fecha_hasta" />
		        <input type="submit" name="button" id="button" value="Buscar" />
	          </h3>
		      <p>&nbsp;</p>
		      
              
          </form>
          <div id="datepicker"></div>
            <script>
$( "#fecha_desde" ).datepicker({
    // Formato de la fecha
    dateFormat: "yy-mm-dd",
    // Primer dia de la semana El lunes
    firstDay: 1,
    // Dias Largo en castellano
    dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
    // Dias cortos en castellano
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    // Nombres largos de los meses en castellano
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
    // Nombres de los meses en formato corto 
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
    // Cuando seleccionamos la fecha esta se pone en el campo Input 
    //onSelect: function(dateText) { 
    //      $('#fecha').val(dateText);
    //  }
});
</script>
  <script>
$( "#fecha_hasta" ).datepicker({
    // Formato de la fecha
    dateFormat: "yy-mm-dd",
    // Primer dia de la semana El lunes
    firstDay: 1,
    // Dias Largo en castellano
    dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
    // Dias cortos en castellano
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    // Nombres largos de los meses en castellano
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
    // Nombres de los meses en formato corto 
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
    // Cuando seleccionamos la fecha esta se pone en el campo Input 
    //onSelect: function(dateText) { 
    //      $('#fecha').val(dateText);
    //  }
});
</script>
  <?php if ($totalRows_Estadistica_Solicitud > 0) { // Show if recordset not empty ?>
            <h4>Intervalo de D&iacute;as: 
		        <?php 
					function dias_transcurridos($fecha_i,$fecha_f)
					{
						$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
						$dias 	= abs($dias); $dias = floor($dias);		
						return $dias;
					}
					// Ejemplo de uso:
					echo dias_transcurridos($_POST['fecha_desde'],$_POST['fecha_hasta']);
					// Salida : 17?>
	      </h4>
  <p>
    
    <script type="text/javascript">
$(function () {
    $('#container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Estadisticas de Solicitudes - Por Estado'
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Solicitudes',
            data: [
                ['Pendientes',   <?php echo $totalRows_Solicitudes_Pendientes ?>],
                ['En Confeccion',       <?php echo $totalRows_Solicitudes_Confeccion ?>],
                {
                    name: 'Completadas',
                    y: <?php echo $totalRows_Solicitudes_Completadas ?>,
                    sliced: true,
                    selected: true
                },
            ]
        }]
    });
});
    

		</script>
    </head>
    <body>
    <script src="../js/highcharts.js"></script>
    <script src="../js/modules/exporting.js"></script>
    
  <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
              
            <table width="523" height="134">
              <tr>
                <td width="293">Solicitudes Pendientes:</td>
                <td width="218"><?php echo $totalRows_Solicitudes_Pendientes ?></td>
              </tr>
              <tr>
                <td>Solicitudes en Proceso de Confecci&oacute;n:</td>
                <td><?php echo $totalRows_Solicitudes_Confeccion ?></td>
              </tr>
              <tr>
                <td>Solicitudes Completadas:</td>
                <td><?php echo $totalRows_Solicitudes_Completadas ?></td>
              </tr>
              <tr>
                <td><strong>Total de Solicitudes:</strong></td>
                <td><strong><?php echo $totalRows_Estadistica_Solicitud ?></strong></td>
              </tr>
            </table>
<p>&nbsp;</p>
<p>
<script type="text/javascript">
$(function () {
    $('#container2').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Estadisticas de Solicitudes Completadas - Por Genero'
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Genero',
            data: [
                ['Masculino',   <?php echo $totalRows_Solicitudes_GeneroM ?>],
                ['Femenino',       <?php echo $totalRows_Solicitudes_GeneroF ?>],
            ]
        }]
    });
});
    

		</script>
    </head>
    <body>
    <script src="../js/highcharts.js"></script>
    <script src="../js/modules/exporting.js"></script>
    
  <div id="container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</p>
<p>&nbsp;</p>
<table width="1">
  <tr>
    <td>Solicitudes Genero Masculino:</td>
    <td><?php echo $totalRows_Solicitudes_GeneroM ?></td>
  </tr>
  <tr>
    <td>Solicitudes Genero Femenino:</td>
    <td><?php echo $totalRows_Solicitudes_GeneroF ?></td>
  </tr>
</table>
              <?php } // Show if recordset not empty ?>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
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
mysql_free_result($Estadistica_Solicitud);

mysql_free_result($Solicitudes_Pendientes);

mysql_free_result($Solicitudes_Confeccion);

mysql_free_result($Solicitudes_Completadas);

mysql_free_result($Solicitudes_GeneroM);

mysql_free_result($Solicitudes_GeneroF);
?>
