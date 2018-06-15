<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin,entrada,archivo,confeccion";
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl"><!-- InstanceBegin template="/Templates/plantillasitio.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="author" content="Pawel 'kilab' Balicki - kilab.pl" />
<title>Sistema de Solicitud de Antecedentes Personales **** Polic&iacute;a de Catamarca - Ministerio de Gobierno y Justicia</title>
<link rel="stylesheet" type="text/css" href="../css/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../css/navi.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../css/tcal.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css" media="screen" />
<!--<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>-->
<!--<script type="text/javascript" src="../js/tcal.js"></script>-->
<!--<script type="text/javascript" src="../js/ui/1.9.1/jquery-1.9.1.js"></script>-->
<!--<script type="text/javascript" src="../js/ui/1.10.3/jquery-ui.js"></script>-->

<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />

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
		    <li>&#8250; <a href="logout.php">Salir del sistema</a></li>
						
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
					<li class="b1"><a class="icon view_asignar" href="entrada/index.php">Generar Solicitud</a></li>
                    <li class="b2"><a class="icon view_asignar" href="entrada/ver_estado.php">Ver Estado</a></li>
				</ul>
			</div>
        <?php } ?>

            
         <?php
			// Aqui comienza el if que muestra el panel solo si sos user super
			if ($_SESSION['MM_UserGroup'] == 'archivo' OR $_SESSION['MM_UserGroup'] == 'admin') { // Show if recordset empty ?>   
            <div class="box">
				<div class="h_title">&#8250; Archivo</div>
				<ul id="home">
					<li class="b1"><a class="icon view_asignar" href="archivo/solicitud_pendiente.php">Solicitudes Pendientes</a></li>
				</ul>
			</div>
			<?php }?>
            
            
            <?php
			// Aqui comienza el if que muestra el panel solo si sos user super
			if ($_SESSION['MM_UserGroup'] == 'confeccion' OR $_SESSION['MM_UserGroup'] == 'admin') { // Show if recordset empty ?>
			<div class="box">
				<div class="h_title">&#8250; Confección e Impresión</div>
				<ul>
					<li class="b1"><a class="icon view_imprimir" href="confeccion/listado_solicitudes.php">Imprimir Ingresados por Sistema</a></li>
                    <li class="b2"><a class="icon view_reasignar" href="confeccion/alta_solicitante.php">Alta de Solicitante</a></li>
                                     
                    <li class="b4"><a class="icon view_padron" href="confeccion/listado_solicitante.php">Modificar Datos Solicitante</a></li>
                    <li class="b3"><a class="icon view_imprimir" href="confeccion/listado_solicitudes_impresas.php">Certificados Impresos</a></li>
                    
                    <li class="b5"><a class="icon view_imprimir" href="confeccion/configurar_margenes.php">Configurar Margenes</a></li>
                    
                    <li class="b6"><a class="icon view_imprimir" href="confeccion/generar_certificado.php">Generar Certificados Nuevos, del Interior y Urgentes</a></li>
				</ul>
			</div>
            <?php }?>
            
            
            <div class="box">
				<div class="h_title">&#8250; Sistema</div>
				<ul>
                
                <li class="b1"><a class="icon view_help" href="manual_usuario.php">Manual de Usuario</a></li>
				<li class="b2"><a class="icon view_deposito" href="logout.php">Salir del Sistema</a></li>
                    
                    
                   
				</ul>
			</div>
            
		</div>
		<div id="main">
		  <div class="full_w"><!-- InstanceBeginEditable name="EditRegion1" -->
		    <h1><a name="up" id="up"></a>Manual de Usuario</h1>
		    <p>&nbsp;</p>
		    <h3 align="center"><strong><u>&Iacute;NDICE:</u></strong></h3>
            <h3>&nbsp;</h3>
            <h3><strong>1.1</strong> <a href="#a.1.1">Pantalla de Acceso</a><br />
              <strong>1.2</strong> <a href="#a.1.2">Pantalla de Acceso Denegado</a><br />
              <strong>2.</strong> <strong><a href="#a.2">Roles de Usuarios</a></strong><br />
              <strong>2.1</strong> <strong><a href="#a.2.1">Rol de Entrada</a></strong><br />
              <strong>2.2</strong> <a href="#a.2.2">Men&uacute; de Entrada</a><br />
              <strong>2.3</strong> <a href="#a.2.3">Pantalla de Inicio</a><br />
              <strong>2.4</strong> <a href="#a.2.4">Generar Solicitud</a><br />
              <strong>2.5</strong> <a href="#a.2.5">Ver Estado de Solicitud</a><br />
              <strong>3.1</strong> <strong><a href="#a.3.1">Rol de Archivo</a></strong><br />
              <strong>3.2</strong> <a href="#a.3.2">Men&uacute; Archivo</a><br />
              <strong>3.3</strong> <a href="#a.3.3">Listar Solicitudes Pendientes</a><br />
              <strong>4.1</strong> <strong><a href="#a.4.1">Rol Confecci&oacute;n</a></strong><br />
              <strong>4.2</strong> <a href="#a.4.2">Men&uacute; Confecci&oacute;n</a><br />
              <strong>4.3</strong> <a href="#a.4.3">Como imprimir un certificado  solicitado</a><br />
              <strong>4.4</strong> <a href="#a.4.4">Como dar de alta a un  solicitante</a><br />
              <strong>4.5</strong> <a href="#a.4.5">Confeccionar una solicitud de  una persona &nbsp;que no se encuentra en la  base de datos.</a><br />
              <strong>4.6</strong> <a href="#a.4.6">Certificados Impresos</a><br />
              <strong>5. <a href="#a.5">Salir del Sistema</a><br clear="all" />
            </strong></h3>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <h3><strong><a name="a.1.1" id="a.1.1"></a>1.1 Pantalla de Acceso</strong><br />
            <img src="img_manual_usuario/manual_usuario_clip_image002.jpg" alt="" width="589" height="490" /></h3>
            <h3>Para acceder al sistema Ud.  deber&aacute; ingresar su nombre de usuario y su contrase&ntilde;a a los fines de ser  validados, como puede observarse en la figura de la pantalla de acceso que  muestra el sistema.</h3>
            <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
            </strong>
            </h3>
          <h3><strong><a name="a.1.2" id="a.1.2"></a>1.2 Pantalla de Acceso Denegado</strong> </h3>
            <h3><br />
              Al ser validados los datos ingresados y si estos no fueran  correctos debido a un error en la escritura de los mismos, o si la persona que  desea ingresar no se encuentra registrada en el sistema. Este emite una  pantalla de Acceso Denegado como se muestra en la figura.<br />
  <img src="img_manual_usuario/manual_usuario_clip_image004.gif" alt="" width="589" height="350" />&nbsp;</h3>
            <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
            </strong> </h3>
            <h3><strong><u><a name="a.2" id="a.2"></a>2. Roles de  Usuarios:</u></strong></h3>
            <h3><br />
            El sistema contempla cuatro roles  de usuarios: (entrada, archivo, confecci&oacute;n, admin)</h3>
            <h3><br />
            <u>Rol Entrada:</u> Usuarios  encargados de generar las solicitudes en Mesa de Entrada y Verificar el estado  de una solicitud, (identificando en el proceso que se encuentra), en caso de  ser requerido por un solicitante.</h3>
            <h3><br />
            <u>Rol Archivo:</u> Usuarios que  procesan las solicitudes generadas en mesa de entrada.</h3>
            <h3><br />
            <u>Rol Confecci&oacute;n:</u> Usuarios  encargados de:</h3>
            <ul>
              <li>
                <h3>Generar el alta de un solicitante en caso que no  se encuentre en la base de datos.</h3>
              </li>
              <li>
                <h3>Actualizar los datos del mismo.</h3>
              </li>
              <li>
                <h3>Imprimir los certificados de antecedentes</h3>
              </li>
              <li>
                <h3>Listar los certificados ya impresos  anteriormente.</h3>
              </li>
            </ul>
            <h3><u>Rol Admin:</u> Administradores  con acceso a manipular todas las opciones del sistema, incluyendo las de los  roles antes mencionados.</h3>
            <h3><strong><a href="#up">Volver al &Iacute;ndice</a><br clear="all" />
            </strong></h3>
            <p>&nbsp;</p>
          <h3><strong><u><a name="a.2.1" id="a.2.1"></a>2.1 Rol de Entrada</u></strong>            </h3>
          <h3><br />
              <strong><a name="a.2.2" id="a.2.2"></a>2.2 Men&uacute; Entrada</strong><br />
              <strong><img width="232" height="129" src="img_manual_usuario/manual_usuario_clip_image006.jpg" alt="8" /></strong><strong> </strong></h3>
          <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
          </strong> <br />
            <strong><a name="a.2.3" id="a.2.3"></a>2.3 Pantalla de Inicio</strong> </h3>
          <h3><br />
              Por el contrario, si los datos ingresados fueron  correctamente validados la primera pantalla que el usuario podr&aacute; observar ser&aacute;  la siguiente.<br />
              <img width="588" height="239" src="img_manual_usuario/manual_usuario_clip_image008.jpg" alt="1" /></h3>
          <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
          </strong> </h3>
          <p>&nbsp;</p>
            <h3><strong><a name="a.2.4" id="a.2.4"></a>2.4 Generar Solicitud</strong></h3>
          <h3><br />
              <strong>Como generar una solicitud de Certificado de Antecedentes</strong><br />
              Para generar una solicitud de  antecedentes debemos encontrarnos en la pantalla que a continuaci&oacute;n se muestra  en la figura.</h3>
            <h3><img width="589" height="284" src="img_manual_usuario/manual_usuario_clip_image010.jpg" alt="2" /><strong> </strong><br />
              Primeramente debemos ingresar el  n&uacute;mero de <strong>DNI</strong> de la persona  solicitante y luego hacemos clic en el bot&oacute;n <strong>comprobar</strong>.<br />
              Esta pantalla tiene como objetivo  verificar la existencia de un ciudadano en el sistema. Si una persona solicita un  certificado de antecedentes e ingresamos su n&uacute;mero de DNI el sistema nos puede  arrojar dos respuestas posibles en funci&oacute;n de que la persona se encuentre  registrada o no.<br />
          Si&nbsp; ingresamos un n&uacute;mero de DNI que <strong>no se  encuentre registrado en el sistema</strong> (lo que significa que la persona  solicita por primera vez un certificado de antecedentes por medio del sistema),  la pantalla que podremos observar ser&aacute; la siguiente:</h3>
          <h3><br />
              <strong><img width="589" height="311" src="img_manual_usuario/manual_usuario_clip_image012.jpg" alt="3" /></strong><strong> </strong></h3>
            <h3>En el caso que haga clic en  &ldquo;Generar Solicitud&rdquo;, puede generar la solicitud y ser procesada, pero no se  producir&aacute; la carga a la base de datos hasta que llegue al proceso &ldquo;Confecci&oacute;n&rdquo;  donde se producir&aacute; la carga y/o actualizaci&oacute;n de los datos. <br />
              Los datos a llenar para generar  una solicitud:</h3>
            <h3><br />
              <img width="588" height="302" src="img_manual_usuario/manual_usuario_clip_image014.jpg" alt="4" /><br />
              </h3>
          <h3>Los campos (DNI, APELLIDO, NOMBRES, SOLICITADO POR) son requeridos.<br />
              Tipo de Prontuario y Nro de Prontuario, son opcionales (al momento de generar  la solicitud).</h3>
          <h3>Por el contrario si la persona <strong>se  encuentra registrada en el sistema</strong> (lo que significa que esta persona ya  solicit&oacute; alguna vez un certificado por medio del sistema) podremos observar la  siguiente pantalla que nos arroja como resultado los datos de la persona  solicitante.</h3>
            <h3><br />
              <img width="589" height="221" src="img_manual_usuario/manual_usuario_clip_image016.jpg" alt="5" /><br />
              </h3>
            <h3>Al hacer clic en &ldquo;Continuar&rdquo;,  podr&aacute; generar la solicitud, la cual se enviar&aacute; al proceso &ldquo;Archivo&rdquo; para ser  procesada. Los datos a llenar:</h3>
          <h3><br />
              <img width="589" height="299" src="img_manual_usuario/manual_usuario_clip_image018.jpg" alt="6" /></h3>
          <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
          </strong> </h3>
          <p>&nbsp;</p>
          <h3><strong><a name="a.2.5" id="a.2.5"></a>2.5 Ver Estado de  Solicitud:</strong></h3>
            <h3><br />
              En esta secci&oacute;n, podr&aacute; consultar  el estado de una solicitud, y el lugar donde se encuentra:<br />
              <img width="589" height="351" src="img_manual_usuario/manual_usuario_clip_image020.jpg" alt="7" /></h3>
            <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
            </strong> </h3>
            <h3><strong><u><a name="a.3.1" id="a.3.1"></a>3.1 Rol Archivo:</u></strong></h3>
            <h3><br />
              <strong><a name="a.3.2" id="a.3.2"></a>3.2 Men&uacute; Archivo</strong><br />
              <strong><img width="233" height="102" src="img_manual_usuario/manual_usuario_clip_image022.jpg" alt="2" /></strong><strong> </strong><br />
              En el margen izquierdo se encuentra el men&uacute; &ldquo;Archivo&rdquo;, los  usuarios pertenecientes a este rol, solo podr&aacute;n procesar las solicitudes  pendientes.</h3>
            <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
            </strong> <strong><br clear="all" />
            </strong>
            </h3>
            <h3><strong><a name="a.3.3" id="a.3.3"></a>3.3 Listar Solicitudes Pendientes</strong></h3>
          <h3><br />
              <img width="589" height="555" src="img_manual_usuario/manual_usuario_clip_image024.jpg" alt="1" /><br />
              Aqu&iacute; se listan las solicitudes generadas por mesa de  entrada. Una vez procesado el prontuario y en condiciones de ser confeccionado,  solo basta con hacer clic en &ldquo;Procesar&rdquo; para indicar que el prontuario se  encuentra en condici&oacute;n de ser confeccionado, creado y/o actualizado. </h3>
