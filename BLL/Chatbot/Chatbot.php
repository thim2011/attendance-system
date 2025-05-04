<?php
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $message = MyLIB::getString('message');
    $userid = MyLIB::getNumber('userid');
    $res = array();
    if($message == '' || $userid == ''){
        $res['err'] = 1;
        $res['msg'] = '系統發生問題，請重新嘗試';
        echo json_encode($res);
        exit();
    }

    $question_mean = [
        '公司規則' => [
            '公司的報到手續是什麽？'     =>'報到',
            '有哪些員工福利和獎金制度？' =>'福利',
            '公司的請假政策如何？'       =>'請假',
            '公司的加班政策如何？'       =>'加班',
            '公司的年資計算？'          =>'獎懲',
            '公司的試用期？'          =>'新進試用',
            '終止勞動契約?'          =>'終止契約',
            '公司離職流程？'          =>'離職',
            '資遣預告期？'           =>'資遣',
            '調動'                =>'調動',
            '工資'              =>'工資',
            '工作時間'        =>'工作時間',
            '休假，年假，產假，婚假，等等假'      =>'給假',
            '請假手續'            =>'請假',
            '自請退休，退休'      =>'退休',
            '夜間，女工夜間'      =>'夜間',
            '遲到，早退'          =>'遲到',
            '獎懲升遷'          =>'獎懲',
            '職業災害補償,職業災害補償抵充' =>'職業',
            '勞保，健保，險'          =>'險',
            '申訴'              =>'申訴',
        ],
        '使用者個人資料' => [
            '今年的我每個月上班時數？' => 'working_hours',
            '我有上班嗎？'            => 'attendance',
            '審核通過了嗎？'          => 'approval_status',
            '年假剩餘？'              => 'annual_leave'
        ],
        '關於系統' => [
            '這個月的請假率？'     => 'leave_rate',
            '昨天有誰請假/打卡？'  => 'yesterday_attendance',
            '公司幾點上下班？'     => 'working_hours',
            '公司休息時間？'       => 'break_time',
            '公司行事曆？'         => 'company_calendar',
            '打卡分數？'           => 'attendance_score'
        ],
    ];
    
    #region 1.分析使用者需求，取得keyword關鍵字
    $context = "你是【XXX公司】的AI助理。你的任務是回答關於公司的問題，但僅限於提供的資訊範圍內。請仔細分析使用者的問題，並根據以下類別提供相應的資訊：
    " . json_encode($question_mean, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . " 
    分析後，請直接返回與問題最相關的關鍵詞（也就是每個問題對應的值）。如果找不到完全匹配的問題，請選擇最相近的一個。如果實在找不到相關的關鍵詞，請回答 'unknown'。
    請只回答關鍵詞，不要添加任何其他解釋或內容。";

    $message = $context . "\n\n使用者的問題: " . $message;

    $result = RequestToGemini($message, $API_key);
    $keyword=trim($result['candidates'][0]['content']['parts'][0]['text']);
    #endregion  

    #region 2.根據keyword取得回答
    $AllDoneResponse=processQuery($keyword, $question_mean, $API_key);
    #endregion 
    
    #region 3.回傳結果
    $res['err'] = 0;
    $res['msg'] = $AllDoneResponse['msg'];
    $res['token'] = $AllDoneResponse['token'];
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    #endregion

    function getDAL($DALName) {
        static $services = [];
        
        if (!isset($services[$DALName])) {
            $className = $DALName;
            $filePath = dirname(__FILE__) . "/../../DAL/{$className}.php";
            
            if (file_exists($filePath)) {
                require_once $filePath;
                if (class_exists($className)) {
                    $services[$DALName] = new $className();
                } else {
                    throw new Exception("類別 {$className} 不存在");
                }
            } else {
                throw new Exception("檔案 {$DALName}不存在");
            }
        }
        
        return $services[$DALName];
    }

    function categorizeKeyword($keyword, $question_mean) {
        foreach ($question_mean as $category => $items) {
            if (in_array($keyword, $items)) {
                return $category;
            }
        }   
        return '找不到符合的類別資訊';
    }
    
    function processQuery($keyword, $question_mean, $API_key) {
        $category = categorizeKeyword($keyword, $question_mean);
        
        switch ($category) {
            case '公司規則':
                return PolicyResponse($keyword, $API_key);
            
            case '使用者個人資料':
                return UserResponse($keyword, $API_key);
            
            case '關於系統':
                return SystemResponse($keyword, $API_key);
            
            default:
                return "找不到符合的資訊";
        }
    }

    function isValidKeyword($response, $question_mean) {
        foreach ($question_mean as $category => $questions) {
            if (in_array($response, $questions)) {
                return true;
            }
        }
        return false;
    }

    function formatPolicyResponse($response) {
        $str = '<span class="text-bold p-2">公司條款如下：</span><br>';
        foreach ($response as $row) {
            $str .= '<span class="text-bold bg-secondary">'.$row->Chapter . ' - ' . $row->Article . '</span><br> ' . $row->Content;
            $str .= "<br><br>";
        }
        $str .= '<span class="text-bold p-2">小助理總結：</span><br>';
        return $str;
    }

    function PolicyResponse($keyword, $API_key){
        $PolicyDAL=getDAL('PolicyDAL');
        $SQLresponse = $PolicyDAL->getPolicy($keyword);
        $SQLresponse = formatPolicyResponse($SQLresponse);
        $policyContext="這裏有幾個公司條款，幫使用者做一個小總結。 條款如下：".$SQLresponse;
        $GeminiResponse = RequestToGemini($policyContext, $API_key);
        return  $res = array( 'msg'=>$SQLresponse. $GeminiResponse['candidates'][0]['content']['parts'][0]['text'],
        'token'=>$GeminiResponse['usageMetadata']);
    }

    function UserResponse($keyword, $API_key){
        switch ($keyword) {
            case 'working_hours':
                return '';
            case 'attendance':
                return '';
            case 'approval_status':
                return '';
            case 'annual_leave':
                return '';
            default:
                return '';
        }

        return $res= array('msg'=>'功能還沒完成');
    }

    function SystemResponse($keyword, $API_key){
        return $res= array('msg'=>'功能還沒完成');
    }

    function RequestToGemini($message,$API_key){
        $model = 'gemini-1.5-flash';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $API_key;
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $message
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.9,
                'topK' => 1,
                'topP' => 1,
                'maxOutputTokens' => 2048,
            ]
        ];
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data)
            ],
            'safetySettings' => [
                'category' => 'HARM_CATEGORY_DANGEROUS',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ],
        ];
    
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            die('請求API出問題');
        }
        $result = json_decode($response, true);

        return $result;
    }
        /*如果使用者的問題有屬於這些類別請明確告訴他他的問題關於什麽類別，如果使用者的問題不屬於這些類別或超出了【XXX公司】的範圍，請禮貌地告知你無法回答該問題，並建議他們聯繫公司的人力資源部門或客戶服務。
    
    回答時，請使用提供的資訊，不要編造或猜測任何未提供的細節。如果沒有足夠的資訊回答某個具體問題，請誠實地說明你沒有該資訊。
    
    請用專業、友好的語氣回答，並始終保持對公司的正面態度*/
    /*if (!isValidKeyword($keyword, $question_mean)) {
        $res['err'] = 1;
        $res['msg'] = '不懂你的問題';
        $res['token'] = $result['usageMetadata'];
        echo json_encode($res);
        exit();
    }*/
?>
