<?php
/**
 * 流式发送消息（逐块发送数据）
 * 
 * @param string $token     机器人令牌
 * @param string $recvId    接收者ID（用户或群组）
 * @param string $recvType  接收类型（user/group）
 * @param string $contentType 内容类型（text/markdown）
 * @param array  $chunks    数据块数组，每个元素为一块字符串
 * @param int    $interval  每块发送间隔（微秒，默认1000000即1秒）
 * @return array 响应结果
 */
function sendStreamMessage($token, $recvId, $recvType, $contentType, $chunks, $interval = 1000000) {
    // 构建URL（参数拼接在查询字符串中）
    $url = "https://chat-go.jwzhd.com/open-apis/v1/bot/send-stream?token=" . urlencode($token) 
           . "&recvId=" . urlencode($recvId) 
           . "&recvType=" . urlencode($recvType) 
           . "&contentType=" . urlencode($contentType);
    
    // 初始化cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // 关键：设置分块传输编码头
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Transfer-Encoding: chunked',
        'Content-Type: text/plain'
    ]);
    
    // 核心：使用回调函数模拟流式写入数据
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($handle, $data) use (&$chunks, $interval) {
        static $index = 0; // 静态变量记录当前发送到第几块
        
        if ($index < count($chunks)) {
            $chunk = $chunks[$index];
            $index++;
            // 输出当前块数据
            echo $chunk;
            // 模拟延迟（根据interval参数）
            usleep($interval);
        } else {
            // 所有块发送完毕，返回空字符串告诉cURL结束
            return '';
        }
        
        return strlen($data); // 返回写入的字节数
    });
    
    // 执行请求
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['code' => -1, 'msg' => 'cURL错误: ' . $error];
    }
    
    return json_decode($response, true);
}

// ---------- 使用示例 ----------
$token = 'xxxxxxxxxx';
$chunks = [];
for ($i = 0; $i < 15; $i++) {
    $chunks[] = "Message $i\n"; // 每一块是一条消息
}

$result = sendStreamMessage(
    $token,
    '7058262',          // 接收者ID
    'user',             // 接收类型
    'text',             // 内容类型（text 或 markdown）
    $chunks,            // 数据块数组
    1000000             // 每块间隔1秒
);

print_r($result);
?>