<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $filename = "subscribers.txt";

        // Check if the file is writable
        if (is_writable($filename) || !file_exists($filename)) {
            if (file_put_contents($filename, $email . PHP_EOL, FILE_APPEND | LOCK_EX)) {
                echo "Thank you for subscribing!";
            } else {
                echo "Failed to save your email. Please try again later.";
            }
        } else {
            echo "The file is not writable. Check permissions.";
        }
    } else {
        echo "Invalid email address. Please enter a valid email.";
    }
} else {
    echo "Invalid request.";
}
?>