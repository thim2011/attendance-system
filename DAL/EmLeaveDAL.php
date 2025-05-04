<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class EmLeaveDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`employee_leaves`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Leave_id';
        }
        //--------------------------------------------------------------------------

        public function getOneByID($id){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Leave_id = :Leave_id;";
            $params = array(':Leave_id' => $id);
            return parent::getOne($sql, $params);
        }

        public function insertLeave($leave){
            $sql = "INSERT INTO " .$this->getTableName()." (Employee_id, Leave_type, Start_date, End_date, Total_day, Reason, Status) 
                    VALUES (:Employee_id, :Leave_type, :Start_date, :End_date, :Total_day, :Reason, :Status);";
            $params = array(':Employee_id'  => $leave['Employee_id'], 
                            ':Start_date'   => $leave['Start_date'], 
                            ':End_date'     => $leave['End_date'], 
                            ':Leave_type'   => $leave['Leave_type'],
                            ':Total_day'    => $leave['Total_day'], 
                            ':Reason'       => $leave['Reason'],
                            ':Status'       => 'Pending');

            $result= parent::insert($sql, $params);
            if($result){
                return parent::getLastInsertId();
            }
            return false;
        }

        public function CancelLeave($id){
            $sql = "UPDATE " . $this->getTableName() . " SET Status = 'Cancelled' WHERE Leave_id = :Leave_id;";
            $params = array(':Leave_id' => $id);
            return parent::update($sql, $params);
        }

        public function getLeaveByEmployee($id){
            $sql = "SELECT * FROM " .$this->getTableName()." 
                    WHERE Employee_id = :Employee_id 
                    ORDER BY 
                    CASE 
                        WHEN status = 'Accepted' THEN 1
                        WHEN status = 'Pending' THEN 2
                        WHEN status = 'Rejected' THEN 3
                        WHEN status = 'Completed' THEN 4
                        ELSE 5
                    END, Leave_id DESC;";
            $params = array(':Employee_id' => $id);
            return parent::query($sql, $params);
        }

        public function checkifDuplicate($employee_id, $date, $start_time, $end_time){

            $sql="SELECT emLeave.*, detail.Date, detail.Start_time, detail.End_time
                    FROM employee_leaves emLeave
                    LEFT JOIN leave_details detail 
                        ON emLeave.Leave_id = detail.Leave_id
                    WHERE emLeave.Employee_id = :Employee_id
                    AND  :Leavedate BETWEEN emLeave.Start_date AND emLeave.End_date
                    AND (
                        (:start_time < detail.End_time AND :end_time > detail.Start_time)
                    );
                    ";                                                                   

                $params = array(
                    ':Employee_id' => $employee_id,
                    ':Leavedate' => $date,
                    ':start_time' => $start_time,  
                    ':end_time' => $end_time      
                );
            return parent::query($sql, $params);
        }
        
        public function getLeave($employee_id=null, $time_start=null, $time_end=null, $status=null) {
            $sql = "SELECT * FROM " . $this->getTableName();
            $conditions = [];
            $params = [];
        
            if (!empty($status)) {
                $conditions[] = "Status = :status";
                $params[':status'] = $status;
            }
        
            if (!empty($time_start)) {
                $conditions[] = "Start_date >= :time_start";
                $params[':time_start'] = $time_start;
            }
        
            if (!empty($time_end)) {
                $conditions[] = "Start_date <= :time_end";
                $params[':time_end'] = $time_end;
            }

            if ($employee_id!=0) {
                $conditions[] = "Employee_id = :employee_id";
                $params[':employee_id'] = $employee_id;
            }
        
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
        
            $sql .= " ORDER BY Leave_id DESC";
        
            return parent::query($sql, $params);
        }

        public function VerifyLeave($data){
            $sql = "UPDATE " . $this->getTableName() . " SET Status = :Status, VerifyBy = :VerifyBy, VerifyTime= :VerifyTime, VerifyReason = :VerifyReason  
                    WHERE Leave_id = :Leave_id;";
            $params = array(':Leave_id' => $data['Leave_id'],
                            ':VerifyBy' => $data['VerifyBy'],
                            ':VerifyTime' => $data['VerifyTime'],
                            ':VerifyReason' => $data['VerifyReason'],
                             ':Status' => $data['Status']
                            );
            return parent::update($sql, $params);
        }

        public function getLeaveCountByMonth() {
            $sql = "
                SELECT 
                    DATE_FORMAT(Start_date, '%m') AS Month, 
                    COUNT(*) AS LeaveCount 
                FROM 
                    " . $this->getTableName() . " 
                GROUP BY 
                    Month
            ";
            return parent::query($sql);
        }
        
        public function getLeaveCountToday(){
            $sql = "SELECT COUNT(*) 
                    FROM " . $this->getTableName() . "
                    WHERE CURDATE() BETWEEN Start_date AND End_date";
            return parent::count($sql);
        }

        public function getLeaveToday(){
            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    WHERE CURDATE() BETWEEN Start_date AND End_date";
            return parent::query($sql);
        }

        public function getLeaveCountToday2($date){
            $sql = "SELECT COUNT(*) 
                    FROM " . $this->getTableName() . "
                    WHERE :date BETWEEN Start_date AND End_date";
            $params = array(':date' => $date);
            return parent::count($sql, $params);
        }

        public function getLeaveToday2($date){
            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    WHERE :date BETWEEN Start_date AND End_date";
            $params = array(':date' => $date);
            return parent::query($sql, $params);
        }

        public function CompleteLeave($leaveid){
            $sql = "UPDATE " . $this->getTableName() . " SET Status = 'Completed' WHERE Leave_id = :Leave_id;";
            $params = array(':Leave_id' => $leaveid);
            return parent::update($sql, $params);
        }

        public function countPending(){
            $sql = "SELECT COUNT(*) FROM " . $this->getTableName() . " WHERE Status = 'Pending'";
            return parent::count($sql);
        }

        public function getDataforExcel($Employee_id, $start, $end){
            $sql = "SELECT leaves.Leave_id, leaves.Employee_id, leaves.Reason,
            types.Leave_type_id, types.Leave_type_name,
            detail.Date, detail.Total_time, detail.Start_time, detail.End_time
            FROM employee_leaves leaves
            LEFT JOIN leave_types types ON leaves.Leave_type = types.Leave_type_id
            LEFT JOIN leave_details detail ON leaves.Leave_id = detail.Leave_id
            WHERE (leaves.Start_date BETWEEN :Start_date AND :End_date
                OR leaves.End_date BETWEEN :Start_date AND :End_date)";  // Nhóm điều kiện OR trong dấu ngoặc

            $params = array(':Start_date' => $start, ':End_date' => $end);
            if($Employee_id !== 'all' && $Employee_id !== null){
            $sql .= " AND leaves.Employee_id = :Employee_id";
            $params[':Employee_id'] = $Employee_id;
            }

            $sql .= " ORDER BY leaves.Employee_id, leaves.Start_date ASC";

            return parent::query($sql, $params);
        }

        public function BeginTransaction(){
            return parent::BeginTransaction();
        }

        public function Commit(){
            return parent::Commit();
        }

        public function Rollback(){
            return parent::RollBack();
        }
    }