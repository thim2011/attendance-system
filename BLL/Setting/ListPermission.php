<?php 

    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $EmployeeDAL = new EmployeeDAL();

    $ALLemployees = $EmployeeDAL->getAllforPermission();

    foreach ($ALLemployees as $employee) {
        $Button = "<button class='btn btn-secondary editpermissions' data-employee-id='$employee->Employee_id'><i class='fa-solid fa-edit' style='font-size:12px'></i>編輯</button>";
        $Button .= $employee->Status == 'ACTIVE'?" <button class='btn btn-danger toggleEmployBtn' data-toggle='INACTIVE' data-employee-id='$employee->Employee_id'><i class='fa-solid fa-user-slash' style='font-size:12px'></i>停用</button></td>"
                                            : " <button class='btn btn-warning toggleEmployBtn' data-toggle='ACTIVE' data-employee-id='$employee->Employee_id'><i class='fa-solid fa-user-check' style='font-size:12px'></i>啟用</button></td>";        
        $role=$employee->Role== 1 ? "管理員 <i class='fa-solid fa-user-tie'></i>" : "使用者 <i class='fa-solid fa-users'></i>";
        $status = $employee->Status == "ACTIVE" ? "<i class='fa-solid fa-circle' style='color:green; font-size:8px'></i> 使用中" : "<i class='fa-solid fa-circle' style='color:red; font-size:8px'></i> 已停用";

        $employee->Role = $role;
        $employee->Status= $status;
        $employee->button = $Button;
    }

    $res = array();
    $res['err'] = 0;
    $res['msg'] = "查詢成功";
    $res['result'] = $ALLemployees;
    echo json_encode($res);
    exit();
?>