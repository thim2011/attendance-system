<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';

    $attendanceDAL = new AttendanceDAL();

    $attendance_id = MyLIB::GetNumber('Attendance_id');
    $punchin = MyLIB::GetString('Punchin_time');
    $punchout = MyLIB::GetString('Punchout_time');

    $res=array();

    if($attendance_id==null || $punchin==null || $punchout==null){
        $res['err'] = 1;
        $res['msg'] = "請確認所有欄位是否填寫";
        echo json_encode($res);
        exit();
    }
    $result = $attendanceDAL->EditPunchtime($attendance_id, $punchin, $punchout);
    if(!$result){
        $res['err'] = 1;
        $res['msg'] = "修改失敗";
        echo json_encode($res);
        exit();
    }

    //更新工時
    $Attendance = $attendanceDAL->getOnebyID($attendance_id);
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

    $res['err'] = 0;
    $res['msg'] = "修改成功";
    echo json_encode($res);
    exit();
    ?>
