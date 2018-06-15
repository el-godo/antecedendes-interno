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

mysql_select_db($database_antecedentes, $antecedentes);
$query_Margenes = "SELECT * FROM margenes WHERE margenes.id = 1";
$Margenes = mysql_query($query_Margenes, $antecedentes) or die(mysql_error());
$row_Margenes = mysql_fetch_assoc($Margenes);
$totalRows_Margenes = mysql_num_rows($Margenes);

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

<?php require_once('class.ezpdf.php');
$pdf = new Cezpdf('a4');
// Funcion para trabajar puntos en centimetros
function puntos_cm ($medida, $resolucion=72)
{
   //// 2.54 cm / pulgada
   return ($medida/(2.54))*$resolucion;
}
// Funcion para mostrar los meses del año
function nombremes($mes){
 setlocale(LC_TIME, 'spanish');  
 $nombre=strftime("%B",mktime(0, 0, 0, $mes, 1, 2000));
 //Para transformar en mayuscula
 $nombre = strtoupper($nombre);
 return $nombre;
} 
// Seleccionamos el tipo de letra
$pdf->selectFont('../fonts/courier.afm');


// Datos de la Base de Datos
$ap = $row_Padron['Apellido'];
$nom = $row_Padron['Nombre'];
$clase = $row_Padron['Clase'];
$dni = $row_Padron['DocumentoNro'];
$pasaporte = $row_Padron['Pasaporte'];
$nacionalidad = $row_Nacionalidad['Pais'];
// Estado Civil
if ($row_Padron['EstadoCivil'] == "S") {
			  $estadocivil = 'SOLTERO/A';
		  } elseif ($row_Padron['EstadoCivil'] == "C") {
			  $estadocivil = 'CASADO/A';
		  } elseif ($row_Padron['EstadoCivil'] == "D") {
			  $estadocivil = 'DIVORCIADO/A';
		  } elseif ($row_Padron['EstadoCivil'] == "V") {
			  $estadocivil = 'VIUDO/A';
}
$profesion = $row_Padron['Profesion'];
$domicilio = $row_Padron['Direccion'];
$prontuariotipo = $row_TipoProntuario['ProntuarioTipo'];
$prontuarionro = $row_Padron['ProntuarioNro'];
$solicitadopor = $row_Historial['solicitado_por'];
$mes=nombremes(date("m"));
$nrocertificado = $row_Historial['nro_certificado'];
$intervino = $row_Historial['intervino'];

// Trae los margenes de la base de datos
$margenleft = $row_Margenes['izquierdo'];
$margentop = $row_Margenes['superior'];

$pdf->addText(puntos_cm(3.0+$margenleft),puntos_cm(23.6-$margentop),10,$ap. ', ' .$nom);
$pdf->addText(puntos_cm(8.5+$margenleft),puntos_cm(22.95-$margentop),10,$clase);
// Este if es para que si hay datos en el campo pasaporte imprima el nro de pasaporte de lo conterario el dni.
if ($pasaporte == "") {
	$pdf->addText(puntos_cm(13.3+$margenleft),puntos_cm(22.95-$margentop),10,$dni);
	}
else {
	$pdf->addText(puntos_cm(13.3+$margenleft),puntos_cm(22.95-$margentop),10,'PAS: ' .$pasaporte);
	}
$pdf->addText(puntos_cm(5.5+$margenleft),puntos_cm(22.35-$margentop),10,$nacionalidad);
$pdf->addText(puntos_cm(13.5+$margenleft),puntos_cm(22.35-$margentop),10,$estadocivil);
$pdf->addText(puntos_cm(5+$margenleft),puntos_cm(21.7-$margentop),10,$profesion);
$pdf->addText(puntos_cm(4.5+$margenleft),puntos_cm(21.1-$margentop),10,$domicilio);
$pdf->addText(puntos_cm(5.5+$margenleft),puntos_cm(19.85-$margentop),10,$prontuariotipo. ' ' .$prontuarionro);
$pdf->addText(puntos_cm(1.8+$margenleft),puntos_cm(15.9-$margentop),10,$solicitadopor);
$pdf->addText(puntos_cm(14+$margenleft),puntos_cm(15.3-$margentop),10,date("d"));
$pdf->addText(puntos_cm(5.5+$margenleft),puntos_cm(14.65-$margentop),10,$mes);
$pdf->addText(puntos_cm(12.5+$margenleft),puntos_cm(14.65-$margentop),10,date("Y"));
$pdf->addText(puntos_cm(4+$margenleft),puntos_cm(13.8-$margentop),10,$nrocertificado);
$pdf->addText(puntos_cm(2+$margenleft),puntos_cm(10.6-$margentop),10,$intervino);



$pdf->setLineStyle(5,'round','',array(0,15));
$pdf->setStrokeColor(0,0,0);




////creamos un nuevo array en el que pondremos un borde=1
///y las cabeceras de la tabla las pondremos ocultas
unset ($opciones_tabla);

//// mostrar las lineas
$opciones_tabla['showlines']=1;

//// mostrar las cabeceras
$opciones_tabla['showHeadings']=0;

//// lineas sombreadas
$opciones_tabla['shaded']= 1;

//// tamaño letra del texto
$opciones_tabla['fontSize']= 10;

//// color del texto
$opciones_tabla['textCol'] = array(1,0,0);

//// tamaño de las cabeceras (texto)
$opciones_tabla['titleFontSize'] = 12;


$pdf->ezStream();
//$documento_pdf = $pdf->ezOutput();
//$fichero = fopen('prueba.pdf','wb');
//fwrite ($fichero, $documento_pdf);
//fclose ($fichero);
?>


<?php
mysql_free_result($Padron);

mysql_free_result($TipoProntuario);

mysql_free_result($Nacionalidad);

mysql_free_result($Margenes);

mysql_free_result($Historial);
?>