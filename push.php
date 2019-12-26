<?php
    echo '<title>Отправка</title>';
	header("Отправка");

	$title = $_POST['title'];
	$msgtext = $_POST['body'];
	$attachment = $_POST['img'];
	$sound = $_POST['sound'];
	$vibrate = $_POST['vibrate'];
	$urlEvent = $_POST['urlEvent'];
	
	$googleFcmKey = 'AAAAyK4rOx4:APA91bHy7lzQU0ZFjmA9PfhjDU7FPT8D0vd9uyKAiKwv4A9XNpoA1e45fk68pIozDIgYrjmkcVL5bWiRz-THR3locl4axxJyhMm_shLkLndMP9mfXgnl2K1IYDuxESQxr1JUxxIbmQhw';
	$to = '/topics/test';
	$url = 'https://fcm.googleapis.com/fcm/send';
	define( 'API_ACCESS_KEY', $googleFcmKey );
	
	$data = array
	(
	  'attachment-url' => $attachment,
	  'url-event' => $urlEvent
    );
	$msg = array
	(
		'body' 	=> $msgtext,
		'title'		=> $title,
		'vibrate'	=> $vibrate,
		'sound'		=> $sound
	);
	$fields = array
	(
		'to' => '/topics/test',
		"content_available" => true,
		"mutable_content" => true,
		'notification' => $msg,
		'data' => $data 
	);
	$headers = [
        'Authorization: key=AAAAyK4rOx4:APA91bHy7lzQU0ZFjmA9PfhjDU7FPT8D0vd9uyKAiKwv4A9XNpoA1e45fk68pIozDIgYrjmkcVL5bWiRz-THR3locl4axxJyhMm_shLkLndMP9mfXgnl2K1IYDuxESQxr1JUxxIbmQhw',
        'Content-Type: application/json'
    ];	
	
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
   	//var_dump($result);
   	echo '<header>Результат: ' . htmlspecialchars($result) . '</header>';
    echo '<p><a href="javascript:history.go(-1)" title="Вернуться назад">&laquo; Go back</a></p>';
    exit;
?>
