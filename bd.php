<?php

$sdb_name = "mysql.hostinger.ru";
$user_name = "u364987397_map";
$user_password = "123456";
$db_name = "u364987397_map";

// ñîåäèíåíèå ñ ñåðâåðîì áàçû äàííûõ
if(!$link = mysql_connect($sdb_name, $user_name, $user_password))
{
  echo "<br>Íå ìîãó ñîåäèíèòüñÿ ñ ñåðâåðîì áàçû äàííûõ<br>";
  exit();
}

// âûáèðàåì áàçó äàííûõ
if(!mysql_select_db($db_name, $link))
{
  echo "<br>Íå ìîãó âûáðàòü áàçó äàííûõ<br>";
  exit();
}

mysql_query('SET NAMES utf8');

?>
