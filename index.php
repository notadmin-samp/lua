<!DOCTYPE html>
<head>
  <title>Auth</title>
  <meta charset="utf-8">
</head>
<body>
<?php
$mysqli = new mysqli("localhost", "f0373269_11", "1264508967U", "f0373269_11"); # 123 - user; 321- pass;
$mysqli->set_charset("utf8");
if ($mysqli->connect_errno){
$json_array = array("error" => 1, "message" => "Ошибка подключения к БД");
$json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
exit($json);
} else {
if (isset($_GET['code'])) {
    $query = "SELECT * FROM `users` WHERE CODE=".$_GET['code'];
    $sql = mysqli_query($mysqli,$query) or die(mysqli_error());
    if (mysqli_num_rows($sql) > 0) {
        $myrow = mysqli_fetch_array($sql);
        $date = date_create($myrow['date']);
        $curdate = date("d.m.Y");
        if (strtotime($curdate) < strtotime(date_format($date, 'd.m.y')))
        {
            $interval = date_diff($date, date_create($curdate));
            echo $interval->format("%d");
        } else {
            echo "The duration of the key has expired.";
        }
    } else {
        echo '-1';
    }
}
if (isset($_GET['getip'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    echo "IP: $ip";
}
if ((isset($_GET['iserial'])) and (isset($_GET['username']))) {
    $nickname = $_GET['username'];
    $serialn = $_GET['iserial'];
    $sql = mysqli_query($mysqli, "SELECT * FROM free_users WHERE serial_num = '$serialn'");
    if (mysqli_num_rows($sql) == 0) {
        $sql_query = mysqli_query($mysqli, "INSERT INTO `free_users`(`username`, `serial_num`) VALUES ('{$nickname}', '{$serialn}')");
        echo "Success.";
    } elseif (mysqli_num_rows($sql) > 0) {
        $sql_q = mysqli_query($mysqli, "UPDATE free_users SET username = $nickname WHERE serial_num = $serialn");
        echo "Updated.";
        "UPDATE `free_users` SET `username`=$nickname WHERE serial_num=$serialn"
    }
  }
}
if ((isset($_GET['newcode'])) and (isset($_GET['auth'])) and (isset($_GET['client']))) {
    $ccode = $_GET['newcode'];
    $auth = $_GET['auth'];
    $client = $_GET['client'];
    if ($client == "lua") {
        $auth2 = $_GET['2auth'];
        echo "penis";
    }
}
if ((isset($_GET['db'])) and (isset($_GET['from'])) and (isset($_GET['where'])) and (isset($_GET['where2'])) and (isset($_GET['row']))) {
    $db = $_GET['db'];
    $from = $_GET['from'];
    $where = $_GET['where'];
    $where2 = $_GET['where2'];
    $row = $_GET['row'];
    if ($db == "get") {
        $sql = mysqli_query($mysqli, "SELECT * FROM $from WHERE $where = '$where2'");
        if (mysqli_num_rows($sql) > 0) {
            $arow = mysqli_fetch_array($sql);
            $result = $arow[$row];
            if ($result) {
                echo "$result";
            } else {
                echo "-3";
            }
        } else {
            echo "-4";
        }
}
}
?>
</body>