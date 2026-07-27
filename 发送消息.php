<?php
/**
 * ============================================================
 * 云湖机器人发送消息 - PHP代码
 * 支持发送给个人用户和群聊两种模式
 * API文档参考: https://chat-go.jwzhd.com/open-apis/v1/bot/send
 * ============================================================
 */

// ============================================================
// 配置区域 - 请根据实际情况修改
// ============================================================

// 机器人的token（从云湖机器人配置界面获取）
define('BOT_TOKEN', 'xxxxxxxxxx');

// APIURL
define('API_BASE_URL', 'https://chat-go.jwzhd.com/open-apis/v1/bot/send');


// ============================================================
// 1. 发送消息给个人用户
// ============================================================

/**
 * 发送消息给指定用户
 * 
 * @param string $userId    接收消息的用户ID
 * @param string $message   消息内容（纯文本）
 * @param array  $buttons   可选，按钮配置（二维数组，每行是一个按钮数组）
 * @return array            API响应结果
 */
function sendMessageToUser($userId, $message, $buttons = []) {
    // 构建请求体
    $requestData = [
        'recvId'      => $userId,          // 用户ID
        'recvType'    => 'user',           // 接收类型：用户
        'contentType' => 'text',           // 消息类型：文本
        'content'     => [
            'text'    => $message,         // 消息正文
        ]
    ];
    
    // 如果有按钮，添加到content中
    if (!empty($buttons)) {
        $requestData['content']['buttons'] = $buttons;
    }
    
    // 发送请求
    return sendRequest($requestData);
}


// ============================================================
// 2. 发送消息给群聊
// ============================================================

/**
 * 发送消息到指定群聊
 * 
 * @param string $groupId    接收消息的群ID
 * @param string $message    消息内容（纯文本）
 * @param array  $buttons    可选，按钮配置（二维数组，每行是一个按钮数组）
 * @return array             API响应结果
 */
function sendMessageToGroup($groupId, $message, $buttons = []) {
    // 构建请求体
    $requestData = [
        'recvId'      => $groupId,         // 群ID
        'recvType'    => 'group',          // 接收类型：群聊
        'contentType' => 'text',           // 消息类型：文本
        'content'     => [
            'text'    => $message,         // 消息正文
        ]
    ];
    
    // 如果有按钮，添加到content中
    if (!empty($buttons)) {
        $requestData['content']['buttons'] = $buttons;
    }
    
    // 发送请求
    return sendRequest($requestData);
}


// ============================================================
// 3. 发送 Markdown 消息给个人用户
// ============================================================

/**
 * 发送Markdown消息给指定用户
 * 
 * @param string $userId    接收消息的用户ID
 * @param string $markdown  Markdown格式的消息内容
 * @param array  $buttons   可选，按钮配置
 * @return array            API响应结果
 */
function sendMarkdownToUser($userId, $markdown, $buttons = []) {
    $requestData = [
        'recvId'      => $userId,
        'recvType'    => 'user',
        'contentType' => 'markdown',       // 消息类型：Markdown
        'content'     => [
            'text'    => $markdown,
        ]
    ];
    
    if (!empty($buttons)) {
        $requestData['content']['buttons'] = $buttons;
    }
    
    return sendRequest($requestData);
}


// ============================================================
// 4. 发送 Markdown 消息给群聊
// ============================================================

/**
 * 发送Markdown消息到指定群聊
 * 
 * @param string $groupId    接收消息的群ID
 * @param string $markdown  Markdown格式的消息内容
 * @param array  $buttons   可选，按钮配置
 * @return array            API响应结果
 */
function sendMarkdownToGroup($groupId, $markdown, $buttons = []) {
    $requestData = [
        'recvId'      => $groupId,
        'recvType'    => 'group',
        'contentType' => 'markdown',
        'content'     => [
            'text'    => $markdown,
        ]
    ];
    
    if (!empty($buttons)) {
        $requestData['content']['buttons'] = $buttons;
    }
    
    return sendRequest($requestData);
}


// ============================================================
// 5. 核心请求函数
// ============================================================

/**
 * 发送HTTP请求到机器人API
 * 
 * @param array $requestData 请求数据（已组装好的数组）
 * @return array             解析后的响应结果
 * @throws Exception         请求失败时抛出异常
 */
