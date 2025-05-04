<?php 
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $attendanceDAL = new AttendanceDAL();
    $employeeDAL = new EmployeeDAL();

    $res=array();

    $count = $attendanceDAL->getForgotPunch(true);
    $result = $attendanceDAL->getForgotPunch(false);
    if($count==null || $result==null){
        $res['err'] = 0;
        $res['msg'] = "查詢無資料";
        $res['result'] = null;
        echo json_encode($res);
        exit();
    }


    $res['err'] = 0;
    $res['msg'] = "查詢成功";
    $res['count'] = $count;
    $res['result'] = $result;
    echo json_encode($res);

?>