<?php
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveDetailsDAL.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveTypeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $EmLeaveDAL = new EmLeaveDAL();
    $LeaveDetailsDAL = new LeaveDetailsDAL();
    $LeaveTypeDAL = new LeaveTypeDAL();
    $EmployeeDAL = new EmployeeDAL();

    $Leavestatus = MyLIB::GetString('leaveStatus');
    $time_start = MyLIB::GetString('time_start');
    $time_end = MyLIB::GetString('time_end');
    $employee_id = MyLIB::GetNumber('employee_id');

    $leaveList = $EmLeaveDAL->getLeave($employee_id, $time_start, $time_end, $Leavestatus,);
    if(!$leaveList){
        $res['err'] = 0;
        $res['msg'] = "查無資料";
        echo json_encode($res);
        exit();
    }



foreach ($leaveList as $leave) {
    $typeName = $LeaveTypeDAL->getLeaveTypeName($leave->Leave_type);
    $employee = $EmployeeDAL->getOneById($leave->Employee_id);
    $leavedetail = $LeaveDetailsDAL->getDetailsByID($leave->Leave_id);
    if($leave->Status!= 'Pending') {$verifyBy = $EmployeeDAL->getOneById($leave->VerifyBy);}
    
    $leaveData = array(
        'Leave_id'      => $leave->Leave_id,
        'Employee_id'   => $leave->Employee_id,
        'Employee_name' => $employee->Name,
        'Leave_type'    => $typeName,
        'Start_date'    => $leave->Start_date,
        'End_date'      => $leave->End_date,
        'Reason'        => $leave->Reason,
        'Total_day'    => $leave->Total_day,
        'VerifyBy'      => ($leave->Status!= 'Pending' && $leave->Status!= 'Cancelled')? $verifyBy->Name : '尚未審核',
        'Status'        => $leave->Status,   
        'details'       => []
    );

    foreach($leavedetail as $detail){
        $leaveData['details'][] = array(
            'LeaveDetail_id' => $detail->LeaveDetail_id,
            'Leave_id'       => $detail->Leave_id,
            'Date'           => $detail->Date,
            'Start_time'     => $detail->Start_time,
            'End_time'       => $detail->End_time,
            'Total_time'     => $detail->Total_time,
            'Status'         => $detail->Status
        );
    }

    $result[] = $leaveData;
}


    $res=array();
    $res['err'] = 0;
    $res['msg'] = "success";
    $res['result'] = $result;
    echo json_encode($res);
    exit();
?>