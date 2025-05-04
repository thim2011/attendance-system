<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/HolidaysDAL.php';

    $HolidaysDAL = new HolidaysDAL();

    $holiday_id = MyLIB::GetNumber('Holiday_id');

    $DELETE = $HolidaysDAL->deleteHoliday($holiday_id);

    $res=array();
    if(!$DELETE){
        $res['err']= 1;
        $res['msg']= "刪除動作失敗";
        echo json_encode($res);
        exit();
    }
    $res['err']=0;
    $res['msg']="刪除成功";
    echo json_encode($res);
    exit();
    ?>