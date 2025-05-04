<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveDetailsDAL.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveTypeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmWorkHourDAL.php';
    require_once dirname(__FILE__).'/../../DAL/AnnualLeaveDAL.php';

    $LeaveDAL = new EmLeaveDAL();
    $LeaveDetailsDAL = new LeaveDetailsDAL();
    $LeaveTypeDAL = new LeaveTypeDAL();
    $EmWorkHourDAL = new EmWorkHourDAL();
    $AnnualLeaveDAL = new AnnualLeaveDAL();

    $leaveid = MyLIB::GetNumber('leave_id');
    $res = array();

    /*----------------------以下是一條龍-------------- */
//取得基本資料-------------------------------------------------
    $leave  = $LeaveDAL->getOneByID($leaveid);
    $leave_detail = $LeaveDetailsDAL->getDetailsByID($leaveid);

//判斷請假類型，並更新Leave_details, Employee_leaves, Employee_Workhours 裏的請假時數-------------------------------------------------
    $pay_type = $LeaveTypeDAL->getPayType($leave->Leave_type);

    try{
        $LeaveDAL->beginTransaction();

        $leave_status = $LeaveDAL->CompleteLeave($leaveid);
        if (!$leave_status) {
            throw new Exception("Failed to complete leave.");
        }
    
        $monthly_time = [];
        foreach ($leave_detail as $detail) {
            $year = date('Y', strtotime($detail->Date));
            $month = date('m', strtotime($detail->Date));

            $key = $year . '-' . $month;

            if (!isset($monthly_time[$key])) {
                $monthly_time[$key] = 0;
            }
            $monthly_time[$key] += $detail->Total_time;

            if (!$LeaveDetailsDAL->CompleteLeave($leaveid)) {
                throw new Exception("Failed to complete leave.");
            }
        }

        $LeaveTotalTime = 0;
        foreach ($monthly_time as $key => $total_time) {
            list($year, $month) = explode('-', $key);
            $WorkHours = $EmWorkHourDAL->getByEmployeeYM($leave->Employee_id, $year, $month);
            if ($WorkHours == null) {
                $EmWorkHourDAL->insertNew($leave->Employee_id, $year, $month);
                $WorkHours = $EmWorkHourDAL->getByEmployeeYM($leave->Employee_id, $year, $month);
            }

            $update_status = $EmWorkHourDAL->updateWorktime($WorkHours->WorkTime_id, $pay_type->Pay_type, $total_time);
            if (!$update_status) {
                throw new Exception("Failed to update work time.");
            }

            $LeaveTotalTime+=$total_time; 
        }

        //在Annual_leave裏更新年假時數-------------------------------------------------
        if($leave->Leave_type == 13){
            // $annual_leave = $WorkHours->AnnualLeave - $total_time;
            // $EmWorkHourDAL->updateSomeColumn($annual_leave);
            $Annual = $AnnualLeaveDAL->getAnnualLeaveByEmployee($leave->Employee_id, $year, 'reset');
            $Insert_for_Annual = array(
                'Employee_id' => $leave->Employee_id,
                'Year' => $year,
                'Month' => $month,
                'TotalHours' => $Annual->TotalHours,
                'UseHours' => $LeaveTotalTime,
                'Leave_id' => $leaveid,
                'Status' => 'normal'
            );
            $AnnualLeaveDAL->InsertAnnualLeave($Insert_for_Annual);
        }
    
        // Commit giao dịch
        $LeaveDAL->commit();
    
        $res['WorkHour'] = "更新時數完成!";
        $res['Leave'] = "更改狀態完成!";

    }catch(Exception $e){
        $LeaveDAL->rollBack();
        $res['err'] = 1;
        $res['msg'] = "請假失敗";
        echo json_encode($res);
        exit();
    }

    $res['err'] = 0;
    $res['msg'] = "請假完成！!";
    echo json_encode($res);
    exit();
?>