<?php
function batchSendMessage($token, $recvIds, $recvType, $contentType, $content, $buttons = null) {
    $url = "https://chat-go.jwzhd.com/open-apis/v1/bot/batch_send?token=" . $token;
    
    $payload = [
        'recvIds' => $recvIds,
        'recvType' => $recvType,
        'contentType' => $contentType,
        'content' => $content
    ];
    
    if ($buttons !== null) {
        $payload['content']['buttons'] = $buttons;
    }
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['code' => -1, 'msg' => $error];
    }
    
    return json_decode($response, true);
}

// 使用示例
$result = batchSendMessage(
    'xxxxxxxxxx',
    ['7058262', '7058263'],
    'user',
    'text',
    ['text' => '这里是消息内容'],
    [
        [
            ['text' => '复制(这是按钮)', 'actionType' => 2, 'value' => 'xxxx'],
            ['text' => '点击跳转(这是按钮)', 'actionType' => 1, 'url' => 'http://www.baidu.com']
        ]
    ]
);

print_r($result);
?>