<?php
/**
 * 上传图片到云湖的php
 * 参考文档：https://www.yhchat.com/document/400-452
 * 
 * 温馨提示：
 * 1. 图片大小别超过10MB，晕乎服务器吃不消(
 * 2. 只支持单张图片上传
 * 3. 返回的 imageKey 记得存好。
 * 
 * @param string $token     机器人token
 * @param string $imagePath 图片本地路径（比如：'/home/user/awesome.jpg'）
 * @return array 响应结果（包含 imageKey 就能发图了）
 */
function uploadImage($token, $imagePath) {
    // 检查图片是否存在
    if (!file_exists($imagePath)) {
        return [
            'code' => -1, 
            'msg' => '图片文件不存在，你是不是记错路径了？🧐'
        ];
    }
    
    // 检查文件大小（10MB = 10 * 1024 * 1024 bytes）
    $maxSize = 10 * 1024 * 1024;
    if (filesize($imagePath) > $maxSize) {
        return [
            'code' => -1, 
            'msg' => '图片太大了！ 😅'
        ];
    }
    
    // 构建请求URL
    $url = "https://chat-go.jwzhd.com/open-apis/v1/image/upload?token=" . urlencode($token);
    
    // 准备上传文件（CURLFile 是 PHP 的"快递员"，负责把文件打包）
    $postFields = [
        'image' => new CURLFile($imagePath)
    ];
    
    // 初始化cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    // 让cURL自动处理 multipart/form-data 头，省心省力
    
    // 执行请求，让图片开始它的云端之旅
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // 错误处理（网络不好连神仙也救不了）
    if ($error) {
        return [
            'code' => -1, 
            'msg' => '网络翻车了：' . $error . '，检查下网络或者token对不对？'
        ];
    }
    
    // 解析响应
    $result = json_decode($response, true);
    
    // 如果上传成功，data里会有imageKey
    if (isset($result['code']) && $result['code'] == 1) {
        // 成功！可以拿着 imageKey 去发图片消息了
        return $result;
    } else {
        // 失败了，可能是token过期或者图片格式不支持
        return $result;
    }
}

// ---------- 使用方法 ----------
$token = 'xxxxxxxxxx';  // 你的机器人令牌
$imagePath = '/Users/feng/Downloads/awesome.png';  // 图片路径

$result = uploadImage($token, $imagePath);

// 输出结果
if ($result['code'] == 1) {
    echo "✅ 图片上传成功！图片Key: " . $result['data']['imageKey'] . "\n";
    echo "🎉 现在你可以用这个Key去发送图片消息了\n";
} else {
    echo "❌ 上传失败：" . $result['msg'] . "\n";
}

print_r($result);
?>