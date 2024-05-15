<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "powerdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $department = $_POST['department']; // Added department variable

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM user_details WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<script>alert("Username already taken. Please choose a different username.");</script>';
        echo '<script>window.location.href = "signup.html";</script>';
        exit; // Stop further execution after redirection
    } else {
        // Insert user details into the database
        $stmt = $conn->prepare("INSERT INTO user_details (FIRSTNAME, LASTNAME, USERNAME, PASSWORD, EMAILADD, PHONENUMBER, AGE, GENDER, DEPT) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssiss", $firstName, $lastName, $username, $password, $email, $phone, $age, $gender, $department); // Added department binding
        
        if ($stmt->execute()) {
            echo '<script>alert("User registered successfully.");</script>';
            // Redirect to login.html after successful registration
            echo '<script>window.location.href = "login.html";</script>';
            exit; // Stop further execution after redirection
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
} else {
    echo "Invalid request."; // Handle non-POST requests
}

$conn->close();
?>

