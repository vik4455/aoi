<?php
require_once('./vendor/autoload.php');
// Namespace
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\MessageBuilder\LocationMessageBuilder;

$channel_token = 'at4J6/KfxIO+z/a+guttssKTJ1gC4qbIxvholeGygfe2LYGF1J8mfgoaxIaxb85vZQi5I8WZBRFsyrhdLy5+2nr4Anm1WEi1R6B+tPyLAer6l+4Pu7IdoK3wymXl0NE8bIJYJUgnmiS4EogCCRM+9gdB04t89/1O/w1cDnyilFU=';
$channel_secret = '49a5dd53447e86cb95fc2a052e8eab59';

//Get message from Line API
$content = file_get_contents('php://input');
$events=json_decode($content, true);

// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
        $replyToken = $event['replyToken']; 
		
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            
            $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

            $server = $url["host"];
            $username = $url["user"];
            $password = $url["pass"];
            $db = substr($url["path"], 1);

            $conn = new mysqli($server, $username, $password, $db);
            
            if (mysqli_connect_errno()) {
                printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
            }

            /* return name of current default database */
            if ($result = $conn->query("SELECT DATABASE()")) {
            $row = $result->fetch_row();
            $result->close();
            }

/* change db to world db */
            $conn->select_db("heroku_88c65b266d41d8b");

            $conn->set_charset("utf8");
            $inc = $conn->query('SELECT * FROM phone');
            $inc_c = $inc->num_rows;
            
            $respMessage = 'มี '.$inc_c;
        }//if event
        
        $httpClient = new CurlHTTPClient($channel_token);
        $bot=new LINEBot($httpClient, array('channelSecret'=> $channel_secret));
        
        $textMessageBuilder=new TextMessageBuilder($respMessage);
        $response=$bot->replyMessage($replyToken, $textMessageBuilder);
    }
}

echo "OK";