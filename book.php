<?php
// Database configuration
$host = 'localhost';
$db = 'bookings'; // Replace with your database name
$user = 'root'; // Replace with your database username
$pass = ''; // Replace with your database password

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form data and sanitize inputs
        $name = htmlspecialchars($_POST['name']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $date = $_POST['date'];
        $time = $_POST['time'];

        // Validate inputs
        if (empty($name) || empty($email) || empty($date) || empty($time)) {
            echo "All fields are required.";
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address.";
            exit;
        }

        // Check for existing appointments at the same time
        $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = :date AND appointment_time = :time");
        $stmt->execute(['date' => $date, 'time' => $time]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "The selected time slot is already booked. Please choose another time.";
            exit;
        }

        // Insert booking into the database
        $stmt = $conn->prepare("INSERT INTO appointments (name, email, appointment_date, appointment_time) VALUES (:name, :email, :date, :time)");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'date' => $date,
            'time' => $time
        ]);

        echo "Appointment booked successfully! You will recieve a Zoom invitation shortly.";
    }
} catch (PDOException $e) {
    // Handle database connection errors
    echo "Error: " . $e->getMessage();
    exit;
}
?>