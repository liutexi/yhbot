<?php
/**
 * 获取消息列表
 * 参考文档：https://www.yhchat.com/document/400-450
 * 
 * @param string $token     机器人令牌
 * @param string $chatId    对话ID（用户ID或群组ID）
 * @param string $chatType  对话类型（user 或 group）
 * @param string|null $messageId 消息ID（可选，不填则返回最近的N条）
 * @param int|null $before  指定消息前N条（可选）
 * @param int|null $after   指定消息后N条（可选）
 * @return array 响应结果
 */
function getMessages($token, $chatId, $chatType, $messageId = null, $before = null, $after = null) {
    // 构建查询参数
    $params = [
        'token'     => $token,
        'chat-id'   => $chatId,
        'chat-type' => $chatType
    ];
    
    if ($messageId !== null) {
        $params['message-id'] = $messageId;
    }
    if ($before !== null) {
        $params['before'] = $before;
    }
    if ($after !== null) {
        $params['after'] = $after;
    }
    
    // 构建完整URL
    $url = "https://chat-go.jwzhd.com/open-apis/v1/bot/messages?" . http_build_query($params);
    
    // 初始化cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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

// 1. 获取群组最新10条消息
$result1 = getMessages($token, 'xxx', 'group', null, 10);
print_r($result1);

// 2. 获取指定消息前10条
$result2 = getMessages($token, 'xxx', 'group', 'dad25257f71f41098f733a5079183080', 10);
print_r($result2);

// 3. 获取指定消息后10条
$result3 = getMessages($token, 'xxx', 'group', 'dad25257f71f41098f733a5079183080', null, 10);
print_r($result3);

// 4. 获取指定消息前后各10条
$result4 = getMessages($token, 'xxx', 'group', 'dad25257f71f41098f733a5079183080', 10, 10);
print_r($result4);

// 5. 获取用户消息
$result5 = getMessages($token, 'xxx', 'user', 'dad25257f71f41098f733a5079183080', 10);
print_r($result5);
?>