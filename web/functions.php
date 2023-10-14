<?php
//データーベースに接続する
function connect_db() {
  $param = 'mysql:dbname='.DB_NAME.';host='.DB_HOST;
  $pdo = new PDO($param, DB_USER, DB_PASSWORD);
  $pdo->query('SET NAMES utf8');
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
  return $pdo;
}

//日付を日(曜日)の形式に変換する
function time_format_dw($date) {
  $format_date = NULL;
  $week = array('日','月','火','水','木','金','土');

  if ($date) {
    $format_date = date('j('.$week[date('w', strtotime($date))].')',strtotime($date));
  }

  return $format_date;
}

//時間のデータ形式を調整する
function format_time($value) {
  if (!$value || $value == '00:00:00') {
    return NULL;
  } else {
    return date('H:i', strtotime($value));
  }
}

?> 