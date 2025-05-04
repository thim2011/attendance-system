<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmWorkHourDAL.php';


    $attendanceDAL = new AttendanceDAL();
    $emWorkHourDAL = new EmWorkHourDAL();

    $attendance_id = MyLIB::GetNumber('Attendance_id');

    $res=array();

    try {
        $attendanceDAL->BeginTransaction();

        $attendance = $attendanceDAL->getOnebyID($attendance_id);
        if(!$attendance){
            $res['err'] = 1;
            $res['msg'] = "無此資料";
            echo json_encode($res);
            exit();
        }

        $year = date('Y', strtotime($attendance->Date));
        $month = date('m', strtotime($attendance->Date));

         $minusTime = $emWorkHourDAL->minusNormalWoking($attendance->Employee_id, $attendance->Working_hours, $year, $month);
        if(!$minusTime){
            $res['err'] = 1;
            $res['msg'] = "更新工時失敗";
            echo json_encode($res);
            exit();
        }


        $result = $attendanceDAL->DeletePunchtime($attendance_id);
        if(!$result){
            $res['err'] = 1;
            $res['msg'] = "刪除失敗";
            echo json_encode($res);
            exit();
        }
        $attendanceDAL->Commit();
    }catch(Exception $e){
        $attendanceDAL->rollBack();
        $res['err'] = 1;
        $res['msg'] = "出了問題，請重新操作";
        echo json_encode($res);
        exit();
    }

    

    $res['err'] = 0;
    $res['msg'] = "刪除成功";
    echo json_encode($res);
    exit();
    ?>