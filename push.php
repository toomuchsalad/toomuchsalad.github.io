<?php
// Server file
class PushNotifications {

    // (Android)API access key from Google API's Console.
    private static $API_ACCESS_KEY = 'AAAAyK4rOx4:APA91bHy7lzQU0ZFjmA9PfhjDU7FPT8D0vd9uyKAiKwv4A9XNpoA1e45fk68pIozDIgYrjmkcVL5bWiRz-THR3locl4axxJyhMm_shLkLndMP9mfXgnl2K1IYDuxESQxr1JUxxIbmQhw';
    // (iOS) Private key's passphrase.
    private static $passphrase = 'all';
    // (Windows Phone 8) The name of our push channel.
    private static $channelName = "all";

    // Change the above three vriables as per your app.

    public function __construct() {
        exit('Init function is not allowed');
    }

    // Sends Push notification for Android users
    public function android($data, $reg_id) {
        $url = 'https://android.googleapis.com/gcm/send';
        $message = array(
            'title' => $data['mtitle'],
            'message' => $data['mdesc'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1
        );

        $headers = array(
            'Authorization: key=' .self::$API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $fields = array(
            'registration_ids' => array($reg_id),
            'data' => $message,
        );

        return $this->useCurl($url, $headers, json_encode($fields));
    }

    // Sends Push notification for iOS users
    public function iOS($data, $devicetoken) {

        $deviceToken = 'emgHubN3C8o:APA91bGj7UsYsalWSy7lzLeP7ClFWJN1SkO-916sXAZMtDWVHz7XcCcq19RV0s2Amkl4TMfs0XN4VP4FqK1D6pC81xKyAHc5Lq20RklqNJk6KA6H6Bg6O6poVbYXv2lwF--ktyRwEHfy';

        $ctx = stream_context_create();
        // ck.pem is your certificate file
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', self::$passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://gateway.sandbox.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'title' => $data['mtitle'],
                'body' => $data['mdesc'],
            ),
            'sound' => 'default'
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);

        if (!$result)
            return 'Message not delivered' . PHP_EOL;
        else
            return 'Message successfully delivered' . PHP_EOL;

    }

    // Curl
    private function useCurl(&$model, $url, $headers, $fields = null) {
        // Open connection
        $ch = curl_init();
        if ($url) {
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if ($fields) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }

            // Execute post
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);

            return $result;
        }
    }

}
?>
