<?php
include('vendor/autoload.php');

use Telegram\Bot\Api;

$telegram = new Api('979015857:AAHSLDwfOTiYayD0X438RpAnzwmJxiYUCtQ');

$result = $telegram->getWebhookUpdates();
$chat_id = $result["message"]["chat"]["id"];
$text = $result["message"]["text"];

if ($curl = curl_init()) {
    $get = str_replace(' ', '+', $text);

    curl_setopt($curl, CURLOPT_URL, "https://knigavuhe.org/search/?q=" . $get);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

    $html = curl_exec($curl);
    preg_match_all('/<a class="bookkitem_cover" href="(.*)">/m', $html, $arr);
    $links = $arr[1];
//    var_dump($links);

    foreach ($links as $link) {
        if ($chat_id && $link) {
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $link]);
        } else
            return "Нет chat_id или ссылки на книгу";
    }

    $bookToFind = $result['message']['text'];
//    foreach ($links as $link) {
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, 'https://knigavuhe.org' . $bookToFind);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            $audioPage = curl_exec($curl);
//        }
//        var_dump($audioPage);
    }
    preg_match_all('/"url":"(.*)"/iU', $audioPage, $playerLinks);

//    var_dump($playerLinks[0]);
    if (isset($playerLinks[0])) {
        $newLinks = [];
        foreach ($playerLinks[0] as $playerLink) {
            $clearString = stripslashes(json_encode($playerLink));
//            var_dump($clearString);
            if (strpos($clearString, '0.mp3')) {
                continue;
            } else
                $clearNewLink = str_replace('""', "", $clearString);
            $clearNewLink = str_replace('url":"', "", $clearNewLink);
            $clearNewLink = str_replace("\\", "", $clearNewLink);
            array_push($newLinks, $clearNewLink);
        }
//        var_dump($clearNewLink);
        var_dump($newLinks);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $newLinks]);
    }
} else {
    return "Курла нет!!";
}
?>

