<?php
// รับ input และ log
$input = file_get_contents("php://input");
error_log("Webhook payload: " . $input);

$data = json_decode($input, true);

// ตรวจสอบว่ามี events
if (!isset($data['events'][0])) {
    error_log("No events found.");
    http_response_code(400);
    exit;
}

$event = $data['events'][0];

// ตรวจสอบว่าเป็นข้อความจากกลุ่ม
if (
    $event['source']['type'] === 'group' &&
    $event['type'] === 'message' &&
    $event['message']['type'] === 'text'
) {
    $groupId = $event['source']['groupId'];
    $userMessage = $event['message']['text'];

    // ตอบกลับ
    $replyToken = $event['replyToken'];
    $replyData = [
        'replyToken' => $replyToken,
        'messages' => [[
            'type' => 'text',
            'text' => "ได้ groupId แล้ว: $groupId"
        ]]
    ];

    // เรียกใช้ access token จาก Environment Variable
    #$accessToken = getenv('LINE_ACCESS_TOKEN');
    $accessToken = 'sy+0j5rdQioNfLHWAkc6xba0uwPuUOGAlxPWurS2VhZ/19kylC6F7BWUXQUeeZEwS5N7QJ27VaHcNzTZkUUI2GBnFoVRvvky8BRcMg0TCIgtebq+oRxRTSYAfeOvfr4CRYbnx02jMZq02Ex7/MesoAdB04t89/1O/w1cDnyilFU=';

    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $accessToken"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($replyData, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    error_log("LINE Reply result: " . $result);
    curl_close($ch);

    // เก็บ groupId ไว้ใน log เท่านั้น (ไม่เขียนไฟล์)
    error_log("Captured groupId: " . $groupId);
}

http_response_code(200);
echo "OK";
?>
