<?php
require_once dirname(__FILE__).'/../../Utils/Main.php';
require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
require_once dirname(__FILE__).'/../../DAL/NotificationDAL.php';
require_once dirname(__FILE__).'/../../DAL/LeaveDetailsDAL.php';
require_once dirname(__FILE__).'/../../DAL/LeaveTypeDAL.php';
require_once dirname(__FILE__).'/../../DAL/EmWorkHourDAL.php';
require_once dirname(__FILE__).'/../../DAL/AnnualLeaveDAL.php';

/* 被審核同意后，依序執行以下動作：
1. 更新請假狀態並存入審核人，審核時間,... （Employee_leaves）
2. 更新請假細節 （Leave_details）
3. 更新請假時數 （Employee_workhours）
4. 如果用年假，更新年假時數 （Annual_leave）
5. 通知請假人員
*/
$leave_id   = MyLIB::GetNumber('Leave_id');
$Employee_id = MyLIB::GetNumber('Employee_id');
$status     = MyLIB::GetString('Status');
$verifyBy  = MyLIB::GetNumber('VerifyBy');
$VerifyReason = MyLIB::GetString('VerifyReason');

$LeaveDAL = new EmLeaveDAL();
$EmployeeDAL = new EmployeeDAL();
$NotificationDAL = new NotificationDAL();
$LeaveDetailsDAL = new LeaveDetailsDAL();
$LeaveTypeDAL = new LeaveTypeDAL();
$EmWorkHourDAL = new EmWorkHourDAL();
$AnnualLeaveDAL = new AnnualLeaveDAL();

$res=array();   
$verifyTime = date('Y-m-d H:i');

try{
    $LeaveDAL->beginTransaction();
  
   //step 1. 審核請假
    $data = array('Leave_id' => $leave_id,
                'VerifyBy' => $verifyBy,
                'VerifyReason' => $VerifyReason,
                'VerifyTime' => $verifyTime,
                'Status' => $status);

    $result = $LeaveDAL->VerifyLeave($data);
    if(!$result){
        $res['err'] = 1;
        $res['msg'] = "操作錯誤，審核失敗，請重試或通知工程師";
        echo json_encode($res);
        exit();
    }
    
    // step 2. 更新請假時數    
    $leave_detail = $LeaveDetailsDAL->getDetailsByID($leave_id);
    $monthly_time = [];
    foreach ($leave_detail as $detail) {
        $year = date('Y', strtotime($detail->Date));
        $month = date('m', strtotime($detail->Date));

        $key = $year . '-' . $month;

        if (!isset($monthly_time[$key])) {
            $monthly_time[$key] = 0;
        }
        $monthly_time[$key] += $detail->Total_time;

        if (!$LeaveDetailsDAL->CompleteLeave($leave_id)) {
            throw new Exception("Failed to complete leave.");
        }
    }

    //step 3. 更新請假時數
    $LeaveTotalTime = 0;
    $leave  = $LeaveDAL->getOneByID($leave_id);
    $pay_type = $LeaveTypeDAL->getPayType($leave->Leave_type);

    foreach ($monthly_time as $key => $total_time) {
        list($year, $month) = explode('-', $key);
        $WorkHours = $EmWorkHourDAL->getByEmployeeYM($leave->Employee_id, $year, $month);
        if ($WorkHours == null) {
            $EmWorkHourDAL->insertNew($leave->Employee_id, $year, $month);
            $WorkHours = $EmWorkHourDAL->getByEmployeeYM($leave->Employee_id, $year, $month);
        }

        $update_WorkHour = $EmWorkHourDAL->updateWorktime($WorkHours->WorkTime_id, $pay_type->Pay_type, $total_time);
        if (!$update_WorkHour) {
            throw new Exception("Failed to update work time.");
        }

        $LeaveTotalTime+=$total_time; 
    }
  
    //step 4. 如果用年假，更新年假時數
    if($leave->Leave_type == 13){
        $Annual = $AnnualLeaveDAL->getAnnualLeaveByEmployee($leave->Employee_id, $year, 'reset');
        $Insert_for_Annual = array(
            'Employee_id' => $leave->Employee_id,
            'Year' => $year,
            'Month' => $month,
            'TotalHours' => $Annual->TotalHours,
            'UseHours' => $LeaveTotalTime,
            'Leave_id' => $leave_id,
            'Status' => 'normal'
        );
        $AnnualLeaveDAL->InsertAnnualLeave($Insert_for_Annual);
    }



    $LeaveDAL->commit();
}
catch(Exception $e){
    $LeaveDAL->rollBack();
    $res['err'] = 1;
    $res['msg'] = "開始事務失敗";
    echo json_encode($res);
    exit();
}


 // step 5. 通知人員
    $employee= $EmployeeDAL->getOneById($Employee_id);
    $notification = array(
        'Employee_id'   => $Employee_id,
        'Message'       => "<strong>".$EmployeeDAL->getEmployeeName($verifyBy)."</strong>已審核<strong>".($status == "Rejected" ?"拒絕" :"同意")."</strong>您的請假單號：<strong>".$leave_id."</strong>",
        'Noti_type'     => "both",
        'Mes_type'      => "Leave",
        'Leave_id'      => $leave_id,
        'Status'        => 'unread',
        'Created_by'    => $verifyBy
    );


    $_SESSION['pendingLeave'] = $LeaveDAL->countPending();
    $insertNotification = $NotificationDAL->insertNotification($notification);
    if(!$insertNotification){
        $res['err'] = 1;
        $res['msg'] = "通知失敗";
        echo json_encode($res);
        exit();
    }

$res['err'] = 0;
$res['msg'] ="";
$res['email'] = $employee->Email;
$res['msg'] = "請假審核同意";
echo json_encode($res);
exit();
?>