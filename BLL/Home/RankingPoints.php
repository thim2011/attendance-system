<?php
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';

    $employeeDAL = new EmployeeDAL();

    $result = $employeeDAL->getRankingScore();
    if($result==null){
        $res['err'] = 1;
        $res['msg'] = "查詢無資料";
        $res['result'] = null;
        echo json_encode($res);
        exit();
    }
    
    $res = array();
    $res['err'] = 0;
    $res['msg'] = "查詢成功";
    $res['result'] = $result;
    echo json_encode($res);
?>