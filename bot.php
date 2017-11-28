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
        include 'include/connect.php';
        $replyToken = $event['replyToken']; 
		
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            if (strpos($event['message']['text'], ',') !== false) {
                $ins = $conn->query('SELECT MAX(idphone) AS mi FROM phone');
                $mi = $ins->fetch_assoc();
                $nid = $mi['mi']+1;
                $txttel =explode(',', $event['message']['text']); //รับค่าตัวอักษร
                
                switch(strtolower($txttel[0])){   
                    case 'm':
                            $add_phone = $conn->query('INSERT INTO phone (idphone,name,phone) 
                            VALUES ("'.$nid.'","'.$txttel[1].'","'.$txttel[2].'")');
                            if (!$add_phone) {
                                die('Add Phone : '.$conn->error);
                            }
                            $respMessage='บันทึกเบอร์ '.$txttel[1].' -> '.$txttel[2].' เรียบร้อย';
                            break; 
                    case 's':
                            $sch_phone = $conn->query('SELECT phone FROM phone WHERE name = "'.$txttel[1].'"');
                            if (!$sch_phone) {
                                die('Search Phone : '.$conn->error);
                            }
                            $tt = $sch_phone->fetch_assoc();
                            $respMessage='เบอร์ของ '.$txttel[1].' คือ '.$tt['phone'];
                            break; 
                    default:
                            $respMessage='พิมพ์ m,ชื่อเพื่อน,เบอร์โทร เพื่อบันทึก \n s,ชื่อเพื่อน เพื่อค้นหา'; 
                            break;
                }
            } 
        }//if event
        
        $httpClient = new CurlHTTPClient($channel_token);
        $bot=new LINEBot($httpClient, array('channelSecret'=> $channel_secret));
        
        $textMessageBuilder=new TextMessageBuilder($respMessage);
        $response=$bot->replyMessage($replyToken, $textMessageBuilder);
    }
}

echo "OK";