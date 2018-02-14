<?php
//header("Content-type:text/html;charset=utf-8");
set_time_limit(0);
ini_set('date.timezone', 'Asia/Shanghai');


$sever = "http://localhost/jingyi/index.php/Home/AirStops/findusertostop";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sever);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_REFERER, 'http://google.com/');
curl_setopt($ch, CURLOPT_POSTFIELDS, NULL);
$return = curl_exec($ch);
curl_close($ch);
$temp = json_decode($return, true);
$cont = date("Y-m-d H:i:s")  . "运行成功;\r\n";
$fp = fopen('C:\wamp1\www\jingyi\airshipstop.txt', 'a+');
fwrite($fp, $cont);
fclose($fp);
