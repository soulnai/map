<?php

$sdb_name = "mysql.hostinger.ru";
$user_name = "u364987397_map";
$user_password = "123456";
$db_name = "u364987397_map";

// соединение с сервером базы данных
if(!$link = mysql_connect($sdb_name, $user_name, $user_password))
{
  echo "<br>Не могу соединиться с сервером базы данных<br>";
  exit();
}

// выбираем базу данных
if(!mysql_select_db($db_name, $link))
{
  echo "<br>Не могу выбрать базу данных<br>";
  exit();
}

mysql_query('SET NAMES utf8');

?>
