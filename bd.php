<?php

$sdb_name = "mysql.hostinger.ru";
$user_name = "u364987397_map";
$user_password = "123456";
$db_name = "u364987397_map";

// ���������� � �������� ���� ������
if(!$link = mysql_connect($sdb_name, $user_name, $user_password))
{
  echo "<br>�� ���� ����������� � �������� ���� ������<br>";
  exit();
}

// �������� ���� ������
if(!mysql_select_db($db_name, $link))
{
  echo "<br>�� ���� ������� ���� ������<br>";
  exit();
}

mysql_query('SET NAMES utf8');

?>
