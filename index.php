<?php

// Programa para Contar visitas y obtener IP del Visitante,
// mediante una Imagen con las visitas, generada automaticamente

// ::  Obtiene el ID del Contador de visitas, por GET
$id = $_GET['id'];
$key = $_GET['key'];

// :: Obtiene los Valores RGB si se esta personalizando.

// Si han sido establecidos los Colores personalizados, usarlos.
// Si no, usar los Predeterminados.
if(((is_numeric($_GET['TE_R']) && is_numeric($_GET['TE_G']) && is_numeric($_GET['TE_B'])) ||
   (is_numeric($_GET['BG_R']) && is_numeric($_GET['BG_G']) && is_numeric($_GET['BG_B'])) &&
   ($_GET['TE_R'] <= 255 && $_GET['TE_G'] <= 255 && $_GET['TE_B'] <= 255) ||
   ($_GET['BG_R'] <= 255 && $_GET['BG_G'] <= 255 && $_GET['BG_B'] <= 255))){

    // Color de fondo
    $bg_red = $_GET['BG_R'];
    $bg_green = $_GET['BG_G'];
    $bg_blue = $_GET['BG_B'];

    // Color de Texto
    $te_red = $_GET['TE_R'];
    $te_green = $_GET['TE_G'];
    $te_blue = $_GET['TE_B'];

}else{

    // Color de fondo
    $bg_red = 100;
    $bg_green = 220;
    $bg_blue = 100;

    // Color de Texto
    $te_red = 50;
    $te_green = 128;
    $te_blue = 50;
}

// :: Pregunta si X e Y son establecidos. Si no, usar los valores por defecto

if(is_numeric($_GET['X'])) $x = $_GET['X'];
else $x = 5;
if(is_numeric($_GET['Y'])) $y = $_GET['Y'];
else $y = 27;

if(is_numeric($_GET['FONTSIZE'])) $font_size = $_GET['FONTSIZE'];
else $font_size = 22;

$font = "animeace.ttf";


// Pregunta si es una Imagen personalizada
$custom_image = $_GET['IMAGE'];