<h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
</strong> </h3>
          <h3><strong><u><a name="a.4.1" id="a.4.1"></a>4.1 Rol Confecci&oacute;n:</u></strong></h3>
            <h3><br />
              <strong><a name="a.4.2" id="a.4.2"></a>4.2 Men&uacute; Confecci&oacute;n:</strong><br />
              <img width="241" height="243" src="img_manual_usuario/manual_usuario_clip_image026.jpg" alt="sshot-1" /></h3>
            <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
            </strong> <br />
              <strong><a name="a.4.3" id="a.4.3"></a>4.3 Como imprimir un certificado solicitado</strong></h3>
          <h3><br />
              Para imprimir un certificado  solicitado se debe dirigir al men&uacute; <strong>Imprimir  Certificados</strong> y luego a la opci&oacute;n del mismo nombre, al hacer clic en dicha  opci&oacute;n obtendremos la siguiente pantalla.<br />
              <img width="589" height="295" src="img_manual_usuario/manual_usuario_clip_image028.jpg" alt="sshot-2" /></h3>
            <h3>A continuaci&oacute;n se listar&aacute;n las  solicitudes que est&eacute;n listas para ser procesadas, para encontrar una solicitud  en particular, ingresamos el <strong>DNI</strong> del  solicitante y procedemos a presionar el bot&oacute;n <strong>Buscar.</strong><br />
              Al hacer clic en &ldquo;Procesar&rdquo;, se abrir&aacute; el formulario de confecci&oacute;n,  donde actualizar&aacute; los datos de la persona, para luego imprimir el certificado.<br />
  <br />
          Datos del formulario:</h3>
          <h3><br />
              <img width="589" height="576" src="img_manual_usuario/manual_usuario_clip_image030.jpg" alt="sshot-3" /> <br />
              Todos los campos son requeridos, a excepci&oacute;n del campo &ldquo;Observaciones&rdquo;,  que en el caso que la persona no registre antecedentes, debe dejarlo en blanco.<br />
              Una vez completados y/o actualizados los datos, haga clic en &ldquo;Procesar  Solicitud&rdquo;.<br />
              Nos mostrar&aacute; la siguiente pantalla:<br />
              <img width="589" height="388" src="img_manual_usuario/manual_usuario_clip_image032.jpg" alt="sshot-4" /> <br />
              <img width="588" height="294" src="img_manual_usuario/manual_usuario_clip_image034.jpg" alt="sshot-5" /> <br />
              En el margen inferior izquierdo,  se encuentra un bot&oacute;n &ldquo;Imprimir Certificado&rdquo;, y nos permitir&aacute; imprimirlo:<br />
              <img width="584" height="685" src="img_manual_usuario/manual_usuario_clip_image036.jpg" alt="sshot-6" /></h3>
          <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
          </strong> </h3>
          <p>&nbsp;</p>
            <h3><strong><a name="a.4.4" id="a.4.4"></a>4.4 Como dar de alta a  un solicitante</strong> </h3>
            <h3><br />
              Para dar el alta a un nuevo solicitante al sistema deberemos  rellenar los campos de la siguiente pantalla que a continuaci&oacute;n se detallan:</h3>
            <h3><img width="589" height="519" src="img_manual_usuario/manual_usuario_clip_image038.jpg" alt="sshot-7" /><br />
              Una vez completado el formulario  se procede a presionar sobre el bot&oacute;n &ldquo;Agregar Solicitante&rdquo; y obtendremos la  confirmaci&oacute;n correspondiente.</h3>
            <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
            </strong> </h3>
            <p>&nbsp;</p>
            <h3><strong><a name="a.4.5" id="a.4.5"></a>4.5 Confeccionar una  solicitud donde la persona solicitante no se encuentra en la base de datos.</strong></h3>
            <h3><br />
              Puede darse la situaci&oacute;n que en  Mesa de Entrada, al verificar el dni de la persona solicitante, no se encuentre  en la base de datos. De igual manera, se produce la solicitud del certificado,  pero se cargar&aacute;n los datos de la persona reci&eacute;n cuando se produzca la  confecci&oacute;n del certificado.<br />
              Dado ese caso, veamos los pasos a  seguir para procesarlo:</h3>
            <h3>En       &ldquo;Imprimir Certificados&rdquo; veremos el ejemplo: DNI: 222333, Nombre: Juan,       Apellido: Perez. </h3>
