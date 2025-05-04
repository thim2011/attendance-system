<?php 
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $EmLeaveDAL = new EmLeaveDAL();
    $res=array();

    $result = $EmLeaveDAL->getLeaveCountByMonth();
    if(!$result){
        $res['err'] = 1;
        $res['msg'] = "查詢失敗";
        echo json_encode($res);
        exit();
    }

    $res['err'] = 0;
    $res['msg'] = "success";
    $res['result'] = $result;
    echo json_encode($res);
    exit();
?>