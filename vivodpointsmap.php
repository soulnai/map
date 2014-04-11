<?php
header('Content-Type: text/html; charset=utf-8');
 
require ("bd.php");
 
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
 
$result = mysql_query("SELECT * FROM ymapapiv2_markers WHERE `Show`=1");
if(mysql_num_rows($result)>0)
{
while ($mar = mysql_fetch_array($result))
{
$json =  array(icontext=>$mar['iconText'], hinttext=>$mar['hintText'], balloontext=>$mar['balloonText'], styleplacemark=>$mar['stylePlacemark'], lat=>$mar['lat'], lon=>$mar['lon'], author=>$mar['Author'], id=>$mar['id']);
$markers[] = $json;
}
 
}
$points = array(markers=>$markers);
 
echo json_encode($points);
 
}
 
 
?>