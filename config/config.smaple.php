<?php

$C["domain"] = 'https://sp.tnfsh.tn.edu.tw';
$C["path"] = '/teacherinfo';
$C["sitename"] = '公民科教師資料填報';
$C["titlename"] = '公民教師資料';

$C["DBhost"] = 'localhost';
$C["DBuser"] = 'user';
$C["DBpass"] = 'pass';
$C["DBname"] = 'dbname';

$C["cookiename"] = 'teacherinfo';
$C["cookieexpire"] = 86400*7;

$C["CAPTCHAuselogin"] = false;
$C["CAPTCHAuseschool"] = false;
$C["CAPTCHAuseteacher"] = false;
$C["CAPTCHAsitekey"] = '';
$C["CAPTCHAsecretkey"] = '';

$C["mail"] = 'no-reply@sp.tnfsh.tn.edu.tw';

$C["ipserverkey"] = array("HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_X_CLUSTER_CLIENT_IP", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR", "HTTP_VIA");

$G["db"] = new PDO ('mysql:host='.$C["DBhost"].';dbname='.$C["DBname"].';charset=utf8', $C["DBuser"], $C["DBpass"]);
$G["schoolyear"] = date("Y")-1911-(date("m")<=8);
$G["confirm"] = array("未確認", "已確認", "錯誤");
$G["inuse"] = array("隱藏", "顯示");

$G["csvmime"] = array("application/vnd.ms-excel", "text/comma-separated-values");

date_default_timezone_set("Asia/Taipei");

require("func/check_login.php");
