<?php 
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $userId = MyLIB::GetNumber('userid');
    $yearmonth = MyLIB::GetString('PersonalMonth');

    $employeeDAL = new EmployeeDAL();
    $attendanceDAL = new AttendanceDAL();

    
    $result = $attendanceDAL->getPersonalRecordbyYearMonth($userId, $yearmonth);

    if($result==null){
        $res['err'] = 0;
        $res['msg'] = "查詢無資料";
        $res['result'] = null;
        echo json_encode($res);
        exit();
    }

    $res['result'] = array();
    foreach ($result as $value) {
        // Lấy thông tin của nhân viên
        $userName = $employeeDAL->getEmployeeName($value->Employee_id);
    
        $res['result'][] = array(
            'Attendance_id' => $value->Attendance_id,
            'Name' => $userName,
            'Date' => $value->Date,
            'PunchIn' => $value->Punch_in,
            'PunchOut' => $value->Punch_out,
            'Status' => $value->Status,
        );
    }
    


    $res['err'] = 0;
    $res['msg'] = "查詢成功";
    if($result!=null){
        
    }else{
        $res['result'] = null;
    }
    echo json_encode($res);
    exit();
?>