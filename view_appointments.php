<?php
// Start session
session_start();

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    header("location: user_login.php");
    exit;
}

// Include config file
require_once 'config.php';

// Initialize variables
$user_id = $_SESSION["user_id"]; // Assuming user is logged in and user_id is stored in session

// Fetch user's appointments
$sql = "SELECT a.appointment_id, a.appointment_date, d.name AS doctor_name, d.specialization
        FROM appointments a
        INNER JOIN doctors d ON a.doctor_id = d.doctor_id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Close statement
$stmt->close();

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f5f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2e6da4;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .button-container {
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
        <h2>My Appointments</h2>
        <table>
            <thead>
                <tr>
                    <th>Appointment Date</th>
                    <th>Doctor Name</th>
                    <th>Specialization</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . date("M d, Y", strtotime($row["appointment_date"])) . "</td>";
                        echo "<td>" . $row["doctor_name"] . "</td>";
                        echo "<td>" . $row["specialization"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>You have no appointments scheduled.</td></tr>";
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
