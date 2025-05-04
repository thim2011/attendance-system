<?php 

require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
require_once dirname(__FILE__).'/../../DAL/LeaveDetailsDAL.php';
require_once dirname(__FILE__).'/../../DAL/LeaveTypeDAL.php';
require_once dirname(__FILE__).'/../../Utils/Main.php';


$employeeDAL = new EmployeeDAL();
$emLeaveDAL = new EmLeaveDAL();
$leaveDetailDAL = new LeaveDetailsDAL();
$leaveTypeDAL = new LeaveTypeDAL();

$date = MyLIB::GetString('date');

$res=array();
//請假人數

$TodayLeave      = $emLeaveDAL->getLeaveToday2($date);


$workStartTime = strtotime('08:00');
$workEndTime = strtotime('17:00');

$res['resultLeave'] = array();
$LeaveCount = 0;
foreach ($TodayLeave as $value) {
    $userName = $employeeDAL->getEmployeeName($value->Employee_id);
    $leaveType = $leaveTypeDAL->getOneById($value->Leave_type);
    $leaveDetail = $leaveDetailDAL->getDetailsByID($value->Leave_id);

    foreach($leaveDetail as $detail){
        if(strtotime($detail->Date) == strtotime($date)){
            $leaveStartTime = strtotime($detail->Start_time);
            $leaveEndTime = strtotime($detail->End_time);
    
            if ($leaveStartTime <= $workStartTime && $leaveEndTime >= $workEndTime) {
                // Employee is on leave for the entire day
                $attendanceStatus = '整天';
            } elseif ($leaveStartTime <= $workStartTime && $leaveEndTime < $workEndTime) {
                // Employee will come to the company after leave ends
                $attendanceStatus = date('H:i', $leaveEndTime)."之後會在公司";
            } elseif ($leaveStartTime > $workStartTime && $leaveEndTime >= $workEndTime) {
                // Employee will leave the company before leave starts
                $attendanceStatus =  date('H:i', $leaveStartTime)."離開公司";
            } else {
                // Employee has partial leave during the day
                $attendanceStatus = '部分時間' . date('H:i', $leaveStartTime) . '到' . date('H:i', $leaveEndTime);
            }
        }
    }

    $res['resultLeave'][] = array(
        'date'    => $date,
        'Name' => $userName,
        'LeaveType' => $leaveType->Leave_type_name,
        'StartTime' => date('H:i', $leaveStartTime),
        'EndTime' => date('H:i', $leaveEndTime),
        'AttendanceStatus' => $attendanceStatus
    );

    
}
$res['err'] = 0;
    $res['msg'] = "查詢成功";
    echo json_encode($res);
    exit();

?>