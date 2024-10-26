<?php
// Include config file
require_once 'config.php';

session_start();
if (!isset($_SESSION["admin_id"]) || empty($_SESSION["admin_id"])) {
    header("location: admin_login.php");
    exit;
}

// Initialize variables
$name = $specialization = $phone = $email = "";
$name_err = $specialization_err = $phone_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate doctor's name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter doctor's name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate specialization
    if (empty(trim($_POST["specialization"]))) {
        $specialization_err = "Please enter specialization.";
    } else {
        $specialization = trim($_POST["specialization"]);
    }

    // Validate phone number (optional)
    if (!empty(trim($_POST["phone"]))) {
        $phone = trim($_POST["phone"]);
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            $phone_err = "Invalid phone number format.";
        }
    }

    // Validate email (optional)
    if (!empty(trim($_POST["email"]))) {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        }
    }

    // Check input errors before inserting into database
    if (empty($name_err) && empty($specialization_err) && empty($phone_err) && empty($email_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO doctors (name, specialization, phone, email) VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssss", $param_name, $param_specialization, $param_phone, $param_email);

            // Set parameters
            $param_name = $name;
            $param_specialization = $specialization;
            $param_phone = $phone;
            $param_email = $email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to index.php or another page after successful insertion
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        } else {
            echo "Oops! Something went wrong with the database preparation.";
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
    <title>Add Doctor</title>
     <style>
/* Reset or Normalize CSS */
body, html, * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Basic styling */
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-color: #f4f4f4; /* Light gray background */
}

.wrapper {
    width: 60%;
    margin: 50px auto;
    background: #fff; /* White background */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

h2 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #007bff; /* Blue header */
}

p {
    font-size: 16px;
    color: #555; /* Dark gray text */
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #007bff; /* Blue label */
}

input[type="text"],
input[type="email"],
select {
    width: calc(100% - 10px);
    padding: 10px;
    font-size: 16px;
    border: 1px solid #007bff; /* Blue border */
    border-radius: 4px;
}

select {
    width: 100%;
}

.error {
    color: red; /* Red error message */
    font-size: 14px;
}

input[type="submit"] {
    background-color: #007bff; /* Blue submit button */
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

input[type="submit"]:hover {
    background-color: #0056b3; /* Darker blue hover state */
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
/* Optional: Responsive Design */
@media (max-width: 768px) {
    .wrapper {
        width: 80%;
    }
}


     </style>
</head>
<body>
    <div class="wrapper">
        <h2>Add New Doctor</h2>
        <p>Please fill in doctor's information.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <label>Doctor's Name</label>
                <input type="text" name="name" value="<?php echo $name; ?>">
                <span class="error"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($specialization_err)) ? 'has-error' : ''; ?>">
                <label>Specialization</label>
                <select name="specialization">
                    <option value="">Select Specialization</option>
                    <option value="Cardiology" <?php echo ($specialization == 'Cardiology') ? 'selected' : ''; ?>>Cardiology</option>
                    <option value="Dermatology" <?php echo ($specialization == 'Dermatology') ? 'selected' : ''; ?>>Dermatology</option>
                    <option value="Endocrinology" <?php echo ($specialization == 'Endocrinology') ? 'selected' : ''; ?>>Endocrinology</option>
                    <option value="Gastroenterology" <?php echo ($specialization == 'Gastroenterology') ? 'selected' : ''; ?>>Gastroenterology</option>
                    <option value="Neurology" <?php echo ($specialization == 'Neurology') ? 'selected' : ''; ?>>Neurology</option>
                    <option value="Ophthalmology" <?php echo ($specialization == 'Ophthalmology') ? 'selected' : ''; ?>>Ophthalmology</option>
                    <option value="Orthopedics" <?php echo ($specialization == 'Orthopedics') ? 'selected' : ''; ?>>Orthopedics</option>
                </select>
                <span class="error"><?php echo $specialization_err; ?></span>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo $phone; ?>">
                <span class="error"><?php echo (!empty($phone_err)) ? $phone_err : ''; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
                <span class="error"><?php echo (!empty($email_err)) ? $email_err : ''; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
        <br>
     <!-- Back to Home Page Button -->
     <div class="button-container">
            <a href="index.php">Back to Home Page</a>
        </div>
    </div>
    </div>
   
</body>
</html>
