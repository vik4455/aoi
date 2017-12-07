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
        
		$res = $bot->getProfile($user);
        if ($res->isSucceeded()) {
            $profile = $res->getJSONDecodedBody();
            $displayName = $profile['displayName'];
        }
        if ($event['type'] == 'message') {
            switch($event['message']['type']) {
                case 'text':
                $ct = $event['message']['text'];
                $respMessage=checktxt($ct,$user,$grp,$displayName);
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
        }else if($event['type'] == 'join') {
            if($event['source']['groupId']=="Cb01a760efcaa68ed9590969791ad0175"){
                $respMessage='สวัสดีจ้า User';
            }
        }
        
        $textMessageBuilder=new TextMessageBuilder($respMessage);
        $response=$bot->replyMessage($replyToken, $textMessageBuilder);
    }
}

echo "OK";

function checktxt($cote,$u,$g,$un)
{
    $txt =explode(',', $cote);
    if($txt[0]=="rg"){
        $rname = $txt[1];
        $dt = date('Y-m-d');
        return "ลงทะเบียน ".$g." ชื่อ : ".$un." User ID : ".$u." วันที่ : ".$dt;
    }
}