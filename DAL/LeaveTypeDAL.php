<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class LeaveTypeDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`leave_types`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Leave_type_id';
        }
        //--------------------------------------------------------------------------
        public function getOneById($id){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Leave_type_id = :id;";
            $params = array(':id' => $id);
            $result= parent::getOne($sql, $params);
            return $result;
        }

        public function getLeaveType(){
            $sql = "SELECT Leave_type_id, Leave_type_name FROM " .$this->getTableName(). ";";
            return parent::query($sql);
        }

        public function getLeaveTypeName($id){
            $sql = "SELECT Leave_type_name FROM " .$this->getTableName(). " WHERE Leave_type_id = :id;";
            $params = array(':id' => $id);
            $result= parent::getOne($sql, $params);
            return $result->Leave_type_name;
        }

        public function getPayType($id){
            $sql = "SELECT * FROM " .$this->getTableName(). " WHERE Leave_type_id = :id;";
            $params = array(':id' => $id);
            $result= parent::getOne($sql, $params);
            return $result;
        }
    }
