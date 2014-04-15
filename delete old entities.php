<?php
include("bd.php");

$sql = "DELETE  FROM `ymapapiv2_markers` WHERE GameDate < (NOW() - INTERVAL 30 DAY)";

$result = mysql_query($sql) or die("Ошибочный запрос: " . mysql_error());

?>