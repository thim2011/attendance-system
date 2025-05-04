<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class AttendanceDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`attendance`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Attendance_id';
        }

        public function getOnebyID($Attendance_id){
            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    WHERE Attendance_id = :Attendance_id";
            $params = array(':Attendance_id' => $Attendance_id);
            return parent::getOne($sql, $params);
        }

        public function getTodayRecord($Date){
            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    WHERE Date = :Date
                    ORDER BY Attendance_id DESC";
            $params = array( ':Date' => $Date);
            return parent::query($sql, $params);
        }
        
        public function Punchin($punchin){
            $sql = "INSERT INTO " .$this->getTableName()." (Employee_id, Date, Punch_in, Punch_out, Status, Working_hours, Is_Attend) 
                    VALUES (:Employee_id, 
                            :Date, 
                            :Punch_in, 
                            :Punch_out, 
                            :Status,
                            :Working_hours,
                            :Is_Attend
                            );";
                                       
            $params = array(':Employee_id' => $punchin['Employee_id'], 
                            ':Date' => $punchin['Date'],
                            ':Punch_in' =>  $punchin['Punch_in'],
                            ':Punch_out' =>  $punchin['Punch_out'],
                            ':Status' =>  $punchin['Status'],
                            ':Working_hours' =>  $punchin['Working_hours'],
                            ':Is_Attend' =>  $punchin['Is_Attend'],
                            );
            return parent::insert($sql, $params);
        }

        public function getPunchinByEmployID($Employee_id, $Date){
            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    WHERE Employee_id = :Employee_id 
                    AND Date = :Date 
                    ORDER BY Attendance_id DESC 
                    LIMIT 1;";  
            $params = array(':Employee_id' => $Employee_id, ':Date' => $Date);
            return parent::getOne($sql, $params);
        }

        public function PunchOut($punchout){
            $sql = "UPDATE " .$this->getTableName()." 
                    SET Punch_out = :Punch_out, 
                        Working_hours = :Working_hours,
                        Is_Attend = :Is_Attend
                    WHERE Employee_id = :Employee_id 
                    AND Date = :Date 
                    AND Attendance_id = :Attendance_id
                    ORDER BY Attendance_id DESC 
                    LIMIT 1;";
            $params = array(':Punch_out' => $punchout['Punch_out'], 
                            ':Working_hours' => $punchout['Working_hours'],
                            ':Is_Attend' => $punchout['Is_Attend'],
                            ':Employee_id' => $punchout['Employee_id'],
                            ':Date' => $punchout['Date'],
                            ':Attendance_id' => $punchout['Attendance_id']);
            return parent::update($sql, $params);
        }

        public function ForgotPunch($Employee_id, $type, $date, $time){
            $sql = "UPDATE " . $this->getTableName() ;
            if($type == 'punchin'){
                $sql .= " SET Punch_in = :time, Is_Attend = 'Checked-in'";
            } else {
                $sql .= " SET Punch_out = :time, Is_Attend = 'Checked-out'";
            } 

            $sql .= " WHERE Employee_id = :Employee_id 
                    AND Date = :Date";
            $params = array(':Employee_id' => $Employee_id, ':Date' => $date, ':time' => $time);
            return parent::update($sql, $params);
        }

        public function EditPunchtime($Attendance_id, $punchin, $punchout){
            $sql = "UPDATE " . $this->getTableName() . " 
                    SET Punch_in = :punchin, 
                        Punch_out = :punchout
                    WHERE Attendance_id = :Attendance_id";
            $params = array(':Attendance_id' => $Attendance_id, ':punchin' => $punchin, ':punchout' => $punchout);
            return parent::update($sql, $params);
        }

        public function DeletePunchtime($Attendance_id){
            $sql = "DELETE FROM " . $this->getTableName() . " 
                    WHERE Attendance_id = :Attendance_id";
            $params = array(':Attendance_id' => $Attendance_id);
            return parent::delete($sql, $params);
        }

        public function UpdateWorkingHours($Attendance_id, $time){
            $sql = "UPDATE " . $this->getTableName() . " 
                    SET Working_hours = :time
                    WHERE Attendance_id = :Attendance_id ";
                  
            $params = array(':Attendance_id' => $Attendance_id, ':time' => $time);
            return parent::update($sql, $params);
        }

        public function getPersonalRecordbyYearMonth($userId, $yearmonth){
            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    WHERE Employee_id = :Employee_id 
                    AND Date LIKE :Date 
                    ORDER BY Date DESC";
            $params = array(':Employee_id' => $userId, ':Date' => $yearmonth . '%');
            return parent::query($sql, $params);
        }

        public function getPersonalPunch($userid, $date){
            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    WHERE Employee_id = :Employee_id 
                    AND Date = :Date";
            $params = array(':Employee_id' => $userid, ':Date' => $date);
            return parent::getOne($sql, $params);
        }

        public function getValueForChart1($Employee_id){
            $sql="SELECT SUM(Working_hours) AS TotalWorkHours,
                    DATE_FORMAT(Date, '%m') AS Month
                    FROM Attendance
                    WHERE Employee_id = :Employee_id
                    GROUP BY Employee_id, Month";
            $params = array(':Employee_id' => $Employee_id);
            return parent::query($sql, $params);
        }

        public function getForgotPunch($count = false){
            if($count){
                $sql = "SELECT COUNT(*) FROM " . $this->getTableName();
            } else {
                $sql = "SELECT Employee_id, Date, Punch_in, Punch_out FROM " . $this->getTableName();
            }
        
            $sql .= " WHERE 
                      Is_leave != 1 
                      AND (Punch_in = '00:00:00' OR Punch_out = '00:00:00')";
            if($count){
                return parent::count($sql);
            } else {
                return parent::query($sql);
            }
        }

        public function getAttendance($Employee_id, $start, $end){
            $sql= "SELECT attend.Date, attend.Employee_id, emp.Name, attend.Punch_in, attend.Punch_out
                    FROM attendance attend
                    LEFT JOIN employees emp ON attend.Employee_id = emp.Employee_id
                    WHERE attend.Date BETWEEN :start AND :end";

            $params = array(':start' => $start, ':end' => $end);

            if ($Employee_id !== 'all' && $Employee_id !== null) {
                $sql .= " AND attend.Employee_id = :Employee_id";
                $params[':Employee_id'] = $Employee_id;
            }

            $sql .= " ORDER BY emp.Employee_id, attend.Date ASC";

            return parent::query($sql, $params);
        }

        public function BeginTransaction(){
            return parent::beginTransaction();
        }

        public function Commit(){
            return parent::commit();
        }

        public function Rollback(){
            return parent::rollback();
        }
        
    }