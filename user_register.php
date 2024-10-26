<?php
// Start session
session_start();

// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check input errors before inserting into database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_username, $param_password, $param_email);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_email = $email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: user_login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
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
    <title>User Registration</title>
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
    width: 360px;
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
input[type="password"],
input[type="submit"] {
    width: calc(100% - 10px);
    padding: 10px;
    font-size: 16px;
    border: 1px solid #007bff; /* Blue border */
    border-radius: 4px;
}

input[type="submit"] {
    background-color: #007bff; /* Blue submit button */
    color: white;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #0056b3; /* Darker blue hover state */
}

.error {
    color: red; /* Red error message */
    font-size: 14px;
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
        <h2>User Registration</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>>
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span class="error"><?php echo $username_err; ?></span>
            </div>
            <div <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>>
                <label>Password</label>
                <input type="password" name="password" value="<?php echo $password; ?>">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>>
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
                <span class="error"><?php echo $confirm_password_err; ?></span>
            </div>
            <div <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>>
                <label>Email</label>
                <input type="text" name="email" value="<?php echo $email; ?>">
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <br>
            <div>
                <input type="submit" value="Register">
            </div>
            <br>
            <p>Already have an account? <a href="user_login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
