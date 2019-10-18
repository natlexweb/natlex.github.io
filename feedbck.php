<?php
$recipient_email    = "info@natlex.ru"; //recepient
$from_email         = "info@natlex.ru"; //from email using site domain.
$subject            = "Новое сообщение с сайта Natlex.ru";


if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    die('Sorry Request must be Ajax POST'); //exit script
}

if($_POST){

    $sender_name    = filter_var($_POST["name"], FILTER_SANITIZE_STRING); //capture sender name
    $sender_email   = filter_var($_POST["email"], FILTER_SANITIZE_STRING); //capture sender email
    $message        = filter_var($_POST["message"], FILTER_SANITIZE_STRING); //capture message
    $message .= "\n Имя: " . $sender_name;
    $message .= "\n Емейл: " . $sender_email;

    //php validation
    if(strlen($sender_name)<2){ // If length is less than 2 it will output JSON error.
        print json_encode(array('type'=>'error', 'text' => 'Имя не должно быть короче 2-х символов'));
        exit;
    }
    if(!filter_var($sender_email, FILTER_VALIDATE_EMAIL)){ //email validation
        print json_encode(array('type'=>'error', 'text' => 'Пожалуйста, укажите корректный адрес электронной почты.'));
        exit;
    }
    if(strlen($message)<3){ //check emtpy message
        print json_encode(array('type'=>'error', 'text' => 'Сообщение не содержит какой-либо ценной информации и слишком лаконично. Попробуйте добавить содержания.'));
        exit;
    }


    $boundary = md5("natlex.ru");

    $headers = "From:".$from_email."\r\n".
     "Reply-To: ".$sender_email. "\n" .
     "X-Mailer: PHP/" . phpversion();
     $body = $message;

    $sentMail = mail($recipient_email, $subject, $body, $headers);
    if($sentMail) //output success or failure messages
    {
        print json_encode(array('type'=>'done', 'text' => 'Сообщение отправлено, благодарим!'));
        exit;
    }else{
        print json_encode(array('type'=>'error', 'text' => 'Ошибка отправки сообщения, проверьте конфигурацию PHP.'));
        exit;
    }
}
?>
