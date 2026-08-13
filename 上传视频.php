<?php
/**
 * bot上传视频到云湖的php
 * 参考文档：https://www.yhchat.com/document/400-453
 * 
 * 温馨提示：
 * 1. 视频大小别超过20MB，晕乎服务器接受不了
 * 2. 只支持单个视频上传，想传连续剧得循环调用
 * 3. 返回的 videoKey 记得存好。
 * 
 * @param string $token     机器人的偷啃
 * @param string $videoPath 视频本地路径
 * @return array 响应结果
 */
function uploadVideo($token, $videoPath) {
    // 检查视频文件是否存在
    if (!file_exists($videoPath)) {
        return [
            'code' => -1, 
            'msg' => '视频文件不存在。'
        ];
    }
    
    // 检查文件大小（20MB限制）
    $maxSize = 20 * 1024 * 1024;
    if (filesize($videoPath) > $maxSize) {
        return [
            'code' => -1, 
            'msg' => '视频太大了！'
        ];
    }
    
    // 构建请求URL
    $url = "https://chat-go.jwzhd.com/open-apis/v1/video/upload?token=" . urlencode($token);
    
    // 准备上传文件
    $postFields = [
        'video' => new CURLFile($videoPath)
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
            'msg' => '网络异常：' . $error
        ];
    }
    
    return json_decode($response, true);
}

// ---------- 使用示例 ----------
$token = 'xxxxxxxxxx';
$videoPath = '/path/to/your/video.mp4';  // 你的视频文件路径

$result = uploadVideo($token, $videoPath);

// 输出结果
if ($result['code'] == 1) {
    echo "视频上传成功！videoKey: " . $result['data']['videoKey'] . "\n";
} else {
    echo "上传失败：" . $result['msg'] . "\n";
}

print_r($result);
?>