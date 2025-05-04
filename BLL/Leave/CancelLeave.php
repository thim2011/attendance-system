<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';

    $EmLeaveDAL = new EmLeaveDAL();

    $Leave_id = MyLIB::GetNumber('leave_id');

    $res = array();

    if(!$EmLeaveDAL->CancelLeave($Leave_id)){
        $res['err'] = 1;
        $res['msg'] = "取消失敗";
        echo json_encode($res);
    }

    $res['err'] = 0;
    $res['msg'] = "取消成功";
    echo json_encode($res);
    exit();
    ?>