function sendRequest($requestData) {
    // 构建完整的请求URL（包含token）
    $url = API_BASE_URL . '?token=' . BOT_TOKEN;
    
    // 将请求数据转换为JSON字符串
    $jsonData = json_encode($requestData, JSON_UNESCAPED_UNICODE);
    
    // 初始化cURL会话
    $ch = curl_init($url);
    
    // 设置cURL选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // 返回响应内容，不直接输出
    curl_setopt($ch, CURLOPT_POST, true);                // 使用POST方法
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);     // 设置请求体
    curl_setopt($ch, CURLOPT_HTTPHEADER, [               // 设置请求头
        'Content-Type: application/json; charset=utf-8',
        'Content-Length: ' . strlen($jsonData)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);               // 设置超时时间（秒）
    
    // 如果使用HTTPS且需要验证SSL证书，取消注释下面两行
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    // curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cacert.pem');
    
    // 执行请求
    $response = curl_exec($ch);
    
    // 检查cURL错误
    if (curl_errno($ch)) {
        $errorMsg = curl_error($ch);
        curl_close($ch);
        throw new Exception('cURL请求失败: ' . $errorMsg);
    }
    
    // 获取HTTP状态码
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // 检查HTTP状态码
    if ($httpCode !== 200) {
        throw new Exception('HTTP请求失败，状态码: ' . $httpCode . '，响应: ' . $response);
    }
    
    // 解析JSON响应
    $result = json_decode($response, true);
    if ($result === null) {
        throw new Exception('解析JSON响应失败: ' . $response);
    }
    
    return $result;
}


// ============================================================
// 6. 使用示例
// ============================================================

// 示例：定义按钮配置（二维数组，每行是一个按钮组）
$exampleButtons = [
    // 第一行按钮
    [
        [
            'text'       => '复制链接',
            'actionType' => 2,              // 复制类型
            'value'      => 'https://example.com'
        ],
        [
            'text'       => '访问官网',
            'actionType' => 1,              // 跳转URL类型
            'url'        => 'https://www.baidu.com'
        ]
    ],
    // 第二行按钮（单独一行）
    [
        [
            'text'       => '点击汇报',
            'actionType' => 3,              // 汇报类型
            'value'      => '用户点击了汇报按钮'
        ]
    ]
];

// 简单的单行按钮（只有一个按钮）
$simpleButton = [
    [
        [
            'text'       => '点击我',
            'actionType' => 2,
            'value'      => '复制内容'
        ]
    ]
];

try {
    // ============================================================
    // 场景一：发送文本消息给个人用户
    // ============================================================
    echo "【发送给个人用户】\n";
    $result = sendMessageToUser(
        '7058262',                                    // 用户ID
        '您好，这是一条来自机器人的测试消息！',          // 消息内容
        $simpleButton                                 // 可选按钮
    );
    print_r($result);
    
    // ============================================================
    // 场景二：发送文本消息给群聊
    // ============================================================
    echo "\n【发送给群聊】\n";
    $result = sendMessageToGroup(
        '123456789',                                  // 群ID（请替换为实际群ID）
        '【群公告】\n今日系统维护，请各位注意！',        // 消息内容
        $exampleButtons                               // 可选按钮
    );
    print_r($result);
    
    // ============================================================
    // 场景三：发送Markdown消息给个人用户
    // ============================================================
    echo "\n【发送Markdown给个人用户】\n";
    $markdownContent = "## 系统通知\n" .
                       "**重要提醒**：\n" .
                       "- 系统将于今晚 22:00 进行升级\n" .
                       "- 预计耗时 2 小时\n" .
                       "- 请提前保存好工作数据\n\n" .
                       "> 如有疑问，请联系管理员";
    
    $result = sendMarkdownToUser(
        '7058262',
        $markdownContent,
        $simpleButton
    );
    print_r($result);
    
    // ============================================================
    // 场景四：只发送纯文本，不带按钮（最简单的情况）
    // ============================================================
    echo "\n【发送纯文本给群聊（无按钮）】\n";
    $result = sendMessageToGroup(
        '123456789',
        'Hello, 这是一条纯文本消息，没有任何按钮！'
        // 不传入buttons参数，默认为空数组
    );
    print_r($result);
    
} catch (Exception $e) {
    echo '错误: ' . $e->getMessage() . "\n";
}

/**
 * ============================================================
 * 其他消息类型说明（扩展功能）
 * ============================================================
 * 
 * 如果需要发送图片、文件、视频等，可以按以下方式修改：
 * 
 * 1. 发送图片消息：
 *    $requestData['contentType'] = 'image';
 *    $requestData['content'] = ['imageKey' => '图片Key', 'buttons' => $buttons];
 * 
 * 2. 发送文件消息：
 *    $requestData['contentType'] = 'file';
 *    $requestData['content'] = ['fileKey' => '文件Key', 'buttons' => $buttons];
 * 
 * 3. 发送视频消息：
 *    $requestData['contentType'] = 'video';
 *    $requestData['content'] = ['videoKey' => '视频Key', 'buttons' => $buttons];
 * 
 * 4. 发送HTML消息：
 *    $requestData['contentType'] = 'html';
 *    $requestData['content'] = ['text' => '<b>粗体</b> <i>斜体</i>'];
 * 
 * 5. 引用消息（回复某条消息）：
 *    $requestData['parentId'] = '消息ID';
 * ============================================================
 */