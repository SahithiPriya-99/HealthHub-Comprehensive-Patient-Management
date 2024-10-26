<?php
session_start();
require_once 'config.php';

// Initialize variables
$name = $email = $phone = $doctor_id = $appointment_date = "";
$name_err = $email_err = $phone_err = $doctor_id_err = $date_err = "";
$error_msg = "";

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    header("location: user_login.php");
    exit;
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate selected doctor
    if (empty(trim($_POST["doctor_id"]))) {
        $doctor_id_err = "Please select a doctor.";
    } else {
        $doctor_id = trim($_POST["doctor_id"]);
    }

    // Validate appointment date
    if (empty(trim($_POST["appointment_date"]))) {
        $date_err = "Please select an appointment date.";
    } else {
        $appointment_date = trim($_POST["appointment_date"]);
    }

    // Check input errors before inserting into database
    if (empty($name_err) && empty($email_err) && empty($phone_err) && empty($doctor_id_err) && empty($date_err)) {

        // Get user_id from session
        $user_id = $_SESSION["user_id"];

        // Insert into patients table if not already exists
        $sql_patient = "INSERT INTO patients (user_id, name, phone) VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE user_id = LAST_INSERT_ID(user_id)";
        if ($stmt_patient = $conn->prepare($sql_patient)) {
            $stmt_patient->bind_param("iss", $user_id, $name, $phone);
            $stmt_patient->execute();
            $patient_id = $stmt_patient->insert_id;
            $stmt_patient->close();
        } else {
            $error_msg = "Oops! Something went wrong with patient insertion.";
        }

        // Prepare an insert statement for appointments
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) 
                VALUES (?, ?, ?, ?, 'Pending')";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("iiss", $patient_id, $doctor_id, $appointment_date, $appointment_time);

            // Set parameters
            $appointment_time = date("H:i:s"); // Assuming appointment_time is TIME type in database

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to view_appointments.php or another page after successful insertion
                header("location: view_appointments.php");
                exit();
            } else {
                $error_msg = "Oops! Something went wrong. Please try again later. Error: " . $conn->error;
            }

            // Close statement
            $stmt->close();
        } else {
            $error_msg = "Oops! Something went wrong with the database preparation.";
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f5f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 360px;
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

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        select,
        input[type="date"],
        input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .error {
            color: red;
            margin-bottom: 10px;
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
        <h2>Appointment Request Form</h2>
        <span class="error"><?php echo $error_msg; ?></span>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Your Name</label>
                <input type="text" name="name" value="<?php echo $name; ?>">
                <span class="error"><?php echo $name_err; ?></span>
            </div>
            <div>
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div>
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo $phone; ?>">
                <span class="error"><?php echo $phone_err; ?></span>
            </div>
            <div>
                <label>Select Doctor</label>
                <select name="doctor_id">
                    <option value="">Select Doctor</option>
                    <?php
                    // Fetch list of doctors
                    $sql_doctors = "SELECT doctor_id, name FROM doctors";
                    $result_doctors = $conn->query($sql_doctors);

                    if ($result_doctors->num_rows > 0) {
                        while ($row_doctor = $result_doctors->fetch_assoc()) {
                            $selected = ($doctor_id == $row_doctor["doctor_id"]) ? "selected" : "";
                            echo "<option value='" . $row_doctor["doctor_id"] . "' " . $selected . ">" . $row_doctor["name"] . "</option>";
                        }
                    }
                    ?>
                </select>
                <span class="error"><?php echo $doctor_id_err; ?></span>
            </div>
            <div>
                <label>Appointment Date</label>
                <input type="date" name="appointment_date" value="<?php echo $appointment_date; ?>">
                <span class="error"><?php echo $date_err; ?></span>
            </div>
            <div class="button-container">
                <input type="submit" value="Submit">
            </div>
        </form>
        <!-- Back to Home Page Button -->
        <div class="button-container">
            <a href="index.php">Back to Home Page</a>
        </div>
    </div>
</body>
</html>
