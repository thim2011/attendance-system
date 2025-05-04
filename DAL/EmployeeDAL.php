<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class EmployeeDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`employees`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Employee_id';
        }
        //--------------------------------------------------------------------------

        public function getOneById($id){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Employee_id = :id";
            $params = array(':id' => $id);
            $result= parent::getOne($sql, $params);
            return $result;
        }

        public function getOnebyEmail($email){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Email = :email";
            $params = array(':email' => $email);
            $result= parent::getOne($sql, $params);
            return $result;
        }

        public function getEmployeeName($Employee_id){
            $sql= "SELECT Name FROM " .$this->getTableName(). " WHERE Employee_id = :Employee_id;";
            $params = array(':Employee_id' => $Employee_id);
            $result= parent::getOne($sql, $params);
            return $result->Name;
        }

        public function getAll(){
            $sql = "SELECT * FROM " .$this->getTableName(). " WHERE Status = 'ACTIVE' ORDER BY Employee_id DESC;";
            return parent::query($sql);
        }

        public function getAllforPermission(){
            $sql = "SELECT Employee_id,Name,Email,Status,Role FROM " .$this->getTableName(). " ORDER BY Employee_id DESC;";
            return parent::query($sql);
        }

        public function insertEmployee($employee){
            $sql = "INSERT INTO " .$this->getTableName()." (Email, Name, Account, Password, Position, Department, Join_date, Status, Point) 
                    VALUES (:Email, :Name, :Account, :Password, :Position, :Department, :Join_date, :Status, :Point);";

            $params = array(':Email'      =>   $employee['Email'], 
                            ':Name'       =>   $employee['Name'], 
                            ':Account'    =>   $employee['Account'], 
                            ':Password'   =>   $employee['Password'], 
                            ':Position'   =>   $employee['Position'], 
                            ':Department' =>   $employee['Department'],
                            ':Join_date'  =>   date('Y-m-d H:i:s'),
                            ':Status'     =>   'ACTIVE',
                            ':Point'      =>   0);
            return parent::query($sql, $params);
        }

        public function resetPassword($data){
            $sql = "UPDATE " .$this->getTableName(). " SET Password = :Password WHERE Email = :Email;";
            $params = array(':Password' => $data['Password'], ':Email' => $data['Email']);
            return parent::update($sql, $params);
        }

        public function IsExist($column, $p){
            $sql = "SELECT COUNT(*) FROM " .$this->getTableName(). " WHERE " .$column. " = :p;";
            $params = array(':p' => $p);
            $result= parent::count($sql, $params);
            if($result>0){
                return true;
            }
            return false;
        }

        public function Login($account){
            $sql = "SELECT * FROM " .$this->getTableName(). " WHERE Account = :account;";
            $params = array(':account' => $account);
            return parent::getOne($sql, $params);
        }

        public function countAllEmployee(){
            $sql = "SELECT COUNT(*) FROM " .$this->getTableName(). " WHERE Status = 'ACTIVE';";
            return parent::count($sql);
        }

        

        public function updateScore($employee_id, $point){
            $sql = "UPDATE " .$this->getTableName(). " SET Point = :point WHERE Employee_id = :employee_id;";
            $params = array(':point' => $point, ':employee_id' => $employee_id);
            return parent::update($sql, $params);
        }

        public function updateStatus($employee_id, $toggle){
            $sql = "UPDATE " .$this->getTableName(). " SET Status = :toggle WHERE Employee_id = :employee_id;";
            $params = array(':employee_id' => $employee_id,
                             ':toggle' => $toggle);
            return parent::update($sql, $params);
        }

        public function updateRole($employee_id, $role){
            $sql = "UPDATE " .$this->getTableName(). " SET Role = :role WHERE Employee_id = :employee_id;";
            $params = array(':employee_id' => $employee_id,
                             ':role' => $role);
            return parent::update($sql, $params);
        }

        public function getRankingScore(){
            $sql = "SELECT Name, Point FROM " .$this->getTableName(). " ORDER BY Point DESC;";
            return parent::query($sql);
        }

        public function checkRole($employee_id, $role){
            $sql    = "SELECT Role FROM " .$this->getTableName(). " WHERE Employee_id = :employee_id;";
            $params = array(':employee_id' => $employee_id);
            $result = parent::getOne($sql, $params);
            if($result->Role == $role){
                return true;
            }
            return false;
        }

        public function countUniqueEmployee(){
            $sql = "SELECT DISTINCT Employee_id FROM " .$this->getTableName(). " WHERE Status = 'ACTIVE';";
            return parent::query($sql);
        }

      
    }
?>