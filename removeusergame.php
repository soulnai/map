<?php
include("bd.php");
$id = $_GET['id'];
echo $id;
$sql = "UPDATE `ymapapiv2_markers` SET `Show`='0' WHERE Id=".$id;

$result = mysql_query($sql) or die("Ошибочный запрос: " . mysql_error());

?>