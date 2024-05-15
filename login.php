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
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username exists
    $stmt = $conn->prepare("SELECT * FROM user_details WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username exists, check password
        $user = $result->fetch_assoc();
        if ($password == $user['PASSWORD']) {
            // Password is correct, retrieve email
            $email = $user['EMAILADD'];
            // Redirect to index.html with email as URL parameter
            header("Location: index.html?email=" . urlencode($email));
            exit();
        } else {
            // Wrong password, redirect with alert
            header("Location: login.html?msg=wrong_password");
            exit();
        }
    } else {
        // Username not found, redirect with alert
        header("Location: login.html?msg=username_not_found");
        exit();
    }

    $stmt->close();
} else {
    // Handle non-POST requests
    echo "Invalid request.";
}

$conn->close();
?>