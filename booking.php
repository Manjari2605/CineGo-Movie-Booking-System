<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    die();
}

if (!isset($_SESSION['movie_name'], $_SESSION['theater'], $_SESSION['show_date'], $_SESSION['show_time'], $_SESSION['seats'])) {
    echo "<script>window.location.href = 'homepage.php';</script>";
    die();
}

$movie_name = $_SESSION['movie_name'];
$theater = $_SESSION['theater'];
$date = $_SESSION['show_date'];
$time = $_SESSION['show_time'];
$seats = is_array($_SESSION['seats']) ? implode(', ', $_SESSION['seats']) : $_SESSION['seats'];

$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

$query = "SELECT show_date, DATE_FORMAT(show_date, '%D %M %Y') AS readable_date 
          FROM shows 
          WHERE show_date = '$date'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $formatted_date = $row['show_date'];
    $readable_date = $row['readable_date'];
} else {
    die("Invalid date. The selected date does not match any available shows.");
}

$time = date("H:i", strtotime($time));

$total_amount = isset($_SESSION['total_price']) ? $_SESSION['total_price'] : 0;
$food_total = isset($_SESSION['food_total']) ? $_SESSION['food_total'] : 0;
$total_before_gst = $total_amount + $food_total;
$gst = $total_before_gst * 0.10;
$grand_total = $total_before_gst + $gst;

$use_free_ticket = isset($_SESSION['use_free_ticket']) ? $_SESSION['use_free_ticket'] : 0;
error_log("Session value for use_free_ticket: " . $use_free_ticket);

error_log("Booking.php - Session Use Free Ticket: " . $use_free_ticket);

$query = "INSERT INTO bookings (user_id, movie_name, theater, show_date, show_time, seats, amount_paid, use_free_ticket) 
          VALUES ($user_id, '$movie_name', '$theater', '$formatted_date', '$time', '$seats', $grand_total, $use_free_ticket)";
if (!mysqli_query($conn, $query)) {
    error_log("Error inserting booking: " . mysqli_error($conn));
    die("Booking failed. Please try again.");
}

mysqli_close($conn);
?>

<html>
<head>
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body {
            background-image: url(login-page-background.jpg.jpg);  
            background-size: cover;  
            font-family: "Playfair";
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .confirmation-box {
            background: #151515;
            padding: 30px;
            text-align: left;
            width: 420px;
            border-radius: 18px;
            box-shadow: 3px 3px 3px #262626;
        }

        h2 {
            color: #df212e;
            font-weight: bold;
            text-align: center;
            margin-bottom: 25px;
        }

        strong {
            display: block;
            font-size: 15px;
            color: #8d929c;
            margin-bottom: 5px;
        }

        p {
            font-size: 18px;
            margin-bottom: 20px;
            color: white;
        }

        .movie-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .seat-box {
            background-color: #212121;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .go {
            border-radius: 20px;
        }

        .bottom-text {
            color: #9d9d9d;
            font-size: 16px;
            text-align: center;
            margin-top: 20px;
        }

        hr {
            border-color: #8e8d8d;
        }
    </style>
</head>
<body>
    <div class="confirmation-box">
        <h2><img src="logo.png" alt="Logo" width="40" height="30" class="me-3">Booking Confirmed!</h2>
        <hr>
        <p class="movie-title"><?php echo $movie_name; ?></p>
        <p><strong>Theater</strong> <?php echo $theater; ?></p>
        <p><strong>Date</strong> <?php echo $readable_date; ?></p> 
        <p><strong>Time</strong> <?php echo $time; ?></p>

        <div class="seat-box">
            <strong>Your Seats</strong>
            <p class="text-danger fw-bold"><?php echo $seats; ?></p>
        </div>
        <hr>
        <center><button class="go btn btn-danger" onclick="goHome()">Go Home</button></center>
        <p class="bottom-text">Enjoy your movie! Please arrive 15 minutes early.</p>
    </div>

    <script>
        window.addEventListener('load', () => {
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 }
            });
        });

        function goHome() {
            window.location.href = "homepage.php";
        }
    </script>
</body>
</html>