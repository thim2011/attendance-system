<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/ResetPasswordDAL.php';

    $EmployeeDAL = new EmployeeDAL();
    $ResetPasswordDAL = new ResetPasswordDAL();

    $newPass = MyLIB::GetString('newPass');
    $token = MyLIB::GetString('token');

    if(empty($newPass) || empty($token)){
        $res = array();
        $res['err'] = 1;
        $res['msg'] = "新密碼是空的";
        echo json_encode($res);
        exit();
    }

    $VerifyToken = $ResetPasswordDAL->getEmailbyToken($token);

    if($VerifyToken == null){
        $res = array();
        $res['err'] = 1;
        $res['msg'] = "查無此Token";
        echo json_encode($res);
        exit();
    }

    $employee = $EmployeeDAL->getOnebyEmail($VerifyToken->Email);
    if($employee != null){
        $data = array('Email' => $VerifyToken->Email,
                      'Password' => password_hash($newPass, PASSWORD_BCRYPT));
        $result = $EmployeeDAL->resetPassword($data);
        if(!$result){
            $res = array();
            $res['err'] = 1;
            $res['msg'] = "更新密碼失敗";
            echo json_encode($res);
            exit();
        }
    }

    $res = array();
    $res['err'] = 0;
    $res['msg'] = "更新密碼成功";
    $res['email'] = $VerifyToken->Email;
    echo json_encode($res);
    exit();

?>