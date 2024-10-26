<?php
session_start();

// Check if user is not logged in or is not an admin
if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    header("location: user_login.php");
    exit;
}

// Include config file
require_once 'config.php';

// Initialize variables
$category = "";
$category_err = "";

// Define an array of categories
$categories = array("Cardiology", "Dermatology", "Endocrinology", "Gastroenterology", "Neurology", "Ophthalmology", "Orthopedics");

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate category selection
    if (empty(trim($_POST["category"]))) {
        $category_err = "Please select a specialization category.";
    } else {
        $category = trim($_POST["category"]);
    }

    // Check input errors before querying the database
    if (empty($category_err)) {
        // Prepare a select statement
        $sql = "SELECT doctor_id, name, specialization FROM doctors WHERE specialization = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_category);
            $param_category = $category;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $result = $stmt->get_result();
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
    <title>Search Doctors</title>
    <style>
        body {
            background-color: #f0f5f9; /* Light blue background */
            color: #333; /* Dark gray text */
            font-family: Arial, sans-serif;
        }

        .wrapper {
            width: 80%;
            margin: 20px auto;
            background-color: #fff; /* White background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }

        h2 {
            color: #2e6da4; /* Dark blue heading */
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        select,
        input[type="submit"] {
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2; /* Light gray background */
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Light gray background */
        }

        .back-button {
            margin-top: 10px;
        }

        .back-button a {
            background-color: #2e6da4; /* Dark blue button */
            color: #fff; /* White text */
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-button a:hover {
            background-color: #1a4b74; /* Darker blue on hover */
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Search Doctors by Specialization</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div <?php echo (!empty($category_err)) ? 'has-error' : ''; ?>>
                <label>Select Specialization</label>
                <select name="category">
                    <option value="">Select Specialization</option>
                    <?php
                    // Populate dropdown with categories
                    foreach ($categories as $cat) {
                        $selected = ($category == $cat) ? "selected" : "";
                        echo "<option value='$cat' $selected>$cat</option>";
                    }
                    ?>
                </select>
                <span><?php echo $category_err; ?></span>
            </div>
            <div>
                <input type="submit" value="Search">
            </div>
        </form>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($category_err)) : ?>
            <?php if (!empty($result) && $result->num_rows > 0) : ?>
                <h3>Search Results:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Doctor Name</th>
                            <th>Specialization</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row["name"]; ?></td>
                                <td><?php echo $row["specialization"]; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <h3>No doctors found.</h3>
            <?php endif; ?>
        <?php endif; ?>
        <br>
        <!-- Back to Home Page Button -->
        <div class="back-button">
            <a href="index.php">Back to Home Page</a>
        </div>
    </div>
</body>

</html>
