<?php
$input = file_get_contents("php://input");
file_put_contents("webhook_log.txt", $input . "\n", FILE_APPEND); // บันทึกเพื่อดู

$data = json_decode($input, true);
$event = $data['events'][0];

// ตรวจสอบว่าเป็นข้อความจากกลุ่ม
if ($event['source']['type'] === 'group') {
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

    // ส่งกลับไปที่ LINE
    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer {sy+0j5rdQioNfLHWAkc6xba0uwPuUOGAlxPWurS2VhZ/19kylC6F7BWUXQUeeZEwS5N7QJ27VaHcNzTZkUUI2GBnFoVRvvky8BRcMg0TCIgtebq+oRxRTSYAfeOvfr4CRYbnx02jMZq02Ex7/MesoAdB04t89/1O/w1cDnyilFU=}'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($replyData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // เก็บ groupId ไว้ใช้งานภายหลัง
    file_put_contents("groups.txt", "$groupId\n", FILE_APPEND);
}
?>
