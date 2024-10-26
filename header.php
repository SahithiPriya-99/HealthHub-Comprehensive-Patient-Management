<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Healthcare Management System</title>
</head>
<body>
    <header>
        <h1>Healthcare Management System</h1>
        <?php if (isset($_SESSION['admin'])): ?>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="add_doctor.php">Add Doctor</a></li>
                    <li><a href="manage_appointments.php">Manage Appointments</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        <?php elseif (isset($_SESSION['user'])): ?>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="search_doctors.php">Search Doctors</a></li>
                    <li><a href="book_appointment.php">Book Appointment</a></li>
                    <li><a href="view_appointments.php">View Appointments</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        <?php endif; ?>
    </header>
</body>
</html>