// :: Pregunta si ha sido establecido el ID. De ser asi, ejecutar la aplicacion principal
if(isset($id)&&!isset($key)) {

    // Obtener informacion del Cliente
    $timestamp = time();
    $remote_addr = $_SERVER['REMOTE_ADDR'];
    $remote_port = $_SERVER['REMOTE_PORT'];
    $useragent = $_SERVER['HTTP_USER_AGENT'];

    // Evitar conteo de Impresiones desde el Instituto
    if($remote_addr != "200.75.14.3"){

        // Usar las funciones de FILE

        // Abre el archivo en modo APPEND
        $csv_file = @fopen('counter/' . $id . '.csv','a') or die();

        // Dar formato a la Salida
        $output = "$timestamp;";
        $output.= "$remote_addr;";
        $output.= "$remote_port;";
        $output.= "\"$useragent\"\n";

        // Bloquear el Archivo de salida, escribirlo y Cerrarlo
        @flock($csv_file,'w'); @fwrite($csv_file,$output); @fclose($csv_file);
    }

    // :: Utilizando la libreria GD para obtener el numero de Visitas.

    // Cabeceras
    header("Expires: Mon, 11 May 2009 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
    header("cache-Control: no-store, no-cache, must-revalidate");
    header("cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");  
    header('Content-type: image/png');

    // Obtener numero de lineas del Archivo
    $csv_file_set = "counter/$id.csv";
    $csv_lines = count(file($csv_file_set)); 

    // Generar Imagen. Si existe una Imagen PNG subida por el Usuario, usar esa

    if(file_exists("images/$id")) $imagen = imagecreatefrompng("images/$id");
    else $imagen = imagecreatetruecolor(180,30);

    $bgcolor = imagecolorallocate($imagen,$bg_red,$bg_green,$bg_blue);  
    $text_color = imagecolorallocate($imagen,$te_red,$te_green,$te_blue);

    if(!file_exists("images/$id"))imagefilledrectangle ($imagen,0,0,180,30,$bgcolor);
    imagettftext($imagen,$font_size,0,$x,$y, $text_color,"counter/$font",$csv_lines);

    // Dar salida a la Imagen    
    imagepng($imagen);    
    imagedestroy($imagen); 

// :: Enviar formulario por Email

}elseif(isset($_GET['MAIL'])){

    $email = $_POST['mail'];
    $id = $_POST['id'];
    $key = $_POST['key'];

    // validar el Email con Exprsiones regulares
    if(preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/',$email)){

		$header = "From: Contador de visitas <mail@contadorvistas.mywebcomunity.org>\n";

		$output = "<style type=\"text/css\">";
		$output.= "body {font-family:\"Comic Sans MS\",Arial,sans-serif;font-size:12px}div{margin:10px 0;}img{border:0;}</style>";
		$output.= "<h1>Contador de visitas gratis</h1>";
		$output.= "<div>Este Contador de visitas puedes ponerlo facilmente en tu p&aacute;gina Web. ";
		$output.= "Sin Scripts ni c&oacute;digos raros, as&iacute; de sencillo.</div>";
		$output.= "<div><strong>Tu ID:</strong> ". $id . "</div>";
		$output.= "<div><strong>La URL de tu Contador:</strong> ";
		$output.= "http://contadorvisitas.mywebcommunity.org/index.php?id=". $id . "</div>";
		$output.= "<div>Copia y pega el siguiente c&oacute;digo en tu Web como Imagen:<br />";
		$output.= "<span>&lt;img src=\"http://contadorvisitas.mywebcommunity.org/index.php?id=$id\" /&gt;</span>";
		$output.= "</div>";
		$output.= "<div>";
		$output.= "<strong>Desde la siguiente URL puedes visualizar las Visitas:</strong><br />";
		$output.= "<span>http://contadorvisitas.mywebcommunity.org/index.php?id=$id&amp;key=$key</span>";
		$output.= "</div>";
		$output.= "<div>";
		$output.= "<strong>Puedes descargar el Informe CSV sin formato, desde la siguiente URL:</strong><br />";
		$output.= "<span>http://contadorvisitas.mywebcommunity.org/index.php?id=$id&amp;key=$id&amp;CSV</span>";
		$output.= "</div>";
		$output.= "<div><strong>Por favor, guarda estos datos, ya que si los pierdes, NO podr&aacute;s recuperarlos y deber&aacute;s solicitar un nuevo Contador.</strong></div>";
		$output.= "<div>Este mensaje ha sido enviado autom&aacute;ticamente y no recibe respuestas. Si lo recibiste por error o crees que es Spam, s&oacute;lo ign&oacute;ralo.</div>";
	
	mail($email,"Contador de visitas",$output,$header);
        header("location: " . $_SERVER['PHP_SELF'] . "?OK");

    }else header("location:" . $_SERVER['PHP_SELF'] . "?INCORRECT");

// OK
}elseif(isset($_GET['OK'])){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contador de visitas</title>

<style type="text/css">
body {
  font-family:"Comic Sans MS",Arial,sans-serif;
  font-size:13px
}
div {
  margin:10px 0;
}
img {
  border:0;
}
</style>

</head>
<body>
<h1>Contador de visitas gratis</h1>

<div>Corr&eacute;o enviado exitosamente. Revisa en la Bandeja de entrada y tambien en Spam.</div>

<div><a href="<?= $_SERVER['PHP_SELF'] ?>">Volver</a></div>

</body>
</html><?php

// ::  Obtener el contenido del registro, mediante una Clave que no es mas que el MD5 del ID, reverso

// En formato CSV
}elseif(isset($id) && isset($key) && isset($_GET['CSV'])){

    if($key == md5(strrev($id)) && file_exists("counter/$id.csv")){
      header('Content-type: text/csv');
      header('Content-Disposition: attachment; filename="visitas.csv"');
      echo file_get_contents("counter/$id.csv");
    }


// En linea, formateado con tablas
}elseif(isset($id) && isset($key) && !isset($_GET['CSV'])){

    if($key == md5(strrev($id)) && file_exists("counter/$id.csv")){

        $csv_file_set = "counter/$id.csv";
        $csv_lines = count(file($csv_file_set)); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contador de visitas: Reporte de Visitas</title>

<style type="text/css">
body {
  font-family:"Comic Sans MS",Arial,sans-serif;
  font-size:13px;
}
div {
  margin:10px 0;
}
th {
  background:#BBBBBB;
  font-weight:bold;
}
td {
  padding: 5px !important;
  border: 1px #000000 dotted !important;
}
table {
  border: 1px #000000 solid;
  font-family:"Courier New", Courier, monospaced !important;
}
</style>

</head>
<body>
<h1>Contador de visitas. Reporte de visitas</h1>

<div>Aqu&iacute; puedes ver las Visitas de tu Contador, el total, y los detalles.</div>

<div><strong>Tu ID:</strong> <?= $id ?></div>

<div><strong>La URL de tu Contador:</strong> http://contadorvisitas.mywebcommunity.org/index.php?id=<?= $id ?></div>

<div style="font-size:18px"><strong>Total de Visitas:</strong> <?= $csv_lines ?></div>

<div><strong>Detalles</strong></div>

<div>

<table style="float:center">
<tr>
<th>N&ordm;</th>
<th>Fecha y hora</th>
<th>Direcci&oacute;n IP</th>
<th>Puerto</th>
<th>User agent</th>
</tr>
<?php

$row = 0;
$csv_table = fopen("counter/$id.csv", "r");
while (($data = fgetcsv($csv_table, 100000, ";")) !== FALSE) {
    $num = count($data);
    $row++;
    echo "<tr>\n";
    echo "<td>$row</td>\n";
    echo "<td style=\"width:160px\">" . date("d/m/Y H:i:s",$data[0]) . "</td>\n";
    echo "<td><a href=\"http://network-tools.com/default.asp?prog=trace&host=" . $data[1] . "\" title=\"Traceroute a la direcci&oacute;n IP\">" . $data[1] . "</a></td>\n";
    echo "<td>" . $data[2] . "</td>\n";
    echo "<td>" . substr($data[3],0,80) . "...</td>\n";
    echo "</tr>\n";
}
fclose($csv_table);

?>
</table>
</div>

<div>
<strong>Puedes descargar el Informe completo en CSV sin formato, desde la siguiente URL:</strong><br />
<a href="http://contadorvisitas.mywebcommunity.org/index.php?id=<?= $id ?>&amp;key=<?= $key ?>&amp;CSV">
http://contadorvisitas.mywebcommunity.org/index.php?id=<?= $id ?>&amp;key=<?= $key ?>&amp;CSV</a>
</div>

<div>Copia y pega el siguiente c&oacute;digo en tu Web como Imagen:<br />
<span>&lt;img src="http://contadorvisitas.mywebcommunity.org/index.php?id=<?= $id ?>" /&gt;</span>
</div>

<div>Por favor, guarda estos datos, ya que si los pierdes, NO podr&aacute;s recuperarlos y deber&aacute;s solicitar un nuevo Contador.</div>

</body>
</html><?php
    }

// Generar una pagina con indicaciones para Colocar la Imagen
// en la pagina donde queremos contar las Visitas.
// La pgina HTML DEBE estar Fuera del codigo PHP para mayor comodidad.
// NO olvideoms cerrar el Condicional!

}else{

// Declara el Timestamp, para generar el ID con MD5
$timestamp = time();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contador de visitas</title>

<style type="text/css">
body {
  font-family:"Comic Sans MS",Arial,sans-serif;
  font-size:13px
}
div {
  margin:10px 0;
}
img {
  border:0;
}
</style>

</head>
<body>
<h1>Contador de visitas gratis</h1>

<div>Este Contador de visitas puedes ponerlo facilmente en tu p&aacute;gina Web. Sin Scripts ni c&oacute;digos raros, as&iacute; de sencillo.</div>

<div><strong>Tu ID:</strong> <?= md5($timestamp); ?></div>

<div><strong>La URL de tu Contador:</strong> http://contadorvisitas.mywebcommunity.org/index.php?id=<?= md5($timestamp); ?></div>

<div>Copia y pega el siguiente c&oacute;digo en tu Web como Imagen:</div>
<div style="margin:10px">&lt;img src="http://contadorvisitas.mywebcommunity.org/index.php?id=<?= md5($timestamp); ?>" /&gt;</div>

<div>
<strong>Desde la siguiente URL puedes visualizar las Visitas:</strong><br />
<span>http://contadorvisitas.mywebcommunity.org/index.php?id=<?= md5($timestamp); ?>&amp;key=<?= md5(strrev(md5($timestamp))); ?></span>
</div>

<div>
<strong>Puedes descargar el Informe CSV sin formato, desde la siguiente URL:</strong><br />
<span>http://contadorvisitas.mywebcommunity.org/index.php?id=<?= md5($timestamp); ?>&amp;key=<?= md5(strrev(md5($timestamp))); ?>&amp;CSV</span>
</div>
<?php /*
<div>

<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>?MAIL">

<p>Puedes enviar este Contador a tu Corr&eacute;o electr&oacute;nico. Aseg&uacute;rate de ingresarla correctamente.</p>

<div>
<p><? if(isset($_GET['INCORRECT'])) {  ?><span style="font-size:10px;color:#FF0000">Por favor escribe correctamente tu direcci&oacute;n de Email:</span>
<? }else { ?>Direcci&oacute;n de email:<? } ?>
<br />
<input type="text" name="mail" /></p>
<input type="submit" />
<input type="hidden" name="id" value="<?= md5($timestamp); ?>" />
<input type="hidden" name="key" value="<?= md5(strrev(md5($timestamp))); ?>" />
</div>

<p>No almacenaremos y publicaremos tu direcc&oacute;n, y no se te enviar&aacute; ningun otro mensaje.</p>

</form>
</div>
*/ ?>
<div><strong>Por favor, guarda estos datos, ya que si los pierdes, NO podr&aacute;s recuperarlos y deber&aacute;s solicitar un nuevo Contador.</strong></div>

<div>
    <a href="http://validator.w3.org/check?uri=referer"><img
        src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a>
</div>

</body>
</html><?php

} // NO OLVIDEMOS CERRAR EL CONDICIONAL!!!

?>
