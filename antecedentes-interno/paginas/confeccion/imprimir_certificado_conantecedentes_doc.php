<?php
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment; Filename=Con Antecedentes.doc");
?>
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
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
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
mysql_free_result($Padron);

mysql_free_result($Solicitud);

mysql_free_result($TipoProntuario);

mysql_free_result($Nacionalidad);

mysql_free_result($Historial);
?>
