<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/NotificationDAL.php';

    $NotificationDAL = new NotificationDAL();

    $Notification_id = MyLIB::GetNumber('Noti_id');
    $employee =  MyLIB::GetNumber('userId');

    $now  = date('Y-m-d H:i:s');
    if($Notification_id == 0 || $Notification_id == null){
        $res['err'] = 1;
        $res['msg'] = "無效的通知ID";
        echo json_encode($res);
        exit();
    }
    $res = array();

    if(!$NotificationDAL->ReadNotification($Notification_id, $now)){
        $res['err'] = 1;
        $res['msg'] = "讀取失敗";
        echo json_encode($res);
        exit();
    }

    $notification = $NotificationDAL->countUnreadNotification($employee);
    $_SESSION['notification'] = $notification;

    $res['err'] = 0;
    $res['msg'] = "讀取成功";
    echo json_encode($res);
    exit();

?>