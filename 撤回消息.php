<?php
/**
 * 撤回消息
 * 参考文档：https://www.yhchat.com/document/400-451
 * 
 * @param string $token   机器人令牌
 * @param string $msgId   消息ID
 * @param string $chatId  消息对象ID（用户ID或群组ID）
 * @param string $chatType 消息对象类型（user 或 group）
 * @return array 响应结果
 */
function recallMessage($token, $msgId, $chatId, $chatType) {
    // 构建请求URL
    $url = "https://chat-go.jwzhd.com/open-apis/v1/bot/recall?token=" . urlencode($token);
    
    // 构建请求体
    $payload = [
        'msgId'   => $msgId,
        'chatId'  => $chatId,
        'chatType' => $chatType
    ];
    
    // 初始化cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8'
    ]);
    
    // 执行请求
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    // 错误处理
    if ($error) {
        return ['code' => -1, 'msg' => 'cURL错误: ' . $error];
    }
    
    return json_decode($response, true);
}

// ---------- 使用示例 ----------
$token = 'xxxxxxxxxx';
$result = recallMessage(
    $token,
    'xxxxxxx',      // 消息ID
    '7058262',      // 用户ID或群组ID
    'user'          // user 或 group
);

print_r($result);
?>