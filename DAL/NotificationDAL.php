<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class NotificationDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`notifications`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Notification_id';
        }

        public function getOneByid($id){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Attendance_id = :id;";
            $params = array(':id' => $id);
            $result= parent::getOne($sql, $params);
            return $result;
        }

        public function insertNotification($notification){
            $sql = "INSERT INTO " .$this->getTableName()." (Employee_id, Message, Noti_type, Mes_type, Leave_id, Status, Created_by, Read_at) 
                    VALUES (:Employee_id, :Message, :Noti_type, :Mes_type, :Leave_id, :Status, :Created_by, :Read_at);";
            $params = array(':Employee_id'  => $notification['Employee_id'], 
                            ':Message'      => $notification['Message'], 
                            ':Noti_type'    => $notification['Noti_type'], 
                            ':Mes_type'     => $notification['Mes_type'],
                            ':Leave_id'     => $notification['Leave_id'],
                            ':Status'       => $notification['Status'],
                            ':Created_by'   => $notification['Created_by'],
                            ':Read_at'      => '');
            return parent::insert($sql, $params);
        }

        public function getNotification($Employee_id, $status = null){

            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    WHERE Employee_id = :Employee_id ";

            $params = array(':Employee_id' => $Employee_id);

            if($status != null){
                $sql .= " AND Status = :Status ORDER BY Created_at DESC";
                $params[':Status'] = $status;
                return parent::query($sql, $params);
            }
            else{
                $sql.="ORDER BY 
                    CASE 
                        WHEN Status = 'unread' THEN 1 
                        ELSE 2 
                    END, 
                    Created_at DESC LIMIT 10;";
            }

            return parent::query($sql, $params);
        }

        public function countUnreadNotification($Employee_id){
            $sql = "SELECT COUNT(*) FROM " .$this->getTableName()." WHERE Employee_id = :Employee_id AND Status = 'unread';";
            $params = array(':Employee_id' => $Employee_id);
            return parent::count($sql, $params);
        }

        public function ReadNotification($Notification_id, $datetime){
            $sql = "UPDATE " .$this->getTableName()." SET Status = 'read', Read_at = :datetime WHERE Notification_id = :Notification_id;";
            $params = array(':datetime' => $datetime, 
                            ':Notification_id' => $Notification_id);
            return parent::update($sql, $params);
        }
    }