<?php
// Set the timezone to Kolkata/Chennai
date_default_timezone_set('Asia/Kolkata');
// Include PHPExcel classes
require 'PHPExcel/Classes/PHPExcel.php';
require 'PHPExcel/Classes/PHPExcel/IOFactory.php'; // Include IOFactory as well if needed

// Check if file is uploaded
if ($_FILES['file']['error'] == 0) {
    // Connect to MySQL database
    $conn = new mysqli('localhost', 'root', '', 'powerdb', 3306); // Adjust as per your database details

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get uploaded file data
    $file = $_FILES['file']['tmp_name'];

    // Get the selected sheet date
    $sheetDate = $_POST['sheetDate'];
    // Get current date and time
    $currentDateTime = date("Y-m-d H:i:s");

    try {
        // Load the uploaded file using PHPExcel
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        // Get the active sheet
        $sheet = $objPHPExcel->getActiveSheet();

        // Get the highest row and column numbers
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Iterate through each row in the sheet starting from the third row
        for ($row = 3; $row <= $highestRow; $row++) {
            // Get cell values for each column
            $time = $sheet->getCell('A' . $row)->getValue();
            $date = $sheetDate; // Use the selected sheet date
            $powerGeneration = $sheet->getCell('C' . $row)->getValue();
            $loadSechSMS2 = $sheet->getCell('D' . $row)->getValue();
            $loadSechSMS3 = $sheet->getCell('E' . $row)->getValue();
            $loadSechSMSTotal = ($sheet->getCell('D' . $row)->getValue()+$sheet->getCell('E' . $row)->getValue());
            $loadSechRailMill = $sheet->getCell('G' . $row)->getValue();
            $loadSechPlateMill = $sheet->getCell('H' . $row)->getValue();
            $loadSechSPM = $sheet->getCell('I' . $row)->getValue();
            $loadSechNSPL = $sheet->getCell('J' . $row)->getValue();
            $total = ($sheet->getCell('D' . $row)->getValue()+$sheet->getCell('E' . $row)->getValue()+$sheet->getCell('G' . $row)->getValue()+$sheet->getCell('H' . $row)->getValue()+$sheet->getCell('I' . $row)->getValue()+$sheet->getCell('J' . $row)->getValue());

            // Insert data into database with current date and time in UPDATED_ON column
            $sql = "INSERT INTO power_table (TIME, DATE, POWER_GENERATION, LOAD_SECH_SMS2, LOAD_SECH_SMS3, LOAD_SECH_SMS_TOTAL, LOAD_SECH_RAILMILL, LOAD_SECH_PLATEMILL, LOAD_SECH_SPM, LOAD_SECH_NSPL, TOTAL, UPDATED_ON) 
                    VALUES ('$time', '$date', '$powerGeneration', '$loadSechSMS2', '$loadSechSMS3', '$loadSechSMSTotal', '$loadSechRailMill', '$loadSechPlateMill', '$loadSechSPM', '$loadSechNSPL', '$total', '$currentDateTime')";
            if ($conn->query($sql) !== TRUE) {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        echo "Data inserted successfully!";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }

    // Close database connection
    $conn->close();
} else {
    echo "Error uploading file!";
}
?>
