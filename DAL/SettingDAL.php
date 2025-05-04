<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class SettingDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`setting`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return '';
        }

        public function updateBreakTime($breakTime){
            $sql = "UPDATE " . $this->getTableName() . " SET Break_start_time = :Break_start_time,  Break_end_time = :Break_end_time;";
            $params = array(':Break_start_time' => $breakTime['Break_start_time'],
                            ':Break_end_time' => $breakTime['Break_end_time']);
            return parent::update($sql, $params);
        }

        public function updateWorkTime($workTime){
            $sql = "UPDATE " . $this->getTableName() . " SET Work_start_time = :Work_start_time,  Work_end_time = :Work_end_time;";
            $params = array(':Work_start_time' => $workTime['Work_start_time'],
                            ':Work_end_time' => $workTime['Work_end_time']);
            return parent::update($sql, $params);
        }

        public function getWorkTime(){
            $sql = "SELECT Work_start_time, Work_end_time FROM " . $this->getTableName() . ";";
            return parent::getOne($sql);
        }

        public function getBreakTime(){
            $sql = "SELECT Break_start_time, Break_end_time FROM " . $this->getTableName() . ";";
            return parent::getOne($sql);
        }
    }