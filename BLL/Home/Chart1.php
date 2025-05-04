<?php
    require_once dirname(__FILE__).'/../../DAL/AttendanceDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $userId = MyLIB::GetNumber('userid');
    $attendanceDAL = new AttendanceDAL();
    $result = $attendanceDAL->getValueForChart1($userId);

    foreach ($result as $value) {
       
    }
    $res = array();
    if(!$result){
        $res['err'] = 0;
        $res['msg'] = "查詢個失敗";
        $res['result'] = null;
        echo json_encode($res);
        exit();
    }
    $res['err'] = 0;
    $res['msg'] = "success";
    
    $res['result'] = $result;
    echo json_encode($res);

?>