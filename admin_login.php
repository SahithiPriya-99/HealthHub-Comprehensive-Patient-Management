<?php
session_start();
include('config.php');

// Redirect to index if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Define variables and initialize with empty values
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
        $sql = "SELECT admin_id, username, password FROM admins WHERE username = ?";

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
                    $stmt->bind_result($admin_id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION['admin_id'] = $admin_id;
                            $_SESSION['username'] = $username;

                            // Redirect user to welcome page
                            header("Location: index.php");
                        } else {
                            // Password is not valid
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist
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
    <title>Admin Login</title>
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
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    width: 360px;
    background: #fff; /* White background */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

h2 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #007bff; /* Blue header */
    text-align: center;
}

p {
    font-size: 16px;
    color: #555; /* Dark gray text */
    margin-bottom: 20px;
    text-align: center;
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
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc; /* Light gray border */
    border-radius: 4px;
    box-sizing: border-box;
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
    margin-top: 5px;
    text-align: center;
}

    </style>
</head>
<body>
    <div>
        <h2>Admin Login</h2>
        <p>Please fill in your credentials to login.</p>
        <?php 
        if (!empty($login_err)) {
            echo '<div>' . $login_err . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span><?php echo $username_err; ?></span>
            </div>    
            <div>
                <label>Password</label>
                <input type="password" name="password">
                <span><?php echo $password_err; ?></span>
            </div>
            <br>
            <div>
                <input type="submit" value="Login">
            </div>
            <br>
            <p>Don't have an account? <a href="admin_register.php">Sign up now</a>.</p>
        </form>
    </div>    
</body>
</html>
