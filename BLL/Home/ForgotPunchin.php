<?php 
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $attendanceDAL = new AttendanceDAL();
    $employeeDAL = new EmployeeDAL();

    $employee_id = MyLIB::GetNumber('employee_id');
    $type = MyLIB::GetString('forgot-punchin-type');
    $date = MyLIB::GetString('forgot-punchin-date');
    $time = MyLIB::GetString('forgot-punchin-time');

    $today = date("Y-m-d");

    $res=array();

    if($employee_id==null || $type==null || $date==null || $time==null){
        $res['err'] = 1;
        $res['msg'] = "請確認所有欄位是否填寫";
        echo json_encode($res);
        exit();
    }

    if($date > $today){
        $res['err'] = 1;
        $res['msg'] = "無法執行，日期不可大於今天";
        echo json_encode($res);
        exit();
    }else if($date == $today){
        $res['err'] = 1;
        $res['msg'] = "無法執行，不可補打當天的卡";
        echo json_encode($res);
        exit();
    }
    

    if($type == 'punchin')
    {
        $punchin=$attendanceDAL->getPunchinByEmployID($employee_id, $date);
        if($punchin!=null){
            $res['err'] = 1;
            $res['msg'] = "已有簽到，勿簽到";
            echo json_encode($res);
            exit();
        }

        $status = $time > '08:00:00' ? $status = 'Late' : $status = 'On Time';
        $punchin = array('Employee_id' => $employee_id,
                     'Date' => $date,
                     'Punch_in' => $time,
                     'Punch_out' => '',
                     'Status' => $status,
                     'Working_hours' => 0,
                     'Is_Attend' => 'Checked-in',
                     'Is_leave' => 0,
                     'Leave_id' => 0);

        $result = $attendanceDAL->Punchin($punchin);
        if(!$result){
            $res['err'] = 1;
            $res['msg'] = "打卡失敗";
            echo json_encode($res);
            exit();
        }

    }else{
        $result = $attendanceDAL->ForgotPunch($employee_id, $type, $date, $time);
        if($result==null){
            $res['err'] = 1;
            $res['msg'] = "補打卡失敗，請再試一次";
            echo json_encode($res);
            exit();
        }

        $Attendance = $attendanceDAL->getPunchinByEmployID($employee_id, $date);
        if(!$Attendance){
            $res['err'] = 1;
            $res['msg'] = "更新時間失敗，請再試一次";
            echo json_encode($res);
            exit();
        }

        $working_seconds = strtotime($Attendance->Punch_out) - strtotime($Attendance->Punch_in);

        if (strtotime($Attendance->Punch_in)< strtotime('12:00:00')&& strtotime($Attendance->Punch_out) > strtotime('13:00:00')) {
            $working_seconds -= 3600; 
        }
        
        $working_hours = floor($working_seconds / 3600);
        $working_minutes = floor(($working_seconds % 3600) / 60);
        $working_time = sprintf("%02d:%02d", $working_hours, $working_minutes);

        $result = $attendanceDAL->UpdateWorkingHours($Attendance->Attendance_id, $working_time);
        if(!$result){
            $res['err'] = 1;
            $res['msg'] = "更新時間失敗，請再試一次";
            echo json_encode($res);
            exit();
        }
    }
    

    $res['err'] = 0;
    $res['msg'] = "補打卡成功，請自行確認";
    echo json_encode($res);
    exit();
    ?>