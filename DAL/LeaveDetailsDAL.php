<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class LeaveDetailsDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`leave_details`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'LeaveDetail_id';
        }


        public function insertLeaveDetails($detail){
            $sql = "INSERT INTO " .$this->getTableName()." (Leave_id, Date, Start_time, End_time, Total_time, Status) 
                    VALUES (:Leave_id, :Date, :Start_time, :End_time, :Total_time, :Status);";
            $params = array(':Leave_id'  => $detail['Leave_id'], 
                            ':Date'   => $detail['Date'], 
                            ':Start_time'     => $detail['Start_time'], 
                            ':End_time'   => $detail['End_time'], 
                            ':Total_time'    => $detail['Total_time'],
                            ':Status'       => $detail['Status']);
            return parent::insert($sql, $params);
        }

        public function getDetailsByID($id){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Leave_id = :Leave_id;";
            $params = array(':Leave_id' => $id);
            return parent::query($sql, $params);
        }

        public function CompleteLeave($leave_id){
            $sql = "UPDATE " .$this->getTableName()." SET Status = 'ON' WHERE Leave_id = :Leave_id;";
            $params = array(':Leave_id' => $leave_id);
            return parent::update($sql, $params);
        }
    }
?>