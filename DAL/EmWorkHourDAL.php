<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class EmWorkHourDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`employee_workhour`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'WorkTime_id';
        }

        public function updateWorktime($WorkTime_id, $paytype, $worktime){
            $sql = "UPDATE " .$this->getTableName();
            if($paytype == "full"){
                $sql .= " SET Full_leave_hours = Full_leave_hours + :WorkTime";
            }
            else if($paytype == "half"){
                $sql .= " SET Half_leave_hours = Half_leave_hours + :WorkTime";
            }
            else if($paytype == "none"){
                $sql .= " SET Unpaid_leave_hours = Unpaid_leave_hours + :WorkTime";
            }else{
                return false;
            }
            $sql .= " WHERE WorkTime_id = :WorkTime_id;";   
            $params = array(':WorkTime_id' => $WorkTime_id, 
                            ':WorkTime' => $worktime);
            return parent::update($sql, $params);
        }

        public function minusNormalWoking($employee_id, $data, $year, $month){
            $sql = "UPDATE " .$this->getTableName(). " SET NormalWorkingHours = NormalWorkingHours - :data WHERE Employee_id = :Employee_id AND Year = :Year AND Month = :Month";
            $params = array(
            ':data' => $data,
            ':Employee_id' => $employee_id,
            ':Year' => $year,
            ':Month' => $month
            );
            return parent::update($sql, $params);
        }

        public function getByEmployeeYM($employee, $year, $month){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Employee_id = :Employee_id AND Year = :Year AND Month = :Month;";
            $params = array(':Employee_id' => $employee, 
                            ':Year' => $year, 
                            ':Month' => $month);
            return parent::getOne($sql, $params);
        }

        public function insertNew($employee, $year, $month){
            $sql = "INSERT INTO " .$this->getTableName()." (Employee_id, Year, Month) 
                    VALUES (:Employee_id, :Year, :Month);";
            $params = array(':Employee_id' => $employee, 
                            ':Year' => $year, 
                            ':Month' => $month);
            return parent::insert($sql, $params);
        }

        public function updateSomeColumn($data){
            $sql = "UPDATE " .$this->getTableName(). " SET AnnualLeave = :AnnualLeave";
            $params = array(':AnnualLeave' => $data);
            return parent::update($sql, $params);
        }
    }