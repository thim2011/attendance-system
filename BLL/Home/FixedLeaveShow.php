<?php 
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/FixedLeaveDAL.php';
    $EmployeeDAL = new EmployeeDAL();
    $FixedLeaveDAL = new FixedLeaveDAL();
    $ALLemployees = array();

    $ALLemployees = $FixedLeaveDAL->getAll();
    $result = array();

    foreach ($ALLemployees as $employee) {
        $employee_id = $employee->Employee_id;
        $employee_name = $EmployeeDAL->getEmployeeName($employee_id);
        
        // Tạo chuỗi tóm tắt ngày nghỉ
        $leaveText = array();
        
        // Kiểm tra thứ Hai
        if ($employee->Monday_AM == 1 && $employee->Monday_PM == 1) {
            $leaveText[] = "（一）全";
        } elseif ($employee->Monday_AM == 1) {
            $leaveText[] = "（一）早";
        } elseif ($employee->Monday_PM == 1) {
            $leaveText[] = "（一）午";
        }
        
        // Kiểm tra thứ Ba
        if ($employee->Tuesday_AM == 1 && $employee->Tuesday_PM == 1) {
            $leaveText[] = "（二）全";
        } elseif ($employee->Tuesday_AM == 1) {
            $leaveText[] = "（二）早";
        } elseif ($employee->Tuesday_PM == 1) {
            $leaveText[] = "（二）午";
        }
        
        // Kiểm tra thứ Tư
        if ($employee->Wednesday_AM == 1 && $employee->Wednesday_PM == 1) {
            $leaveText[] = "（三）全";
        } elseif ($employee->Wednesday_AM == 1) {
            $leaveText[] = "（三）早";
        } elseif ($employee->Wednesday_PM == 1) {
            $leaveText[] = "（三）午";
        }
        
        // Kiểm tra thứ Năm
        if ($employee->Thursday_AM == 1 && $employee->Thursday_PM == 1) {
            $leaveText[] = "（四）全";
        } elseif ($employee->Thursday_AM == 1) {
            $leaveText[] = "（四）早";
        } elseif ($employee->Thursday_PM == 1) {
            $leaveText[] = "（四）午";
        }
        
        // Kiểm tra thứ Sáu
        if ($employee->Friday_AM == 1 && $employee->Friday_PM == 1) {
            $leaveText[] = "（五）全";
        } elseif ($employee->Friday_AM == 1) {
            $leaveText[] = "（五）早";
        } elseif ($employee->Friday_PM == 1) {
            $leaveText[] = "（五）午";
        }
        
        // Tạo đối tượng kết quả chỉ với thông tin cần thiết
        $employeeData = new stdClass();
        $employeeData->Employee_id = $employee_id;
        $employeeData->Employee_name = $employee_name;
        
        // Kiểm tra nếu không có ngày nghỉ nào
        if (empty($leaveText)) {
            $employeeData->LeaveSummary = "無";
        } else {
            $employeeData->LeaveSummary = implode(" ", $leaveText);
        }
        
        $result[] = $employeeData;
    }

    $res = array();
    $res['err'] = 0;
    $res['msg'] = "查詢成功";
    $res['result'] = $result;
    echo json_encode($res);
    exit();
?>