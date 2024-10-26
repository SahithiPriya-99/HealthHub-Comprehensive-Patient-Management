<?php
session_start();

// Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    // Admin is logged in
    $user_type = 'admin';
    $welcome_message = "Hello, Admin " . $_SESSION['username'] . "!";
    $register_link = '<p><a href="admin_register.php">Register New Admin</a></p>';
    $manage_patients_link = '<p><a href="manage_patients.php">Manage Patients</a></p>';
    $add_doctor_link = '<p><a href="add_doctor.php">Add Doctor</a></p>';
} elseif (isset($_SESSION['user_id'])) {
    // User is logged in (assuming you have a users table and user_id in session)
    $user_type = 'user';
    $welcome_message = "Hello, User " . $_SESSION['username'] . "!";
    $search_doctors_link = '<p><a href="search_doctors.php">Search Doctors</a></p>';
    $appointment_link = '<p><a href="appointment_form.php">Fill Appointment Form</a></p>';
    $view_appointments_link = '<p><a href="view_appointments.php">View Appointments</a></p>';
} else {
    // No user logged in, redirect to login page
    header("Location: user_login.php");
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: user_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Healthcare Management System</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f0f7fa; /* Light blue background */
    color: #333; /* Dark text color */
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full viewport height */
}

.container {
    width: 80%;
    max-width: 600px;
    background-color: #fff; /* White background */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

h1 {
    color: #007bff; /* Blue heading */
    text-align: center;
}

p {
    margin-bottom: 20px;
    line-height: 1.6;
}

.links {
    margin-top: 20px;
}

.links a {
    display: block;
    margin-bottom: 10px;
    padding: 12px 20px;
    background-color: #007bff; /* Blue button */
    color: #fff;
    text-decoration: none;
    text-align: center;
    border-radius: 4px;
    transition: background-color 0.3s ease;
    font-size: 16px;
}

.links a:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.logout {
    text-align: center;
    margin-top: 20px;
}

.logout a {
    color: #007bff; /* Blue logout link */
    text-decoration: none;
    font-size: 14px;
}

.logout a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div>
        <h1>Welcome to Healthcare Management System</h1>
        <p><?php echo $welcome_message; ?></p>
        
        <?php
        // Display links based on user type
        if ($user_type === 'admin') {
             echo $manage_patients_link;
            echo $add_doctor_link;
        } elseif ($user_type === 'user') {
            echo $search_doctors_link;
            echo $appointment_link;
            echo $view_appointments_link;
        }
        ?>
        
        <p><a href="index.php?logout=true">Logout</a></p>
    </div>
</body>
</html>
