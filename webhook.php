<?php
$input = file_get_contents("php://input");
file_put_contents("webhook_log.txt", $input . "\n", FILE_APPEND);

$data = json_decode($input, true);
$event = $data['events'][0];

if ($event['source']['type'] === 'group') {
    $groupId = $event['source']['groupId'];
    $replyToken = $event['replyToken'];

    // ตอบกลับเพื่อทดสอบ
    $reply = [
        'replyToken' => $replyToken,
        'messages' => [[
            'type' => 'text',
            'text' => "Hello from webhook! groupId: $groupId"
        ]]
    ];

    $accessToken = 'YOUR_CHANNEL_ACCESS_TOKEN';

    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . "Bearer $accessToken"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($reply));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?>
