<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/HolidaysDAL.php';

    $HolidaysDAL = new HolidaysDAL();

    $data = array(
        'Date' => MyLIB::GetString('holiday-date'),
        'Year' => date("Y", strtotime(MyLIB::GetString('holiday-date'))),
        'Name' => MyLIB::GetString('holiday-name'),
        'Is_Holiday' => MyLIB::GetString('is_holiday') =='true'? 1 : 0,
        'Category' => MyLIB::GetString('category'),
        'Description' => MyLIB::GetString('description'),
        'Is_Government' => 0
    );

    if($HolidaysDAL->checkifExist($data) != null){
        $res=array();
        $res['err'] = 1;
        $res['msg'] = "日期已存在";
        echo json_encode($res);
        exit();
    }

    if(!$HolidaysDAL->insertHoliday($data)){
        $res=array();
        $res['err'] = 1;
        $res['msg'] = "新增失敗";
        echo json_encode($res);
        exit();
    }


    $res=array();
    $res['err'] = 0;
    $res['msg'] = "新增成功";
    echo json_encode($res);
    exit();
?>