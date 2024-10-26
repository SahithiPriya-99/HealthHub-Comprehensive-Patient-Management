<?php
session_start();
include('config.php');

// Redirect to index if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Define variables and initialize with empty values
$username = $password = $email = "";
$username_err = $password_err = $email_err = $register_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($email_err)) {
        // Prepare a select statement
        $sql = "SELECT admin_id FROM admins WHERE username = ? OR email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_email);

            // Set parameters
            $param_username = $username;
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $register_err = "An account with this username or email already exists. Please login.";
                } else {
                    // Prepare an insert statement
                    $sql = "INSERT INTO admins (username, password, email) VALUES (?, ?, ?)";

                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_email);

                        // Set parameters
                        $param_username = $username;
                        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                        $param_email = $email;

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            // Redirect to login page
                            header("Location: admin_login.php");
                        } else {
                            echo "Something went wrong. Please try again later.";
                        }
                    }
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Register</title>
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
        <h2>Admin Register</h2>
        <p>Please fill this form to create an admin account.</p>
        <?php 
        if (!empty($register_err)) {
            echo '<div>' . $register_err . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span><?php echo $username_err; ?></span>
            </div>    
            <div>
                <label>Email</label>
                <input type="text" name="email" value="<?php echo $email; ?>">
                <span><?php echo $email_err; ?></span>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password">
                <span><?php echo $password_err; ?></span>
            </div>
            <br>
            <div>
                <input type="submit" value="Submit">
            </div>
            <p>Already have an account? <a href="admin_login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>
