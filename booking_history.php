<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    echo "<script>alert('" . $alert['message'] . "');</script>";
    unset($_SESSION['alert']); 
}

$servername = "localhost"; 
$username = "root"; 
$password = "";
$dbname = "MovieDB";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.id AS booking_id, b.movie_name, b.theater, b.show_date, b.show_time, b.seats, b.status, m.image 
        FROM bookings b 
        JOIN movies m ON b.movie_name = m.name 
        WHERE b.user_id = $user_id 
        ORDER BY b.show_date DESC";
$result = mysqli_query($conn, $sql);
$bookings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
$moviePages = [
    'Court' => 'court.php',
    'Empuraan' => 'l2.php',
    'Mad2' => 'mad2.php',
    'Sweetheart' => 'sweetheart.php',
    'good-bad-guy' => 'good-bad-guy.php',
    'jaat' => 'jaat.php',
    'The Raja Saab' => 'raja-saab.php',
    'Kannappa' => 'kannappa.php',
    'Bhairavam' => 'bhairavam.php',
    'Hit-3' => 'hit3.php',
    'Gymkhana' => 'gymkhana.php',
    'Salaar' => 'salaar.php',
    'Yug' => 'yug.php',
    'Khaleja' => 'khaleja.php',
    'arya-2' => 'arya2.php'
];

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/0631155e40.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: rgb(17, 17, 17);
            font-family: "Playfair";
            color: white;
        }
        nav {
            background: linear-gradient(315deg, black, rgb(154, 9, 9));
        }
        nav a {
            text-decoration: none;
            font-size: 14px;
        }
        nav a.home:hover {
            background-color: #8a1313;
            border-radius: 5px;
        }
        .navbar-brand img {
            height: 25px;
            width: auto;
        }
        
        .card {
            background-color: #151515;
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            color: white;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 15px;
        }
        .card img {
            border-radius: 10px;
            height: 250px;
            width: 180px;
            margin-left: 15px;
            object-fit: cover;
        }
        .card-body {
            flex: 1;
            padding-right: 15px;
        }
        .card-title {
            font-size: 20px;
            font-weight: bold;
        }
        .card-text {
            font-size: 16px;
        }
        .btn-cancel {
            background-color: #dc3545;
            color: white;
            border-radius: 5px;
            padding: 10px 15px;
            text-align: center;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-cancel:hover {
            background-color: #b02a37;
        }
    </style>
</head>
<body>
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
    <div class="container">
        <h2 class="text-center mt-2">Booking History</h2>
        <?php if (!empty($bookings)) { ?>
            <div class="row">
                <?php foreach ($bookings as $booking) { ?>
                    <div class="col-md-6"> 
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $booking['movie_name']; ?></h5>
                                <p class="card-text"><strong>Theater:</strong> <?php echo $booking['theater']; ?></p>
                                <p class="card-text"><strong>Date:</strong> <?php echo $booking['show_date']; ?></p>
                                <p class="card-text"><strong>Time:</strong> <?php echo $booking['show_time']; ?></p>
                                <p class="card-text"><strong>Seats:</strong> <?php echo $booking['seats']; ?></p>
                                <p class="card-text text-warning"><strong>Status:</strong> <?php echo ucfirst($booking['status']); ?></p>
                                <?php if ($booking['status'] === 'active') { ?>
                                <form method="POST" action="cancel_booking.php" 
                                    onsubmit="return confirmCancellation(this)"
                                    data-show-date="<?php echo $booking['show_date']; ?>" 
                                    data-show-time="<?php echo $booking['show_time']; ?>">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <button type="submit" class="btn-cancel">Cancel</button>
                                </form>
                                <?php } ?>
                            </div>
                            <img src="<?php echo $booking['image']; ?>" alt="<?php echo $booking['movie_name']; ?>">
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p class="text-center">No bookings found.</p>
        <?php } ?>
    </div>
    <script>
function confirmCancellation(form) {
    const showDate = form.dataset.showDate;
    const showTime = form.dataset.showTime;
    const showDateTime = new Date(showDate + 'T' + showTime);
    const now = new Date();

    if (now >= showDateTime) {
        alert("Cannot cancel. The show's time has already started or completed.");
        return false;
    }

    return confirm("Are you sure you want to cancel the booking?");
}
</script>
</body>
</html>