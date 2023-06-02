<?php
/**
 * --------Usage---------
 * $dater = new Dater();
 * $dater->setDatabase($db); !!! use your own database variable.
 * $dater->addUser($user_id);
 * $dater->run();
 *------------------------
 * $dater->book($customer_id,$user_id,$date)
 *------------------------
 * Class Dater
 * This class provides functionality for a booking system.
 */
class Dater
{
    private $db;
    private $users = [];

    /**
     * Sets the database connection for the class.
     * @param $dbConnection The database connection object.
     */
    public function setDatabase($dbConnection)
    {
        $this->db = $dbConnection;
    }
    
    /**
     * Adds a user to the system.
     * @param $userId The ID of the user to add.
     */
    public function addUser($userId)
    {
        $this->users[] = $userId;
    }
    
  
    /**
     * Runs the necessary setup for the booking system.
     * Creates required database tables.
     */
    public function run()
    {
        $this->createAppointmentsTable();
        $this->createAvailableDatesTable();
    }
    
  
    /**
     * Books an appointment for a customer.
     * @param $customerId The ID of the customer.
     * @param $userId The ID of the user providing the service.
     * @param $date The date and time of the appointment.
     */
    public function book($customerId, $userId, $date)
    {
        if (!$this->isUserValid($userId)) {
            echo "Geçersiz kullanıcı.";
            return;
        }
        
        $formattedDate = date('Y-m-d H:i', strtotime($date));
        
        if ($this->isAppointmentAvailable($userId, $formattedDate)) {
            $sql = "INSERT INTO appointments (user_id, customer_id, date) VALUES ($userId, $customerId, '$formattedDate')";
            $this->db->exec($sql);
            echo "Randevu başarıyla eklendi.";
        } else {
            echo "Seçilen tarih ve saat için randevu müsait değil.";
        }
    }
    
    /**
     * Checks if a user is valid.
     * @param $userId The ID of the user to check.
     * @return bool Returns true if the user is valid, false otherwise.
     */
    private function isUserValid($userId)
    {
        return in_array($userId, $this->users);
    }
    
    /**
     * Checks if an appointment is available for the selected date and time.
     * @param $userId The ID of the user providing the service.
     * @param $selectedTime The selected date and time.
     * @return bool Returns true if the appointment is available, false otherwise.
     */
    private function isAppointmentAvailable($userId, $selectedTime)
    {
        $sql = "SELECT * FROM appointments WHERE user_id = $userId AND TIME(date) = '$selectedTime'";
        $result = $this->db->query($sql);

        if ($result->rowCount() > 0) {
            return false;
        }

        $sql = "SELECT * FROM appointments_available_times WHERE user_id = $userId AND time = '$selectedTime'";
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
    
        foreach ($this->users as $user) {
            for ($i = 1; $i <= 5; $i++) {
                $dayName = $this->getDayName($i);
                $time = $this->generateTime();
    
                $sql = "INSERT INTO appointments_available_times (user_id, day_name, time) VALUES ($user, '$dayName', '$time')";
                $this->db->exec($sql);
            }
        }
    }
    

    
    private function getDayName($dayNumber)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        return $days[$dayNumber - 1];
    }
    
    private function generateTime()
    {
        $hours = mt_rand(9, 17);
        $minutes = sprintf('%02d', mt_rand(0, 59));
        
        return $hours . ':' . $minutes;
    }
}
