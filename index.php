<?php
include('vendor/autoload.php');
use Telegram\Bot\Api;

$telegram = new Api('979015857:AAHSLDwfOTiYayD0X438RpAnzwmJxiYUCtQ');


$result = $telegram->getWebhookUpdates();
$chat_id = $result["message"]["chat"]["id"];

if ($curl = curl_init()) {
    $get = str_replace(' ', '+', $result["message"]["text"]);
    print_r($get);
    curl_setopt($curl, CURLOPT_URL, "https://knigavuhe.org/search/?q=" . $get);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

    $html = curl_exec($curl);

    preg_match_all('|<a class="bookkitem_cover"[^>]*?>|sei', $html, $arr);

    if (isset($arr[0])) {
        $encoded = stripslashes(json_encode($arr[0]));
        preg_match_all('|href=[^>]*?>|sei', $encoded, $bookRefs);

        foreach ($bookRefs as $bookRef) {
            $clearRef = stripslashes(json_encode($bookRef));
            $clearLink = str_replace('["href="', "", $clearRef);
            $clearLink = str_replace('"href="', "", $clearLink);
            $clearLink = str_replace('">', "", $clearLink);
            $clearLink = str_replace(']', "", $clearLink);
            $clearLink = str_replace('"', "", $clearLink);
        }

        $links = explode(',', $clearLink);
        $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $clearLink]);

        $result = $telegram->getWebhookUpdates();
        $exactBook = $result["message"]["text"];

        foreach ($links as $link) {
            if ($curl = curl_init()) {
                curl_setopt($curl, CURLOPT_URL, $exactBook);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                $audioPage = curl_exec($curl);

            }
        }
        preg_match_all('/"url":"(.*)"/iU', $audioPage, $playerLinks);

        if (isset($playerLinks[0])) {
//
            $newLinks = [];
//
            foreach ($playerLinks[0] as $playerLink) {
                $clearString = stripslashes(json_encode($playerLink));
//
                if (strpos($clearString, '0.mp3')) {
                    continue;
                } else
                    $clearNewLink = str_replace('""', "", $clearString);
                    $clearNewLink = str_replace('url":"', "", $clearNewLink);
                $clearNewLink = str_replace("\\", "", $clearNewLink);
                array_push($newLinks, $clearNewLink);
            }
            print_r($newLinks);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $newLinks]);
        }
    } else {
        return "Not found:(";
    }
} else {
    return "Курла нет!!";
}

