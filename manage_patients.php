<?php
// Include config file
require_once 'config.php';

// Check if user is logged in as admin, otherwise redirect to login page
session_start();
if (!isset($_SESSION["admin_id"]) || empty($_SESSION["admin_id"])) {
    header("location: admin_login.php");
    exit;
}

// Fetch list of patients and their appointments
$sql = "SELECT p.patient_id, p.name AS patient_name, a.appointment_id, a.appointment_date, a.doctor_id, d.name AS doctor_name
        FROM patients p
        LEFT JOIN appointments a ON p.patient_id = a.patient_id
        LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
        ORDER BY p.name, a.appointment_date";
$result = $conn->query($sql);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Patients</title>
    <style>
    /* Reset default margin and padding */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

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

.wrapper {
    width: 80%;
    margin: 20px auto;
    background-color: #fff; /* White background */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

h2 {
    color: #007bff; /* Blue heading */
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ccc;
    padding: 12px;
    text-align: left;
}

th {
    background-color: #f2f2f2; /* Light gray header background */
}

tr:nth-child(even) {
    background-color: #f9f9f9; /* Alternate row background */
}  .button-container {
            text-align: center;
            margin-top: 10px;
        }

        .button-container a {
            background-color: #2e6da4;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .button-container a:hover {
            background-color: #1a4b74;
        }


    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Manage Patients and Appointments</h2>
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Doctor Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["patient_name"] . "</td>";
                        echo "<td>" . ($row["appointment_date"] ? date("M d, Y", strtotime($row["appointment_date"])) : "Not scheduled") . "</td>";
                        echo "<td>" . ($row["doctor_name"] ?: "Not assigned") . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No patients found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br>
        <!-- Back to Home Page Button -->
        <div class="button-container">
            <a href="index.php">Back to Home Page</a>
        </div>
    </div>
</body>
</html>
