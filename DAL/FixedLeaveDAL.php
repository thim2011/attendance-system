<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class FixedLeaveDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`fixed_leave`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'id';
        }

        public function getOneByid($Employee_id){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Employee_id = :Employee_id;";
            $params = array(':Employee_id' => $Employee_id);
            $result= parent::getOne($sql, $params);
            return $result;
        }

        public function getAll(){
            $sql = "SELECT * FROM " .$this->getTableName().";";
            return parent::query($sql);
        }

        public function UpdateFixedLeave($Employee_id, $col, $value){
            $sql = "UPDATE " .$this->getTableName()." SET ".$col." = :value, last_update = NOW() WHERE Employee_id = :Employee_id;";
            $params = array(':Employee_id' => $Employee_id, ':value' => $value);
            return parent::update($sql, $params);
        }
    }
?>