<?php

$data = json_decode(file_get_contents('php://input'));
$public_id = "196755690";
$token = "00e9bebce8da007f156cec6c2cbdc6030b39f29ea9a58ed0a4e681e4c064db2cb22d84565c6826aef1c61";
$confirmationToken = "f64cf487";
$DBLink = mysqli_connect("localhost", "f0373269_11", "1264508967U", "f0373269_11");
mysqli_set_charset($DBLink, "utf8");

switch ($data->type) {
  case 'confirmation':
    echo $confirmationToken;
    break;
  case 'message_new':
    $from_id = $data->object->from_id;
    $peer_id = $data->object->peer_id;

    $message_id = $data->object->conversation_message_id;
    $bot_message = mysqli_query($DBLink, "SELECT * FROM bot_message WHERE (`peer_id` = '$peer_id') and (`message_id` = '$message_id')");
    if (mysqli_num_rows($bot_message) == 0) {
      mysqli_query($DBLink, "INSERT INTO `bot_message` (`peer_id`, `message_id`) VALUES ('{$peer_id}', '{$message_id}')");
    } else {
      die('ok');
    }


    $id = $data->object->from_id;
    $reply_id = $data->object->reply_message->from_id;
    $user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$from_id}&access_token={$token}&v=5.87"));
    $first_name = $user_info->response[0]->first_name;
    $last_name = $user_info->response[0]->last_name;
    $chat_act = $data->object->action;
    $date = date("d.m.Y H:i");
    $text = $data->object->text;
    $message = mb_strtolower($data->object->text, "utf-8");
    $cmd = explode(" ", $message);

    switch ($cmd[0]) {
      case 'send':
        if (!$cmd[2]) {
          sendvk("💬 Формат: send [ник или серийный номер] [сообщение].", $peer_id, $token);
          break;
        };
        $sender_format = "$first_name $last_name [id$id]";
        $qsql = mysqli_query($DBLink, "SELECT * FROM free_users WHERE username = $cmd[1]");
        if (mysqli_num_rows($qsql) > 0) {
            mysqli_query($DBLink, "UPDATE `vk_lastmessage` SET `last`={$cmd[2]},`sender`={$sender_format},`recipient`={$cmd[1]} WHERE id = '1'");
            sendvk("💬 Отправлено сообщение для $cmd[1]. Текст: $cmd[2]", $peer_id, $token);
        } else {
            $qsql = mysqli_query($DBLink, "SELECT * FROM free_users WHERE serial_num = $cmd[1]");
            if (mysqli_num_rows($qsql) > 0) {
                mysqli_query($DBLink, "UPDATE `vk_lastmessage` SET `last`={$cmd[2]},`sender`={$sender_format},`recipient`={$cmd[1]} WHERE id = '1'");
                $nickname = mysqli_query($DBLink, "SELECT * from free_users WHERE serial_num = $cmd[1]");
                $row = mysqli_fetch_array($nickname);
                $nickname = $row['username'];
                sendvk("💬 Отправлено сообщение для $nickname. Текст: $cmd[2]", $peer_id, $token);
            } else {
                sendvk("💬 Пользователь не найден. Возможно, вы указали неверный ник, используйте серийный номер ПК получателя.", $peer_id, $token);
                break;
            }
        }
        break;
       case 'getinfo':
        if ((!$cmd[2] and ($id != 432184775))) {sendvk("💬 Формат: getinfo [ник] [ключ авторизации]", $peer_id, $token); break;};
        if (($cmd[2] != "12645_key__" and ($id != 432184775))) {sendvk("💬 Невалидный ключ авторизации.", $peer_id, $token); break;};
        $q_sql = mysqli_query($DBLink, "SELECT * FROM free_users WHERE serial_num = $cmd[1]");
        if (mysqli_num_rows($q_sql) > 0) {
            $row = mysqli_fetch_array($q_sql);
            $username = $row['username'];
            sendvk("💬 Информация:\n💬 Зарегистрирован через: SA-MP.\n💬 Ник при регистрации: $username\n💬 Серийный номер ПК: $cmd[1]", $peer_id, $token);
        } else {
            $result = ucwords($cmd[1], "_");
            sendvk("$result", $peer_id, $token);
            $q_sql = mysqli_query($DBLink, "SELECT * FROM free_users WHERE username = $result");
            if (mysqli_num_rows($q_sql) > 0) {
                $row = mysqli_fetch_array($q_sql);
                $userserial = $row['serial_num'];
                sendvk("💬 Информация:\n💬 Зарегистрирован через: SA-MP.\n💬 Ник при регистрации: $cmd[1]\n💬 Серийный номер ПК: $userserial", $peer_id, $token);
            } else {
                sendvk("💬 Пользователь не найден.", $peer_id, $token);
            }
        }
        break;
       case 'users':
        if ($id != 432184775) {sendvk("💬 Вы не Егор Хакин.", $peer_id, $token); break;};
        if (($cmd[1]) and ($cmd[1] = "remove") and ($cmd[2])) {
            $resullt = ucwords($cmd[2], "_");
            $ssql = mysqli_query($DBLink, "SELECT * FROM free_users WHERE username = $resullt");
            if (mysqli_num_rows($ssql) > 0) {
                $roww = mysqli_fetch_array($ssql);
                $serrial = $roww['serial_num'];
                mysqli_query($DBLink, "DELETE FROM `free_users` WHERE username = $resullt");
                sendvk("💬 Удалена строка пользователя $resullt.\n\n💬 Зарегистрирован через: SA-MP.\n💬 Ник при регистрации: $resullt\n💬 Серийный номер ПК: $serrial", $peer_id, $token);
                break;
            } else {
                $ssql = mysqli_query($DBLink, "SELECT * FROM free_users WHERE serial_num = $cmd[2]");
                if (mysqli_num_rows($ssql) > 0) {
                    $roww = mysqli_fetch_array($ssql);
                    $user_name = $roww['username'];
                    $user_name = ucwords($user_name, "_");
                    mysqli_query($DBLink, "DELETE FROM `free_users` WHERE serial_num = $cmd[2]");
                    sendvk("💬 Удалён пользователь $resullt.\n\n💬 Зарегистрирован через: SA-MP.\n💬 Ник при регистрации: $user_name\n💬 Серийный номер ПК: $cmd[2]", $peer_id, $token);
                    break;
                } else {sendvk("💬 Пользователь не найден.", $peer_id, $token); break;}
            }
        } elseif (($cmd[1]) and ($cmd[1] = "remove") and (!$cmd[2])) {sendvk("💬 Формат: users remove [ник или серийный номер]", $peer_id, $token); break;}
        $resultt = mysqli_query($DBLink, "SELECT * FROM free_users");
        if (mysqli_num_rows($resultt) > 0) {
            $msg_acc .= "Найдено " . mysqli_num_rows($resultt) . " аккаунтов.\n";
            while ($row = mysqli_fetch_assoc($resultt)) {
                $msg_acc .= "\nНик: " . $row['username'].", ";
                $msg_acc .= "серийный номер: " . $row['serial_num'];
            }
            sendvk("💬 [id$id|$first_name,]\n" . $msg_acc, $peer_id, $token);
        } else {sendvk("💬 Не найдено аккаунтов.", $peer_id, $token); break;}
    }
    echo "ok";
}


function sendvk($messages, $peer_id, $token)
{
  $request_params = array(
    'message' => $messages,
    'peer_id' => $peer_id,
    'access_token' => $token,
    'v' => '5.87'
  );
  $get_params = http_build_query($request_params);
  file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
}