<?php

class Dater
{
    private $db;

    public function setDatabase($dbConnection)
    {
        $this->db = $dbConnection;
        $this->createAppointmentsTable();
        $this->createAvailableDatesTable();
    }
    
    public function book($customerId, $userId, $date)
    {       
        $formattedDate = date('Y-m-d H:i', strtotime($date));
        
        if ($this->isAppointmentAvailable($userId, $formattedDate)) {
            $sql = "INSERT INTO appointments (user_id, customer_id, date) VALUES ($userId, $customerId, '$formattedDate')";
            $this->db->exec($sql);
            echo "Randevu başarıyla eklendi.";
        } else {
            echo "Seçilen tarih ve saat için randevu müsait değil.";
        }
    }

    public function defineTime($userId, $dayName, $time)
    {
        $existingTimeQuery = "SELECT * FROM appointments_available_times WHERE user_id = $userId AND day_name = '$dayName' AND time = '$time'";
        $existingTimeResult = $this->db->query($existingTimeQuery);
        
        if ($existingTimeResult->rowCount() > 0) {
            echo "Bu gün ve saat zaten tanımlı.";
            return;
        }
        
        $insertTimeQuery = "INSERT INTO appointments_available_times (user_id, day_name, time) VALUES ($userId, '$dayName', '$time')";
        $this->db->exec($insertTimeQuery);
        echo "Zaman başarıyla tanımlandı.";
    }

    
    private function isAppointmentAvailable($userId, $selectedTime)
    {
        $formattedTime = date('H:i', strtotime($selectedTime));
        $sql = "SELECT * FROM appointments WHERE user_id = $userId AND TIME(date) = '$selectedTime'";
        $result = $this->db->query($sql);

        if ($result->rowCount() > 0) {
            return false;
        }

        $sql = "SELECT * FROM appointments_available_times WHERE user_id = $userId AND time = '$formattedTime'";
        $result = $this->db->query($sql);
        
        return $result->rowCount() > 0;
    }
    
    private function createAppointmentsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS appointments (
            appointment_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            customer_id INT,
            date DATETIME
        )";
        
        $this->db->exec($sql);
    }
    
    private function createAvailableDatesTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS appointments_available_times (
            date_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            day_name VARCHAR(20),
            time VARCHAR(10)
        )";
    
        $this->db->exec($sql);
    }
}
