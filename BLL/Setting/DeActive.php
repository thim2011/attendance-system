<?php 
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $EmployeeDAL = new EmployeeDAL();
    $Employee_id = MyLIB::GetNumber('Employee_id');
    $Toggle      = MyLIB::GetString('toggle');
    $res = array();

    if($Employee_id==null || $Toggle == null){
        $res['err'] = 1;
        $res['msg'] = "員工編號為空，無法辨識";
        echo json_encode($res);
        exit();
    }

    $updateStatus = $EmployeeDAL->updateStatus($Employee_id, $Toggle);
    if(!$updateStatus){
        $res['err'] = 1;
        $res['msg'] = "操作失敗, 請再試一次";
        echo json_encode($res);
        exit();
    }

        $res['err'] = 0;
        $res['msg'] = $Toggle == "ACTIVE"?"啓用帳號成功":"停用帳號成功";
    echo json_encode($res);
        exit();
?>