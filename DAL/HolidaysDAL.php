<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class HolidaysDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`holidays`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Holiday_id';
        }


        public function insertHoliday($data){
            $sql = "INSERT INTO " . $this->getTableName() ." (Date, Year, Name, Is_Holiday, Category, Description, Is_Government)
                        VALUES (:date, :year, :Name, :Is_Holiday, :Category, :Description, :Is_Government);";
            $params = array(':date' => $data['Date'],
                            ':year' => $data['Year'],
                            ':Name' => $data['Name'],
                            ':Is_Holiday' => $data['Is_Holiday'],
                            ':Category' => $data['Category'],
                            ':Description' => $data['Description'],
                            ':Is_Government' => $data['Is_Government']
                            );
            return parent::insert($sql, $params);
        }

        public function deleteHoliday($Holiday_id){
            $sql = "DELETE FROM ".$this->getTableName()." WHERE Holiday_id = :holiday_id;";
            $params = array(':holiday_id' => $Holiday_id);
            return parent::delete($sql, $params);
        }

        public function getHolidaybyMonthYear($month, $year){
            $sql = "SELECT * FROM " . $this->getTableName() . " WHERE MONTH(Date) = :month AND YEAR(Date) = :year 
                    ORDER BY 
                        
                        Date ASC; ;";
            $params = array(':month' => $month, ':year' => $year);
            return parent::query($sql, $params);
        }
        
        public function getAll(){
            $sql = "SELECT * FROM " . $this->getTableName() . " 
                    ORDER BY 
                        CASE WHEN Is_Government = 0 THEN 1 ELSE 2 END ASC, 
                        Date DESC;";
            return parent::query($sql);
        }

        public function getdistinctYear(){
            $sql = "SELECT DISTINCT Year FROM " . $this->getTableName() . " ORDER BY YEAR;";
            return parent::query($sql);
        }   

        public function checkifExist($data){
            $sql = "SELECT * FROM " . $this->getTableName() . " WHERE Date = :date;";
            $params = array(':date' => $data['Date']);
            return parent::query($sql, $params);
        }

        public function getTimeforExcel($StartDate, $EndDate){
            $sql = "SELECT Date, Name FROM " . $this->getTableName() . " WHERE Date BETWEEN :StartDate AND :EndDate;";
            $params = array(':StartDate' => $StartDate, ':EndDate' => $EndDate);
            return parent::query($sql, $params);
        }
    }

    ?>