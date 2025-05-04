<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveTypeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/HolidaysDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';

    $Employee   = MyLIB::GetString('Employee');
    $StartDate  = MyLIB::GetString('StartDate');
    $EndDate    = MyLIB::GetString('EndDate');
    $Language   = MyLIB::GetString('Language');
    $isChart    = MyLIB::GetNumber('isChart');

    $res=array();

    $EmployeeDAL    = new EmployeeDAL();
    $AttendanceDAL  = new AttendanceDAL();
    $LeaveTypeDAL   = new LeaveTypeDAL();
    $HolidaysDAL    = new HolidaysDAL();
    $EmLeaveDAL     = new EmLeaveDAL();

    $dates = createDateRange($StartDate, $EndDate);

    $Attendance = $AttendanceDAL->getAttendance($Employee, $StartDate, $EndDate);
    if($Attendance == null){
        $res['err'] = 1;
        $res['msg'] = '出席查無資料';
        echo json_encode($res);
        exit();
    }

    $Leave = $EmLeaveDAL->getDataforExcel($Employee, $StartDate, $EndDate);

    $HolidayDate = $HolidaysDAL->getTimeforExcel($StartDate, $EndDate);

    if($Employee == 'all'){
        $countAllEmployee = $EmployeeDAL->countUniqueEmployee();
        foreach($countAllEmployee as $OnlyEmployee){
            foreach ($dates as $date) {

                $entry = [
                    'Employee_id' => $OnlyEmployee->Employee_id,
                    'Date' => $date,
                    'DayofWeek' => getDayOfWeek($date),  
                    'Name' => $EmployeeDAL->getEmployeeName($OnlyEmployee->Employee_id),
                    'Punch_in' => '',
                    'Punch_out' => '',
                    'IsLeaveMorning' => '',
                    'IsLeaveEvening' => ''
                ];

                foreach ($Attendance as $attend) {  
                    if ($attend->Date === $date && $attend->Employee_id === $OnlyEmployee->Employee_id) {        
                        $entry['Punch_in'] = $attend->Punch_in;
                        $entry['Punch_out'] = $attend->Punch_out == '00:00:00' ? '' : $attend->Punch_out;
                    }
                }

                if($HolidayDate != null){
                    foreach($HolidayDate as $Holiday){
                        if($date === $Holiday->Date){
                            $entry['IsLeaveMorning'] .= ' ('.$Holiday->Name.')';
                            $entry['IsLeaveEvening'] .= ' ('.$Holiday->Name.')';
                        }
                    }
                }

                if($Leave != null){
                    foreach ($Leave as $leave) {  
                        if ($leave->Date === $date && $leave->Employee_id === $OnlyEmployee->Employee_id) {
                            if($leave->Leave_type_id == 14){
                                $entry['IsLeaveMorning'] .= '('.$leave->Leave_type_name.')';
                                $entry['IsLeaveEvening'] .= '('.$leave->Leave_type_name.')';
                            }
                             else{
                                $IsLeave ='（請假）';
                                if($leave->Start_time < '12:00:00' && $leave->End_time < '13:00:00'){
                                    $entry['IsLeaveMorning'] .= $IsLeave;
                                }else if($leave->Start_time < '12:00:00' && $leave->End_time > '13:00:00'){
                                    $entry['IsLeaveMorning'] .= $IsLeave;
                                    $entry['IsLeaveEvening'] .= $IsLeave;
                                }
                                else{
                                    $entry['IsLeaveEvening'] .= $IsLeave;
                                }
                            } 
                        }
                    }
                } 

                $res['data'][$OnlyEmployee->Employee_id][] = $entry;
            }
        }
        
    }
    else{
        $OnlyEmployee=[];
        foreach ($dates as $date) {

            $entry = [
                'Employee_id' => $Employee,
                'Date' => $date,
                'DayofWeek' => getDayOfWeek($date),  
                'Name' => $EmployeeDAL->getEmployeeName($Employee),
                'Punch_in' => '',
                'Punch_out' => '',
                'IsLeaveMorning' => '',
                'IsLeaveEvening' => ''
            ];

            foreach ($Attendance as $attend) {  
                if ($attend->Date == $date && $attend->Employee_id == $Employee) {        
                    $entry['Punch_in'] = $attend->Punch_in;
                    $entry['Punch_out'] = $attend->Punch_out == '00:00:00' ? '' : $attend->Punch_out;
                }
            }

            if($HolidayDate != null){
                foreach($HolidayDate as $Holiday){
                    if($date === $Holiday->Date){
                        $entry['IsLeaveMorning'] .= ' ('.$Holiday->Name.')';
                        $entry['IsLeaveEvening'] .= ' ('.$Holiday->Name.')';
                    }
                }
            }

            if($Leave != null){
                foreach ($Leave as $leave) {  
                    if ($leave->Date == $date && $leave->Employee_id == $Employee) {     
                        if($leave->Leave_type_id == 14){
                            $entry['IsLeaveMorning'] .= '('.$leave->Leave_type_name.')';
                            $entry['IsLeaveEvening'] .= '('.$leave->Leave_type_name.')';
                        }
                         else{
                            $IsLeave ='（請假）';
                            if($leave->Start_time < '12:00:00' && $leave->End_time < '13:00:00'){
                                $entry['IsLeaveMorning'] .= $IsLeave;
                            }else if($leave->Start_time < '12:00:00' && $leave->End_time > '13:00:00'){
                                $entry['IsLeaveMorning'] .= $IsLeave;
                                $entry['IsLeaveEvening'] .= $IsLeave;
                            }
                            else{
                                $entry['IsLeaveEvening'] .= $IsLeave;
                            }
                        }
                    }

                }
            }

            $OnlyEmployee[$Employee][]= $entry;
        }
    
        $res['data'] = $OnlyEmployee;
    }
    
    function getDayOfWeek($dateString) {
        $date = new DateTime($dateString);
        $daysOfWeek = array("日", "一", "二", "三", "四", "五", "六");
        $dayOfWeekIndex = $date->format('w');
        return $daysOfWeek[$dayOfWeekIndex];
    }

    function createDateRange($start, $end) {
        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            new DateTime($end . ' +1 day') 
        );
    
        $dateRange = [];
        foreach ($period as $date) {
            if ($date->format('N') < 6) {
                $dateRange[] = $date->format('Y-m-d');
            }
        }
        return $dateRange;
    }
    
    $filename = ($Employee == 'all' ? '全部員工' : $EmployeeDAL->getEmployeeName($Employee)).' '.$StartDate.'到'.$EndDate.'上下班打卡記錄';

    $res['err' ] = 0;
    $res['msg' ] = '查詢成功';
    $res['type'] = $Employee == 'all' ? 'all' : 'single';
    $res['fileName'] = $filename;
    echo json_encode($res);
    exit();
    ?>