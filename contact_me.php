<?php
$recipient_email    = "info@natlex.ru"; //recepient
$from_email         = "info@natlex.ru"; //from email using site domain.


 if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
     die('Sorry Request must be Ajax POST'); //exit script
 }

if($_POST){

    $sender_name = filter_var($_POST["name"], FILTER_SANITIZE_STRING); //capture sender name
    $phone_number = filter_var($_POST["phone"], FILTER_SANITIZE_NUMBER_INT);
    $subject_type = filter_var($_POST["jobtype"], FILTER_SANITIZE_STRING);
    $subject .= "\n Поступила новая вакансия: " . $subject_type;

    $message = "\n Посетитель сообщает следующее: " . filter_var($_POST["message"], FILTER_SANITIZE_STRING); //capture message
    $message .= "\n Имя: " . $sender_name;
    $message .= "\n Телефон: " . $phone_number;

    $attachments = $_FILES['file_attach'];


    //php validation
    if(strlen($sender_name)<2){ // If length is less than 4 it will output JSON error.
        print json_encode(array('type'=>'error', 'text' => 'Имя не должно быть короче 2-х символов'));
        exit;
    }
    if(!filter_var($phone_number, FILTER_SANITIZE_NUMBER_FLOAT)){ //check for valid numbers in phone number field
        print json_encode(array('type'=>'error', 'text' => 'Ошибка при вводе номера телефона'));
        exit;
    }
    if(strlen($message)<3){ //check emtpy message
        print json_encode(array('type'=>'error', 'text' => 'Сообщение не содержит какой-либо ценной информации и слишком лаконично. Попробуйте добавить содержания.'));
        exit;
    }


    $file_count = count($attachments['name']); //count total files attached
    $boundary = md5("natlex");

    if($file_count > 0){ //if attachment exists
        //header
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From:".$from_email."\r\n";
        $headers .= "Reply-To: ".$sender_email."" . "\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n";

        //message text
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($message));

        //attachments
        for ($x = 0; $x < $file_count; $x++){
            if(!empty($attachments['name'][$x])){

                if($attachments['error'][$x]>0) //exit script and output error if we encounter any
                {
                    $mymsg = array(
                    1=>"Увы, размер загружаемого файла превышает допустимый предел.",
                    2=>"Увы, размер загружаемого файла превышает допустимый предел для формы обратной связи",
                    3=>"Ошибка загрузки файла на сервер",
                    4=>"Ничего не загружено",
                    6=>"Отсутствует temporary folder" );
                    print  json_encode( array('type'=>'error',$mymsg[$attachments['error'][$x]]) );
					exit;
                }

                //get file info
                $file_name = $attachments['name'][$x];
                $file_size = $attachments['size'][$x];
                $file_type = $attachments['type'][$x];

                //read file
                $handle = fopen($attachments['tmp_name'][$x], "r");
                $content = fread($handle, $file_size);
                fclose($handle);
                $encoded_content = chunk_split(base64_encode($content)); //split into smaller chunks (RFC 2045)

                $body .= "--$boundary\r\n";
                $body .="Content-Type: $file_type; name=".$file_name."\r\n";
                $body .="Content-Disposition: attachment; filename=".$file_name."\r\n";
                $body .="Content-Transfer-Encoding: base64\r\n";
                $body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n";
                $body .= $encoded_content;
            }
        }

    }else{ //send plain email otherwise
       $headers = "From:".$from_email."\r\n".
        "Reply-To: ".$sender_email. "\n" .
        "X-Mailer: PHP/" . phpversion();
        $body = $message;
    }

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
