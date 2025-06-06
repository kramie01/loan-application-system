<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['applicant_id'])) {
    die("Unauthorized access.");
}

$applicantID = $_SESSION['applicant_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Example for updating applicant_info
    $dependentsNum = $_POST['dependentsNum'];
    $presentHomeAdrs = $_POST['presentHomeAdrs'];
    $lengthOfStay = $_POST['lengthOfStay'];
    $adrsStatus = $_POST['adrsStatus'];
    $monthlyPay = $_POST['monthlyPay'];
    
    $employerName = $_POST['employerName'];
    $employerAdd = $_POST['employerAdd'];
    $typeOfEmploy = $_POST['typeOfEmploy'];
    $employStatus = $_POST['employStatus'];
    $rank = $_POST['rank'];
    $curPosition = $_POST['curPosition'];
    $dateOfHire = $_POST['dateOfHire'];
    $curLengthService = $_POST['curLengthService'];
    $officeNum = $_POST['officeNum'];
    $officeEmailAdd = $_POST['officeEmailAdd'];
    $hrContactPerson = $_POST['hrContactPerson'];
    $officeTelNum = $_POST['officeTelNum'];
    $dayToCall = $_POST['dayToCall'];
    $prevEmployer = $_POST['prevEmployer'];
    $prevLengthService = $_POST['prevLengthService'];
    $prevPosition = $_POST['prevPosition'];
    $totalYrsWorking = $_POST['totalYrsWorking'];

    // Connect using MySQLi
    $conn = new mysqli($host, $user, $password, $database);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Update applicant_info
    $sql1 = "UPDATE applicant_info SET dependentsNum=?, presentHomeAdrs=?, lengthOfStay=?, adrsStatus=?, monthlyPay=? WHERE applicantID=?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("sssssi", $dependentsNum, $presentHomeAdrs, $lengthOfStay, $adrsStatus, $monthlyPay, $applicantID);
    $stmt1->execute();

    // Update employment_info
    $sql2 = "UPDATE employment_info SET employerName=?, employerAdd=?, typeOfEmploy=?, employStatus=?, rank=?, curPosition=?, dateOfHire=?, curLengthService=?, officeNum=?, officeEmailAdd=?, hrContactPerson=?, officeTelNum=?, dayToCall=?, prevEmployer=?, prevLengthService=?, prevPosition=?, totalYrsWorking=? WHERE tinNumber=(SELECT tinNumber FROM applicant_info WHERE applicantID=?)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ssssssssssssssssis", $employerName, $employerAdd, $typeOfEmploy, $employStatus, $rank, $curPosition, $dateOfHire, $curLengthService, $officeNum, $officeEmailAdd, $hrContactPerson, $officeTelNum, $dayToCall, $prevEmployer, $prevLengthService, $prevPosition, $totalYrsWorking, $applicantID);
    $stmt2->execute();

    $conn->close();

    // Redirect to profile view page
    header("Location: ../pages/cprofile_page.php?status=updated");
    exit();
}
?>
