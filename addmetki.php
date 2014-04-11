<?php

header('Content-Type: text/html; charset=utf-8');

include("bd.php");

require_once "html_filter_class.php";
		
	$tags_set = array(
		
		'h1'		=> array('id', 'class'),
		'h2'		=> array('id', 'class'),
		'h3'		=> array('id', 'class'),
		'h4'		=> array('id', 'class'),
		'h5'		=> array('id', 'class'),
		'h6'		=> array('id', 'class'),
		'b'			=> array('id', 'class'),
		'p'			=> array('id', 'class'),
		'span'		=> array('id', 'class'),
		'a'			=> array('id', 'class', 'href'),
		'img'		=> array('id', 'class', 'src', 'alt', FALSE),
		'br'		=> array(FALSE),
		'hr'		=> array(FALSE),
		
		'strong'		=> array('id', 'class'),	
		'div'		=> array('id', 'class', 'style'),		
		
		
		'ul'		=> array('id', 'class'),
		'ol'		=> array('id', 'class'),
		'li'		=> array('id', 'class'),
		
		'table'		=> array('id', 'class'),
		'tr'		=> array('id', 'class'),
		'td'		=> array('id', 'class'),
		'th'		=> array('id', 'class'),
		'thead'		=> array('id', 'class'),
		'tbody'		=> array('id', 'class'),
		'tfoot'		=> array('id', 'class')	
		
	);
	
	
	$html_filter = new html_filter();
	$html_filter->set_tags($tags_set);

$iconText = htmlspecialchars($_POST['icontext']);
$hintText = htmlspecialchars($_POST['hinttext']);
$balloonText = $html_filter->filter($_POST['balloontext']);
$stylePlacemark = $_POST['styleplacemark'];
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$Date = htmlspecialchars($_POST['date']);
$Time = htmlspecialchars($_POST['time']);
$Author = htmlspecialchars($_POST['author']);

$sql = "INSERT INTO ymapapiv2_markers (`id`, `iconText`, `hintText`, `balloonText`, `stylePlacemark`, `lat`, `lon`, `Date`, `Time`, `Author`, `Show`) VALUES (NULL, '$iconText', '$hintText', '$balloonText', '$stylePlacemark', '$lat', '$lon', STR_TO_DATE ('$Date', '%c/%e/%Y %r'), '$Time', '$Author', '1');";

$result = mysql_query($sql) or die("Ошибочный запрос: " . mysql_error());

?>