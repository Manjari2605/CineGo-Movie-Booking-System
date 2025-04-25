<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$total_amount = isset($_GET['total_amount']) ? floatval($_GET['total_amount']) : 0.0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgb(17, 17, 17);
            font-family: "Playfair";
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .confirmation-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <h2>Booking Confirmed!</h2>
        <p><strong>Total Amount Paid:</strong> ₹<?php echo number_format($total_amount, 2); ?></p>
        <a href="booking_history.php" class="btn btn-primary">View Booking History</a>
    </div>
</body>
</html>
