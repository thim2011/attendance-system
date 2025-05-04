<?php 
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveDetailsDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $emLeaveDAL = new EmLeaveDAL();
    $leaveDetailsDAL = new LeaveDetailsDAL();
    $employeeDAL = new EmployeeDAL();

    $userid     = MyLIB::GetNumber('userID');
    $userName   = MyLIB::GetString('name');
    $startdate  = MyLIB::GetString('holiday-startdate');
    $enddate    = MyLIB::GetString('holiday-enddate');
    $leavetype  = MyLIB::GetString('holiday-select');
    $reason     = MyLIB::GetString('holiday-reason');
    $days       = MyLIB::GetNumber('days');

    
    $res = array();
    $res['msg'] = "";
    #reigon 請假資料 employee_leaves
    $employee_leaves = array(
        'Employee_id' => $userid,
        'Leave_type'  => $leavetype,
        'Start_date'  => $startdate,
        'End_date'    => $enddate,
        'Total_day'   => $days,
        'Reason'      => $reason,
        'Status'      => "Pending"
    );
    

    #region 請假詳細資料 leave_details

    $i = 0;
    $time = array();
    $startDatestamp = strtotime($startdate);
    $endDatestamp = strtotime($enddate);

    if ($startDatestamp && $endDatestamp && $startDatestamp <= $endDatestamp) {
        while ($startDatestamp <= $endDatestamp) {
            $currentDate = date('Y-m-d', $startDatestamp);
            
            $time[$i] = array(
                'Date' => $currentDate,
                'Start_time' => MyLIB::GetString('holiday-starttime-'.$i) ?? '08:00',
                'End_time' => MyLIB::GetString('holiday-endtime-'.$i) ?? '17:00'
            );
            
            $startDatestamp = strtotime("+1 day", $startDatestamp);
            $i++;
        }
    } else {
        $res['err'] = 1;
        $res['msg'] = "開始請假跟請假結束日期有問題";
        echo json_encode($res);
        exit();
    }


    // 檢查如果有重複的請假日期或時間，通過後才進行存入資料庫
    foreach($time as $key => $value){
        $is_Duplicate=$emLeaveDAL->checkifDuplicate($userid, $value['Date'], $value['Start_time'], $value['End_time']);
        if($is_Duplicate!=''){
            foreach($is_Duplicate as $key => $value){
                $res['msg'] .= "請假失敗，請假時間有重複到請假單編號".$value->Leave_id.",日期".$value->Date."時間".$value->Start_time."到".$value->End_time."請再次確認";
            }
        }
    }

    if($res['msg']!=""){
        $res['err'] = 1;
        echo json_encode($res);
        exit();
    }

    //------------------------------------------------------
    $result = $emLeaveDAL->insertLeave($employee_leaves);
    if(!$result){
        $res['err'] = 1;
        $res['msg'] = "請假失敗";
        echo json_encode($res);
        exit();
    }

    $leave_id = $result;
    $break_start = strtotime('12:00');
    $break_end = strtotime('13:00');
    foreach($time as $key => $value){
        $start_time = strtotime($value['Start_time']);
        $end_time = strtotime($value['End_time']);
        $total_seconds = 0;

        if ($start_time < $break_start && $end_time > $break_end) {
            $total_seconds = ($end_time - $start_time) - ($break_end - $break_start);
        } elseif ($start_time < $break_start && $end_time > $break_start) {
            // Trường hợp 2: Thời gian bắt đầu trước giờ nghỉ và kết thúc trong giờ nghỉ
            $total_seconds = $break_start - $start_time;
        } elseif ($start_time >= $break_end) {
            // Trường hợp 3: Thời gian bắt đầu sau giờ nghỉ
            $total_seconds = $end_time - $start_time;
        } else {
            // Trường hợp 4: Thời gian bắt đầu và kết thúc trước giờ nghỉ
            $total_seconds = $end_time - $start_time;
        }

        $hours = floor($total_seconds / 3600);
        $leave_details = array(
            'Leave_id'  => $leave_id,
            'Date'      => $value['Date'],
            'Start_time'=> $value['Start_time'],
            'End_time'  => $value['End_time'],
            'Total_time'=> $hours,
            'Status'    => "OFF"
        );

    $result = $leaveDetailsDAL->insertLeaveDetails($leave_details);

    if(!$result){
        $res['err'] = 1;
        $res['msg'] = "請假失敗";
        echo json_encode($res);
        exit();
    }
}
#endregion
$user = $employeeDAL->getOneById($userid);

    $res['err'] = 0;
    $res['msg'] = "新增請假成功了~~~, 等審核哦";
    $res['email'] = $user->Email;
    echo json_encode($res);
    exit();
?>