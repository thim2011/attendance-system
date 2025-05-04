<?php
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require("../../PHPMailer-master/src/PHPMailer.php");
    require("../../PHPMailer-master/src/SMTP.php");
    require("../../PHPMailer-master/src/Exception.php");

    $token = MyLIB::GetString('token');
    $email = MyLIB::GetString('email');
    $leave = MyLIB::GetString('leave');
    if(empty($email) || empty($leave)){
        echo "Email or Leave is empty";
        exit();
    }

    $links = [
        'PunchIn' => 'https://c16a-210-61-64-165.ngrok-free.app/PucnhIn_system/Home/Index/Index.php',
        'NewLeave' => 'https://c16a-210-61-64-165.ngrok-free.app/PucnhIn_system/Home/Index/LeaveForm.php',
        'VerifyLeaves' => 'https://c16a-210-61-64-165.ngrok-free.app/PucnhIn_system/Home/Index/LeaveCheck.php',
        'RejectLeaves' => 'https://c16a-210-61-64-165.ngrok-free.app/PucnhIn_system/Home/Index/LeaveCheck.php',
        'Reset' => "https://c16a-210-61-64-165.ngrok-free.app/PucnhIn_system/Home/User/ResetPassword.php?token=$token",
        'Forgot' => 'https://c16a-210-61-64-165.ngrok-free.app/PucnhIn_system/Home/Index/Index.php'
    ];

    $subjects = [
        'PunchIn' => '打卡成功',
        'NewLeave' => '有人新增了請假單，請您審核',
        'VerifyLeaves' => '你的請假單被審核了 快快看',
        'RejectLeaves' => '你的請假單被拒絕了 快看',
        'Reset' => '打卡系統 重設密碼',
        'SuccessReset' => '重設密碼成功',
        'Forgot' => '有人忘記打卡'
    ];
     
    $content = [
        'PunchIn' => '您於'.date("Y-m-d H:i").'已成功打卡, 詳細資料請進入系統查詢',
        'NewLeave' => '有人在'.date("Y-m-d H:i").'新增了請假單，請您審核',
        'VerifyLeaves' => '你的請假單被同意了 快快看',
        'RejectLeaves' => '你的請假單被拒絕了 快看',
        'Reset' => '打卡系統 重設密碼',
        'SuccessReset' => '重設密碼成功',
        'Forgot' => '有人忘記打卡'
    ];


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 's21115148@stu.edu.tw'; 
        $mail->Password   = 'lxzajzevoqzntmpg'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';

        // Người gửi và người nhận
        $mail->setFrom('s21115148@stu.edu.tw', '打卡系統 Alert');
        $mail->addAddress($email, '');

        // Nội dung email
        $mail->isHTML(true);
   
        $mail->Subject = $subjects[$leave];
        $mail->Body    = '  
        <html>
            <body style="font-family: Arial, sans-serif; color: #333;">
                <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9;">
                    <h2 style="color: #28a745;">來自打卡系統的通知</h2>
                    <p>您好，</p>
                    <p>'.$content[$leave].'</p>
                    
                    <p style="margin-top: 20px;">要查看更多詳細信息並確認，請點擊以下按鈕：</p>
                    <a href="' . $links[$leave] . '" style="display: inline-block; padding: 12px 25px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 5px;">查看詳情</a>

                    <p style="margin-top: 20px;">感謝您使用我們的系統。</p>
                    <p>致敬,<br>打卡系統支持團隊</p>

                    <hr style="margin-top: 30px; border-top: 1px solid #ddd;">
                    <p style="font-size: 12px; color: #888;">如果您未申請此通知，請忽略此郵件。</p>
                </div>
            </body>
        </html>';

        
        $mail->send();
        $res = array();
        $res['err']= 0;
        $res['msg']="已成功發送Email通知";
        
    } catch (Exception $e) {
        $res = array();
        $res['err']= 1;
        $res['msg']="發送Email通知失敗, 問題: {$mail->ErrorInfo}";
       
    }
    echo json_encode($res);
    exit();
?>