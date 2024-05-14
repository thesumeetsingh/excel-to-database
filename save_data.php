<?php
// Include PHPExcel classes
require 'PHPExcel/Classes/PHPExcel.php';
require 'PHPExcel/Classes/PHPExcel/IOFactory.php'; // Include IOFactory as well if needed

// Connect to MySQL database
$conn = new mysqli('localhost', 'root', '', 'powerdb'); // Adjust database credentials as needed

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the posted JSON data
$data = json_decode(file_get_contents('php://input'), true);

try {
    foreach ($data as $row) {
        // Calculate LOAD_SECH_SMS_TOTAL and TOTAL based on provided formula
        $LOAD_SECH_SMS_TOTAL = $row['LOAD_SECH_SMS2'] + $row['LOAD_SECH_SMS3'];
        $TOTAL = $LOAD_SECH_SMS_TOTAL + $row['LOAD_SECH_RAILMILL'] + $row['LOAD_SECH_PLATEMILL'] + $row['LOAD_SECH_NSPL'];

        // Prepare and execute SQL query to insert/update data
        $sql = "INSERT INTO powertable (TIME, DATE, POWER_GENERATION, LOAD_SECH_SMS2, LOAD_SECH_SMS3, LOAD_SECH_SMS_TOTAL, LOAD_SECH_RAILMILL, LOAD_SECH_PLATEMILL, LOAD_SECH_SPM, LOAD_SECH_NSPL, TOTAL) 
                VALUES (NOW(), NOW(), '{$row['POWER_GENERATION']}', '{$row['LOAD_SECH_SMS2']}', '{$row['LOAD_SECH_SMS3']}', '{$LOAD_SECH_SMS_TOTAL}', '{$row['LOAD_SECH_RAILMILL']}', '{$row['LOAD_SECH_PLATEMILL']}', '{$row['LOAD_SECH_SPM']}', '{$row['LOAD_SECH_NSPL']}', '{$TOTAL}')
                ON DUPLICATE KEY UPDATE 
                POWER_GENERATION = '{$row['POWER_GENERATION']}',
                LOAD_SECH_SMS2 = '{$row['LOAD_SECH_SMS2']}',
                LOAD_SECH_SMS3 = '{$row['LOAD_SECH_SMS3']}',
                LOAD_SECH_SMS_TOTAL = '{$LOAD_SECH_SMS_TOTAL}',
                LOAD_SECH_RAILMILL = '{$row['LOAD_SECH_RAILMILL']}',
                LOAD_SECH_PLATEMILL = '{$row['LOAD_SECH_PLATEMILL']}',
                LOAD_SECH_SPM = '{$row['LOAD_SECH_SPM']}',
                LOAD_SECH_NSPL = '{$row['LOAD_SECH_NSPL']}',
                TOTAL = '{$TOTAL}'";
        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    echo "Data inserted/updated successfully!";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Close database connection
$conn->close();
?>
