<?php 
   require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/FixedLeaveDAL.php';
    $FixedLeaveDAL = new FixedLeaveDAL();



    $employee_id = MyLIB::GetNumber('employee_id');
    $day=MyLIB::GetString('day');
    $period=MyLIB::GetString('period');
    $value= MyLIB::GetString('value');

    $col = $day.'_'.$period;
    $update=$FixedLeaveDAL->UpdateFixedLeave($employee_id, $col, $value);
    
    $res = array();
    if(!$update){
        $res['err'] = 1;
        $res['msg'] = "修正失敗";
        echo json_encode($res);
        exit();
    }
    $res['err'] = 0;
    $res['msg'] = "已修正";
    $res['result'] = $update;
    echo json_encode($res);
    exit();
?>