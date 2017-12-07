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
        $httpClient = new CurlHTTPClient($channel_token);
        $bot=new LINEBot($httpClient, array('channelSecret'=> $channel_secret));
        $grp = $event['source']['groupId'];
        $user = $event['source']['userId'];
        $replyToken = $event['replyToken'];
        
        $ins = $conn->query('SELECT MAX(idmember) AS mm FROM member');
                $mm = $ins->fetch_assoc();
                $nid = str_pad($mm['mm']+1,3,"0",STR_PAD_LEFT);
		$res = $bot->getProfile($user);
        if ($res->isSucceeded()) {
            $profile = $res->getJSONDecodedBody();
            $displayName = $profile['displayName'];
        }
        if ($event['type'] == 'message') {
            switch($event['message']['type']) {
                case 'text':
                $ct = $event['message']['text'];
                
                $txt =explode(',', $ct);
                $dt = date('Y-m-d');
                if(($txt[0]=="rg")&&(strlen($txt[1])==13)&&(is_numeric($txt[1]))){
                $add_user = $conn->query('INSERT INTO 
                            member (idmember,mem_name,mem_id,mem_card,mem_status,date_regis) 
                            VALUES ("'.$nid.'","'.$displayName.'","'.$user.'","'.$txt[1].'","11","'.$dt.'")');
                            if (!$add_user) {
                                die('Add Member : '.$conn->error);
                            }
                $respMessage= "ลงทะเบียนสมาชิก ".$ni." ชื่อ : ".$dpn." ".$txt[1]." User ID : ".$mi." วันที่ : ".$dt;
                }else{
                $respMessage= "format ลงทะเบียนคือ rg,(เลขบัตรประชาชน)";
                }
                break;
                case 'image':
                $respMessage='รูปภาพ';
                break;
                case 'location':
                $respMessage='สถานที่';
                break;
                case 'audio':
                $respMessage='เสียง';
                break;
            }   
        }
        
        $textMessageBuilder=new TextMessageBuilder($respMessage);
        $response=$bot->replyMessage($replyToken, $textMessageBuilder);
    }
}

echo "OK";