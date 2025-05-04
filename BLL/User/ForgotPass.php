<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/ResetPasswordDAL.php';

    $EmployeeDAL = new EmployeeDAL();
    $ResetPasswordDAL = new ResetPasswordDAL();

    $email = MyLIB::GetString('email');

    if(empty($email)){
        $res = array();
        $res['err'] = 1;
        $res['msg'] = "電子郵件是空的";
        exit();
    }

    $employee = $EmployeeDAL->getOnebyEmail($email);
    if($employee == null){
        $res = array();
        $res['err'] = 1;
        $res['msg'] = "查無此帳號";
        echo json_encode($res);
        exit();
    }

    $token = bin2hex(random_bytes(32));

    $resetPassword = array(
        'Email' => $email,
        'Token' => $token
    );

    $result = $ResetPasswordDAL->insertResetPassword($resetPassword);
    if(!$result){
        $res = array();
        $res['err'] = 1;
        $res['msg'] = "重設密碼失敗";
        echo json_encode($res);
        exit();
    }

    $res = array();
    $res['err'] = 0;
    $res['msg'] = "請至信箱收取重設密碼信，並點擊連結重設密碼，有效時間為20分鐘，謝謝";
    $res['token'] = $token;
    echo json_encode($res);
?>