<?php

include '../includes/config.php';

list($hostName, $port) = explode(':', $host);
$charset = 'utf8mb4';

$dsn = "mysql:host=$hostName;port=$port;dbname=$database;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);

    $tinNumber = $_POST['tinNumber'] ?? '';
    $emailAdrs = $_POST['emailAdrs'] ?? '';

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT applicantID FROM applicant_info WHERE emailAdrs = ?");
    $stmt->execute([$emailAdrs]);
    $applicant = $stmt->fetch();

    if ($applicant) {
        echo "This email address is already registered.";
        exit;
    }


    // 1. Insert applicant_info
    $stmt = $pdo->prepare("INSERT INTO applicant_info 
        (bankAccountNumber, emailAdrs, applicantName, motherMaidenName, age, birthDate, birthPlace, civilStatus, gender, nationality, dependentsNum, educAttainment, homePNum, mobilePNum, presentHomeAdrs, lengthOfStay, adrsStatus, monthlyPay, tinNumber) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $_POST['bankAccountNumber'],
        $emailAdrs,
        $_POST['applicantName'],
        $_POST['motherMaidenName'],
        $_POST['age'],
        $_POST['birthDate'],
        $_POST['birthPlace'],
        $_POST['civilStatus'],
        $_POST['gender'],
        $_POST['nationality'],
        $_POST['dependentsNum'],
        $_POST['educAttainment'],
        $_POST['homePNum'],
        $_POST['mobilePNum'],
        $_POST['presentHomeAdrs'],
        $_POST['lengthOfStay'],
        $_POST['adrsStatus'],
        $_POST['monthlyPay'],
        $tinNumber
    ]);

    $applicantID = $pdo->lastInsertId();

    // 2. Insert employment_info
    $rank = $_POST['rank'];
    if ($rank === 'Others' && !empty($_POST['otherRank'])) {
        $rank = $_POST['otherRank'];
    }

    // Handle Date of Hire - set to NULL if empty or if unemployed/retired
    $dateOfHire = null;
    if ($_POST['typeOfEmploy'] !== 'Unemployed' && $_POST['typeOfEmploy'] !== 'Retired') {
        $dateOfHire = !empty($_POST['dateOfHire']) ? $_POST['dateOfHire'] : null;
    }

    $stmt = $pdo->prepare("INSERT INTO employment_info 
        (tinNumber, employerName, employerAdd, typeOfEmploy, employStatus, rank, curPosition, sssNum, dateOfHire, curLengthService, officeNum, officeEmailAdd, hrContactPerson, officeTelNum, dayToCall, prevEmployer, prevLengthService, prevPosition, totalYrsWorking) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $tinNumber,
        $_POST['employerName'],
        $_POST['employerAdd'],
        $_POST['typeOfEmploy'],
        $_POST['employStatus'],
        $rank,
        $_POST['curPosition'],
        $_POST['sssNum'],
        $dateOfHire, 
        $_POST['curLengthService'],
        $_POST['officeNum'],
        $_POST['officeEmailAdd'],
        $_POST['hrContactPerson'],
        $_POST['officeTelNum'],
        $_POST['dayToCall'],
        $_POST['prevEmployer'],
        $_POST['prevLengthService'],
        $_POST['prevPosition'],
        $_POST['totalYrsWorking']
    ]);

    // 3. Insert credit cards
    if (!empty($_POST['cardNo']) && is_array($_POST['cardNo'])) {
        foreach ($_POST['cardNo'] as $index => $cardNo) {
            $stmt = $pdo->prepare("INSERT INTO creditcard_info 
                (cardNo, creditCard, creditLimit, expiryDate, applicantID) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $cardNo,
                $_POST['creditCard'][$index],
                $_POST['creditLimit'][$index],
                $_POST['expiryDate'][$index],
                $applicantID
            ]);
        }
    }

    header("Location: ../pages/profile_submitted_page.php");
    exit();

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>