<h3><img width="589" height="293" src="img_manual_usuario/manual_usuario_clip_image040.jpg" alt="sshot-8" /></h3>
            <h3><br />
              Al hacer clic en &ldquo;Procesar&rdquo;  veremos:</h3>
            <h3><br />
              <img width="589" height="225" src="img_manual_usuario/manual_usuario_clip_image042.jpg" alt="sshot-9" /><br />
              </h3>
            <h3>Nos indica que esa persona, no se  encuentra en el sistema, pero que podemos dar de alta haciendo clic en &ldquo;Alta  Solicitante&rdquo;. Al hacer clic lo llevar&aacute; al formulario de altas. Detalles ver  Pag. 15.<br />
              Una vez dado de alta en el  sistema, se podr&aacute; procesar de manera normal el certificado, para finalizar con  la impresi&oacute;n del mismo.</h3>
            <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
            </strong> <br clear="all" />
            </h3>
            <h3><strong><a name="a.4.6" id="a.4.6"></a>4.6 Certificados  Impresos</strong></h3>
          <h3><br />
              <img width="589" height="316" src="img_manual_usuario/manual_usuario_clip_image044.jpg" alt="sshot-10" /><br />
              En esta secci&oacute;n podr&aacute; listar y  buscar los certificados impresos.</h3>
          <h3><strong><a href="#up">Volver al Índice</a><br clear="all" />
          </strong> </h3>
          <p>&nbsp;</p>
            <h3><strong><a name="a.5" id="a.5"></a>5. Salir del Sistema</strong></h3>
            <h3><br />
              <img width="230" height="106" src="img_manual_usuario/manual_usuario_clip_image046.jpg" alt="Salir" /><br />
              </h3>
            <h3>En el men&uacute; de la izquierda, se  encuentra la secci&oacute;n &ldquo;Cerrar Sesi&oacute;n&rdquo;. Al hacer clic en &ldquo;Salir del Sistema&rdquo;,  produce que el usuario actualmente identificado, salga del sistema. En  consecuencia, la &uacute;nica forma que el usuario pueda volver a operar, es  ingresando nuevamente con su usuario y contrase&ntilde;a. </h3>
            <h3><strong><a href="#up">Volver al &Iacute;ndice</a><br clear="all" />
          </strong></h3>
            <p>&nbsp;</p>
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
