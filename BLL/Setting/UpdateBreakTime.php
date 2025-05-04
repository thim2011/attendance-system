<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/SettingDAL.php';

    $SettingDAL = new SettingDAL();

    $data = array(
        'Break_start_time' => MyLIB::GetString('BreakStartTime'),
        'Break_end_time' => MyLIB::GetString('BreakEndTime')
    );

    if(!$SettingDAL->updateBreakTime($data)){
        $res=array();
        $res['err'] = 1;
        $res['msg'] = "更新失敗";
        echo json_encode($res);
    }

    $res=array();
    $res['err'] = 0;
    $res['msg'] = "休息時間更新成功";
    echo json_encode($res);
?>