<?php 
    require_once dirname(__FILE__).'/../../DAL/HolidaysDAL.php';
    require_once dirname(__FILE__).'/../../Utils/Main.php';

    $month = MyLIB::getNumber('month');
    $year = MyLIB::getNumber('year');

    $HolidaysDAL = new HolidaysDAL();

    $result = $HolidaysDAL->getHolidaybyMonthYear($month, $year);
    if($result==null){
        $res = array();
        $res['err'] = 1;
        $res['msg'] = '行事曆查詢失敗';
        echo json_encode($res);
        exit();
    }

    $res = array();
    $res['err'] = 0;
    $res['msg'] = '行事曆查詢成功';
    $res['result'] = $result;
    echo json_encode($res);
    exit();

?>