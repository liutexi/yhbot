<?php
/**
 * 群成员禁言.php
 * 参考文档：https://www.yhchat.com/document/456-457
 * 
 * 温馨提示：
 * 1. 机器人必须在群里，且有"允许禁言用户"权限
 * 2. gag = 0 是解禁，-1 是永久禁言
 * 3. 禁言时长单位是秒。
 * 
 * @param string $token   机器人token
 * @param string $userId  被禁言的用户ID
 * @param string $groupId 群组ID
 * @param int    $gag     禁言时长（秒）：0解禁 / 600(10分钟) / 3600(1小时) / 21600(6小时) / 43200(12小时) / -1永久
 * @return array 响应结果
 */
function gagMember($token, $userId, $groupId, $gag) {
    // 构建请求URL
    $url = "https://chat-go.jwzhd.com/open-apis/v1/group/gag-member?token=" . urlencode($token);
    
    // 构建请求体
    $payload = [
        'userId'  => $userId,
        'groupId' => $groupId,
        'gag'     => $gag
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
        return ['code' => -1, 'msg' => '网络异常：' . $error];
    }
    
    return json_decode($response, true);
}

// ---------- 使用示例 ----------
$token = 'xxxxxxxxxx';

// 禁言用户10分钟
$result = gagMember($token, '7058262', '123456', 600);
if ($result['code'] == 1) {
    echo "✅ 禁言成功！用户已安静10分钟\n";
} else {
    echo "❌ 禁言失败：" . $result['msg'] . "\n";
    echo "💡 常见原因：机器人不在群里、没有禁言权限、或参数错误\n";
}

// 解除禁言
$result2 = gagMember($token, '7058262', '123456', 0);
if ($result2['code'] == 1) {
    echo "✅ 已解除禁言\n";
}

print_r($result);
?>