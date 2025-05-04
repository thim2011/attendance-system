<?php 

    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/FixedLeaveDAL.php';
    $EmployeeDAL = new EmployeeDAL();
    $FixedLeaveDAL = new FixedLeaveDAL();
    $ALLemployees = array();

    $ALLemployees = $FixedLeaveDAL->getAll();

    
    foreach ($ALLemployees as $employee) {
        $employee_id = $employee->Employee_id;
        $employee_name = $EmployeeDAL->getEmployeeName($employee_id);
        $employee->Employee_name = $employee_name;
    }


    $res = array();
    $res['err'] = 0;
    $res['msg'] = "查詢成功";
    $res['result'] = $ALLemployees;
    echo json_encode($res);
    exit();
?>