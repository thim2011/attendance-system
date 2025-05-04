<?php
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $email      =   MyLIB::GetString('Email');
    $account    =   MyLIB::GetString('Account');
    $password   =   MyLIB::GetString('Password');
    $Re_password   =   MyLIB::GetString('Re-Password');
    $firstName  =   MyLIB::GetString('FirstName');
    $lastName   =   MyLIB::GetString('LastName');
    $position   =   MyLIB::GetString('Position');
    $department =   MyLIB::GetString('Department');

    $employeeDAL = new EmployeeDAL();
    
    $res = array();

    if($password != $Re_password){
        $res['err'] = 1;
        $res['msg'] = "密碼不一致";
        echo json_encode($res);
        exit();
    }

    if($employeeDAL->IsExist('Account',$account)){
        $res['err'] = 1;
        $res['msg'] = "帳號已存在";
        echo json_encode($res);
        exit();
    };

    if($employeeDAL->IsExist('Email', $email)){
        $res['err'] = 1;
        $res['msg'] = "電子郵件已存在";
        echo json_encode($res);
        exit();
    };

    $params = array('Email'     => $email, 
                    'Account'   => $account, 
                    'Password'  => password_hash($password, PASSWORD_BCRYPT), 
                    'Name'      => $firstName.$lastName, 
                    'Position'  => $position, 
                    'Department'=> $department);

    $employeeDAL->insertEmployee($params);

	
	
    $res['err'] = 0;
    $res['msg'] = "註冊成功";

    echo json_encode($res);
    exit();
?>