<?php 
    require_once dirname(__FILE__).'/../../DAL/HolidaysDAL.php';
    $holidaysDAL = new HolidaysDAL();

$api_url = "https://data.ntpc.gov.tw/api/datasets/308dcd75-6434-45bc-a95f-584da4fed251/json?size=1192";
$json_data = file_get_contents($api_url);
$holidays = json_decode($json_data, true);

if ($holidays) {
    foreach ($holidays as $holiday) {

        // Chỉ xử lý dữ liệu từ năm 2022 trở đi
        if ($holiday['year'] >= 2022) {
            if($holiday['isholiday'] == '是'){
                $holiday['isholiday'] =true;
            }else{
                $holiday['isholiday'] =false;
            }
            $data = array(
                'Date' => $holiday['date'],
                'Year' => $holiday['year'],
                'Name' => $holiday['name'],
               
                'Is_Holiday' => $holiday['isholiday'],
                'Category' => $holiday['holidaycategory'],
                'Description' => $holiday['description']
            );


            $result = $holidaysDAL->insertHoliday($data);
            if (!$result) {
                echo "失敗";
                exit();
            }
        }
    }
    echo "成功";
    exit();
}
?>