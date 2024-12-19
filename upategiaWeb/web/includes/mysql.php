<?php

include('conf.php');

//$conx = mysqli_connect($mysql_host, $mysql_user, $mysql_pass);

$conx = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);

//mysqli_select_db($conx, $mysql_db);

?>
