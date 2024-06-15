<?php
// CORS ヘッダーを設定し、クロスオリジンアクセスを許可する
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// 休日一覧を表示
$holidaysURL = '/holidays.txt';
echo file_get_contents($holidaysURL);