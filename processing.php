<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['total_price'])) {
    echo "<script>window.location.href = 'payment.php';</script>";
    die();
}

$total_price = $_POST['total_price'];
$payment_method = $_POST['paymentMethod'];

$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to process payment.";
    die();
}

$user_id = $_SESSION['user_id'];
$use_free_ticket = isset($_POST['use_free_ticket']) ? intval($_POST['use_free_ticket']) : 0;
error_log("Processing.php - Use Free Ticket: " . $use_free_ticket); 

$_SESSION['use_free_ticket'] = $use_free_ticket;

if ($use_free_ticket == 1) {
    $query = "SELECT free_tickets_remaining FROM subscriptions WHERE user_id = $user_id AND end_date >= CURDATE()";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $free_tickets = $row['free_tickets_remaining'];

        if ($free_tickets > 0) {
            $update_query = "UPDATE subscriptions SET free_tickets_remaining = free_tickets_remaining - 1 WHERE user_id = $user_id AND end_date >= CURDATE()";
            if (!mysqli_query($conn, $update_query)) {
                error_log("Error updating free tickets: " . mysqli_error($conn));
                die("Error updating free tickets.");
            }
        }
    }
}

if ($payment_method == 'wallet') {
    $query = "SELECT balance FROM wallet WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $balance = $row['balance'];

        if ($balance < $total_price) {
            echo "Insufficient wallet balance.";
            die();
        }

        $new_wallet_balance = $balance - $total_price;
        $update_query = "UPDATE wallet SET balance = '$new_wallet_balance' WHERE user_id = '$user_id'";
        if (!mysqli_query($conn, $update_query)) {
            echo "Error updating wallet balance: " . mysqli_error($conn);
            die();
        }
    } else {
        echo "Error fetching wallet balance.";
        die();
    }
}

$seats = explode(',', $_SESSION['seats']);
foreach ($seats as $seat) {
    mysqli_query($conn, "UPDATE seat SET status='booked' WHERE seat_number='$seat'");
}

mysqli_close($conn);
?>

<html>
<head>
    <title>Processing Payment</title>
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
        .processing-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="processing-container">
        <h2>Processing Payment...</h2>
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script>
        setTimeout(() => {
            window.location.href = "booking.php";
        }, 2000); 
    </script>
</body>
</html>