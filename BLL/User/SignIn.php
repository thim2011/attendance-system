<?php
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/NotificationDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $account    =   MyLIB::GetString('Account');
    $password   =   MyLIB::GetString('Password');

    $employeeDAL = new EmployeeDAL();
    $notificationDAL = new NotificationDAL();
    $emLeaveDAL = new EmLeaveDAL();

    $res = array();
    $employee = $employeeDAL->Login($account);
    if(!$employee){
        $res['err'] = 1;
        $res['msg'] = "帳號不存在";
        echo json_encode($res);
        exit();
    }
    if($employee->Status == 'INACTIVE'){
        $res['err'] = 1;
        $res['msg'] = "帳號已停用, 請聯絡管理員";
        echo json_encode($res);
        exit();

    }
    
    if(strval($employee->Account) == $account && password_verify($password, $employee->Password))
    {
        $token = bin2hex(random_bytes(16)); // Tạo token ngẫu nhiên 16 byte
    
        setcookie(
            'auth_token_test', 
            $token,       
            time() + (86400 * 30), 
            "/",          // Đường dẫn
            "",           // Tên miền, để trống cho tên miền hiện tại
            false,         // Bảo mật: chỉ gửi qua HTTPS
            true          // HttpOnly: chỉ có thể truy cập qua HTTP (không thể truy cập qua JavaScript)
        );
        $_SESSION['auth_token'] = $token;
        $_SESSION['USERid'] = $employee->Employee_id;
        $_SESSION['USERname'] = $employee->Name;
        $_SESSION['Role'] = $employee->Role;

        $res['err'] = 0;
        $res['message'] =$token;
        $res['msg'] = $_SESSION['auth_token'];

        $notification = $notificationDAL->countUnreadNotification($employee->Employee_id);
        $_SESSION['notification'] = $notification;

        $pendingLeave = $emLeaveDAL->countPending();
        $_SESSION['pendingLeave'] = $pendingLeave;

    }else{
        $res['err'] = 1;
        $res['msg'] = "帳號或密碼錯誤";
    }
    
   
    echo json_encode($res);
   
    ?>