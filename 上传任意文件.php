<?php
/**
 * 上传文件到云湖的php
 * 参考文档：https://www.yhchat.com/document/400-454
 * 
 * 温馨提示：
 * 1. 文件大小别超过20MB。
 * 2. 支持各种文件类型（PDF、Word、Excel、文本等），废话
 * 3. 返回的 fileKey保存好
 * 
 * @param string $token     机器人token
 * @param string $filePath  文件绝对路径
 * @return array 响应结果
 */
function uploadFile($token, $filePath) {
    // 检查文件是否存在
    if (!file_exists($filePath)) {
        return [
            'code' => -1, 
            'msg' => '文件不存在，回答我！'
        ];
    }
    
    // 检查文件大小（20MB限制）
    $maxSize = 20 * 1024 * 1024;
    if (filesize($filePath) > $maxSize) {
        return [
            'code' => -1, 
            'msg' => '文件太大了！压缩一下'
        ];
    }
    
    // 构建请求URL
    $url = "https://chat-go.jwzhd.com/open-apis/v1/file/upload?token=" . urlencode($token);
    
    // 准备上传文件
    $postFields = [
        'file' => new CURLFile($filePath)
    ];
    
    // 初始化cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    
    // 执行请求
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    // 错误处理
    if ($error) {
        return [
            'code' => -1, 
            'msg' => '网络出问题了：' . $error
        ];
    }
    
    return json_decode($response, true);
}

// ---------- 使用示例 ----------
$token = 'xxxxxxxxxx';
$filePath = '/path/to/your/file.pdf';  // 你的文件路径

$result = uploadFile($token, $filePath);

// 输出结果
if ($result['code'] == 1) {
    echo "✅ 文件上传失败fileKey: " . $result['data']['fileKey'] . "\n";
    echo "🎉 现在可以拿着这个Key去发送文件消息了\n";
} else {
    echo "❌ 上传失败：" . $result['msg'] . "\n";
}

print_r($result);
?>