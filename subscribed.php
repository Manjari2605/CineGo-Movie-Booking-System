<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT free_tickets_remaining FROM subscriptions WHERE user_id = $user_id AND end_date >= CURDATE()";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $free_tickets_left = $row['free_tickets_remaining'];
    
} else {
    echo "No active subscription found.";
}

$query = "SELECT * FROM subscriptions WHERE user_id = $user_id AND end_date >= CURDATE()";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $start = $row['start_date'];
    $end = $row['end_date'];
    $tickets = $row['free_tickets_remaining'];
    $days_left = floor((strtotime($end) - time()) / 86400);
} else {
    header("Location: subscription.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Premium Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body{
            background-color:rgb(17, 17, 17);
            font-family:"Playfair";
            color:white;
        }
        nav a {
            text-decoration: none;
        }
        nav a.home:hover {
            background-color: #8a1313;
            border-radius: 5px;
        }
        .navbar-body{
            background: linear-gradient(315deg,black,rgb(154,9,9));
        }
        .subscription-container{
            width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: linear-gradient(315deg, black, rgb(154, 9, 9));
            border-radius: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .premium{
            text-align: center;
            text-shadow: 0 0 10px #f54242;
        }
        .loginpage{  
                background-image: url(login-page-background.jpg.jpg);  
                background-size: cover;  
                height: 100vh;  
            }
    </style>
</head>
<body class="loginpage">
<nav class="navbar navbar-expand-lg navbar-body">
                <div class="container">
                    <a class="navbar-brand text-white d-flex align-items-center" href="#">
                        <img src="logo.png" alt="Logo" width="40" height="30" class="me-3">
                        <span class="align-middle">CineGo</span>
                    </a> 
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav ms-auto">
                            <a class="nav-link active home text-white ms-auto" href="homepage.php">Home</a>
                            <div class="dropdown">
                                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="https://www.vhv.rs/dpng/d/436-4363443_view-user-icon-png-font-awesome-user-circle.png" alt="Profile" width="30" height="30" class="rounded-circle ms-4">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                    <li><a class="dropdown-item" href="wallet.php">Wallet</a></li>
                                    <li><a class="dropdown-item" href="subscription.php">Subscription</a></li>
                                    <li><a class="dropdown-item" href="booking_history.php">Booking History</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="login.php">Log Out</a></li>
                                </ul>
                            </div>
            
                        </div>
                    </div>
                </div>
            </nav>    

    <div class="subscription-container">
        <h2 class="premium">You're a Premium Member</h2><br>
        <p><strong>🎬 Free Tickets Left:</strong> <?= $tickets ?></p>
        <p><strong>📅 Valid From:</strong> <?= $start ?></p>
        <p><strong>📅 Valid Till:</strong> <?= $end ?></p>
        <p><strong>⏳ Days Left:</strong> <?= $days_left ?> day(s)</p>
    </div>
</body>
</html>