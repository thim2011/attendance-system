<?php
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $userid = MyLIB::GetNumber('userid');

    $res = array();
    $attendanceDAL = new AttendanceDAL();
    $employeeDAL = new EmployeeDAL();

    $worktime= "08:00:00";
    $now = date('H:i:s');
    $now > $worktime ? $status = 'Late' : $status = 'On Time';
    
    //先確認是否已經簽到
    $punchin=$attendanceDAL->getPunchinByEmployID($userid, date('Y-m-d'));
    if($punchin!=null){
        $res['err'] = 1;
        $res['msg'] = "已簽到，勿簽到";
        echo json_encode($res);
        exit();
    }
    //查詢分數
    $user = $employeeDAL->getOneById($userid);
    $user->Point += 1;
    $employeeDAL->updateScore($user->Employee_id, $user->Point);
    //打卡
    $punchin = array('Employee_id' => $userid,
                     'Date' => date('Y-m-d'),
                     'Punch_in' => $now,
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
    $res['err'] = 0;
    $res['email'] = $user->Email;
    $res['msg'] = "簽到成功了~~~";
    echo json_encode($res);
?>