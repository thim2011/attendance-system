<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/SettingDAL.php';

    $SettingDAL = new SettingDAL();

    $data = array(
        'Work_start_time' => MyLIB::GetString('workstart'),
        'Work_end_time' => MyLIB::GetString('workend')
    );

    if(!$SettingDAL->updateWorkTime($data)){
        $res=array();
        $res['err'] = 1;
        $res['msg'] = "更新失敗";
        echo json_encode($res);
    }

    $res=array();
    $res['err'] = 0;
    $res['msg'] = "上下班時間更新成功";
    echo json_encode($res);
?>