<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class AnnualLeaveDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`annual_leave`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Id';
        }

        public function getAnnualLeaveByEmployee($employee, $year, $Status){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Employee_id = :Employee_id AND Year = :Year ANd Status = :Status ";
            if($Status == 'normal') $sql .= "ORDER BY Id DESC LIMIT 1;";
            else $sql .= ";";
            $params = array(':Employee_id' => $employee, 
                            ':Year' => $year,
                            ':Status' => $Status);
            return parent::getOne($sql, $params);
        }

        public function InsertAnnualLeave($data){
            $sql = "INSERT INTO " .$this->getTableName()." (Employee_id, Year, Month, TotalHours, UseHours, Leave_id, Status) 
                    VALUES (:Employee_id, :Year, :Month, :TotalHours, :UseHours, :Leave_id, :Status);";
            $params = array(':Employee_id' => $data['Employee_id'], 
                            ':Year' => $data['Year'], 
                            ':Month' => $data['Month'], 
                            ':TotalHours' => $data['TotalHours'], 
                            ':UseHours' => $data['UseHours'], 
                            ':Leave_id' => $data['Leave_id'], 
                            ':Status' => $data['Status']);
            return parent::insert($sql, $params);
        }

        public function getUsedHours($employee, $year){
            $sql = "SELECT SUM(UseHours) as total FROM " .$this->getTableName()." WHERE Employee_id = :Employee_id AND Year = :Year ;";
            $params = array(':Employee_id' => $employee, 
                            ':Year' => $year);
            return parent::count($sql, $params);
        }
    }   