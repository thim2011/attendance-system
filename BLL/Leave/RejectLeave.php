<?php
require_once dirname(__FILE__).'/../../Utils/Main.php';
require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
require_once dirname(__FILE__).'/../../DAL/NotificationDAL.php';

$LeaveDAL = new EmLeaveDAL();
$EmployeeDAL = new EmployeeDAL();
$NotificationDAL = new NotificationDAL();

$data = array(
    'Leave_id' => MyLIB::GetNumber('Leave_id'),
    'Employee_id' => MyLIB::GetNumber('Employee_id'),
    'Status' => MyLIB::GetString('Status'),
    'VerifyTime' => date('Y-m-d H:i'),
    'VerifyBy' => MyLIB::GetNumber('VerifyBy'),
    'VerifyReason' => MyLIB::GetString('VerifyReason')
);


$res=array();

$result = $LeaveDAL->VerifyLeave($data);
if(!$result){
    $res['err'] = 1;
    $res['msg'] = "操作錯誤，審核失敗，請重試或通知工程師";
    echo json_encode($res);
    exit();
}
// step 5. 通知人員
$employee= $EmployeeDAL->getOneById($data['Employee_id']);
$notification = array(
    'Employee_id'   => $data['Employee_id'],
    'Message'       => "<strong>".$EmployeeDAL->getEmployeeName($data['VerifyBy'])."</strong>已審核<strong>".($data['Status'] == "Rejected" ?"拒絕" :"同意")."</strong>您的請假單號：<strong>".$data['Leave_id']."</strong>",
    'Noti_type'     => "both",
    'Mes_type'      => "Leave",
    'Leave_id'      => $data['Leave_id'],
    'Status'        => 'unread',
    'Created_by'    => $data['VerifyBy']
);


$_SESSION['pendingLeave'] = $LeaveDAL->countPending();
$insertNotification = $NotificationDAL->insertNotification($notification);
if(!$insertNotification){
    $res['err'] = 1;
    $res['msg'] = "Email通知失敗";
    echo json_encode($res);
    exit();
}

$res['err'] = 0;
$res['email'] = $employee->Email;
$res['msg'] = "請假審核決絕";
echo json_encode($res);
exit();


?>