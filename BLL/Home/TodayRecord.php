<?php

    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $userid = MyLIB::GetNumber('userid');

    $attendanceDAL = new AttendanceDAL();
    $employeeDAL = new EmployeeDAL();
    $emLeaveDAL = new EmLeaveDAL();


    $res=array();
    //請假人數
    $todayLeaveCount = $emLeaveDAL->getLeaveCountToday();
    
    //打卡紀錄
    $date   = date('Y-m-d');
    $TodayRecord = $attendanceDAL->getTodayRecord($date);
    $PersonalPunch = $attendanceDAL->getPersonalPunch($userid, $date);
    if (is_object($PersonalPunch) && property_exists($PersonalPunch, 'Is_Attend')) {
        $personalPunchStatus = $PersonalPunch->Is_Attend === 'null' ? 'none' : $PersonalPunch->Is_Attend;
        $personalPunchIn = $personalPunchStatus==='none'?'未簽到':$PersonalPunch->Punch_in;
    } else{
        $personalPunchStatus = 'none';
        $personalPunchIn = '未簽到';
    }
    /*if($TodayRecord==null && $todayLeaveCount==null){
        $res['err'] = 0;
        $res['msg'] = "查詢無資料";
        $res['result'] = 0;
        echo json_encode($res);
        exit();
    }*/

    $res['result'] = array();
    $i = 0;
    foreach ($TodayRecord as $value) {
        
        $user = $employeeDAL->getOneById($value->Employee_id);
    
        $res['result'][] = array(
            'id'    => ++$i,
            'Employee_id' => $value->Employee_id,
            'Name' => $user->Name,
            'Date' => $value->Date,
            'PunchIn' => $value->Punch_in,
            'PunchOut' => $value->Punch_out,
            'Status' => $value->Status
        );
    }

    $res['err'] = 0;
    $res['msg'] = "查詢成功";
    $res['CountPunchIn'] = $i;//打卡人數
    $res['CountLeave'] = $todayLeaveCount;//請假人數
    $res['PersonalPunch'] = $personalPunchStatus;
    $res['PersonalPunchTime'] = $personalPunchIn;
    echo json_encode($res);
    exit();
?>    
    