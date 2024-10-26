<?php
// Include config file
require_once 'config.php';

// Initialize variables
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before querying the database
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT user_id, username, password FROM users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if username exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($user_id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["user_id"] = $user_id;
                            $_SESSION["username"] = $username;

                            // Redirect user to index.php or another page
                            header("location: index.php");
                            exit();
                        } else {
                            // Display an error message if password is not valid
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $login_err = "Invalid username or password.";
                }
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
    <title>User Login</title>
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

.error-message {
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
        <h2>User Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span><?php echo $username_err; ?></span>
            </div>
            <div <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password">
                <span><?php echo $password_err; ?></span>
            </div>
            <br>
            <div>
                <input type="submit" value="Login">
            </div>
            <br>
            <p>Don't have an account? <a href="user_register.php">Sign up now</a>.</p>
            <p><?php echo $login_err; ?></p>
        </form>
    </div>
</body>
</html>
