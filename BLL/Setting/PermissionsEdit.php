<?php 
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $EmployeeDAL = new EmployeeDAL();
    $Employee_id = MyLIB::GetNumber('employID_hide');
    $Permissions = MyLIB::GetNumber('permissions-select');

    $updatePermissions = $EmployeeDAL->updateRole($Employee_id, $Permissions);

    $res = array();
    if($updatePermissions){
        $res['err'] = 0;
        $res['msg'] = "修改成功";
    }else{
        $res['err'] = 1;
        $res['msg'] = "修改操作失敗";
    }
    echo json_encode($res);
        exit();
    ?>