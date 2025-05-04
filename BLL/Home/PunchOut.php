<?php
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $userid = MyLIB::GetNumber('userid');

    $res = array();
    $attendanceDAL = new AttendanceDAL();

    $Offtime= "17:00:00";
    $now = date('H:i:s');

        //計算總上班時數
        $punchin=$attendanceDAL->getPunchinByEmployID($userid,date('Y-m-d'));
        if($punchin==null){
            $res['err'] = 1;
            $res['msg'] = "未簽到，請先簽到";
            echo json_encode($res);
            exit();
        }

        $working_seconds = strtotime($now) - strtotime($punchin->Punch_in);
        if (strtotime($punchin->Punch_in)< strtotime('12:00:00')&& strtotime($now) > strtotime('13:00:00')) {
            $working_seconds -= 3600; 
        }
        $working_hours = floor($working_seconds / 3600);
        $working_minutes = floor(($working_seconds % 3600) / 60);
        $working_time = sprintf("%02d:%02d", $working_hours, $working_minutes);


        $result = array(
                        'Attendance_id' => $punchin->Attendance_id,
                        'Employee_id' => $punchin->Employee_id,
                        'Date' => $punchin->Date,
                        'Punch_out' => $now,
                        'Working_hours' => $working_time,
                        'Is_Attend' => 'Checked-out');

        if(!$attendanceDAL->PunchOut($result)){
            throw new Exception("Failed to punchout.");
            $res['err'] = 1;
            $res['msg'] = "簽退失敗，請重新簽到";
            echo json_encode($res);
            exit();
        } 

    $res['err'] = 0;
    $res['msg'] = "簽退成功了~~~";
    echo json_encode($res);
    exit();
?>