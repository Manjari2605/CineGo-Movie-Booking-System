<?php
session_start();
if (!isset($_SESSION['movie_name'])) {
    echo "<script>window.location.href = 'homepage.php';</script>";
    die();
}

$movie_name = $_SESSION['movie_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theater'], $_POST['date'], $_POST['time'])) {
    $_SESSION['theater'] = $_POST['theater'];
    $_SESSION['show_date'] = $_POST['date'];
    $_SESSION['show_time'] = $_POST['time'];
    echo "<script>window.location.href = 'seat.php';</script>";
    die();
}

$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$dateQuery = "SELECT DISTINCT show_date FROM shows s JOIN movies m ON s.movie_id = m.id WHERE m.name = '$movie_name'";
$dateResult = mysqli_query($conn, $dateQuery);

$dates = [];
while ($row = mysqli_fetch_assoc($dateResult)) {
    $dates[] = $row['show_date'];
}

$dates = [];
for ($i = 0; $i < 3; $i++) {
    $dates[] = date('Y-m-d', strtotime("+$i day"));
}

if (!isset($_GET['date']) && !empty($dates)) {
    $selected_date = $dates[0];
} else {
    $selected_date = $_GET['date'] ?? null;
}

$theaters = [];
if ($selected_date) {
    $theaterQuery = "SELECT t.theater_name AS theater_name, t.location AS location, GROUP_CONCAT(DISTINCT s.show_time ORDER BY s.show_time) AS times
                     FROM shows s
                     JOIN movies m ON s.movie_id = m.id
                     JOIN theaters t ON s.theater_id = t.theater_id
                     WHERE m.name = '$movie_name' AND s.show_date = '$selected_date'
                     GROUP BY t.theater_id";
    $theaterResult = mysqli_query($conn, $theaterQuery);

    $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    while ($row = mysqli_fetch_assoc($theaterResult)) {
        $times = explode(',', $row['times']);
        $future_times = [];

        foreach ($times as $time) {
            $show_datetime = new DateTime($selected_date . ' ' . $time, new DateTimeZone('Asia/Kolkata'));
            if ($show_datetime > $now) {
                $future_times[] = $time;
            }
        }

        if (!empty($future_times)) {
            $theaters[] = [
                'name' => $row['theater_name'],
                'location' => $row['location'],
                'times' => $future_times
            ];
        }
    }
}
?>
<html>
    <head>
        <title>Shows</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            body {
                background-color: rgb(17, 17, 17);
                font-family: 'Playfair';
                color: white;
            }
            nav a {
                text-decoration: none;
            }
            nav a.home:hover {
                background-color: #8a1313;
                border-radius: 5px;
            }
            .navbar-body {
                background: linear-gradient(315deg, black, rgb(154, 9, 9));                     
            }
            .date-container {
                background-color: #222;
                border-radius: 8px;
                display: flex;
                justify-content: center;   
                gap: 20px;
                padding: 15px;    
                box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.1);
            }
            .date-box {
                background-color: #444;
                border-radius: 5px;
                padding: 10px;
                text-align: center;
                color: white;
                width: 50px;
                cursor: pointer;
                text-decoration: none;
            }
            .date-box:hover {
                text-decoration: none; 
            }
            .selected {
                background-color: #8a1313;
            }
            .theater-container {
                background-color: #222;
                margin: 10px;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.1);
                color:white;
                font-size: 20px;
            }
            .showtime-btn {
                padding: 10px 15px;
                border-radius: 5px;
                margin: 10px;
                border-width: 2px;
                background-color: #760e0e;
                color: #cecece;
                font-weight: bold;
                border-style:solid;
                border-color: #760e0e;
            }
            .no-shows {
                text-align: center;
                color: #ccc;
                font-size: 18px;
                margin-top: 20px;
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
        <div class="container mt-3">
            <div class="date-container">
                <?php foreach ($dates as $date): ?>
                    <a href="shows.php?date=<?php echo $date; ?>" class="date-box <?php echo ($selected_date === $date) ? 'selected' : ''; ?>">
                        <?php echo date('d', strtotime($date)); ?><br>
                        <?php echo date('D', strtotime($date)); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div id="theater-list" class="mt-4">
                <?php if (!empty($theaters)): ?>
                    <?php foreach ($theaters as $theater): ?>
                        <div class="theater-container">
                            <div class="theater-name"><strong><?php echo $theater['name']; ?></strong></div>
                            <div class="location"><?php echo $theater['location']; ?></div>
                            <div class="showtimes">
                                <?php
                                $now = new DateTime('now'); 
                                foreach ($theater['times'] as $time):
                                    $show_datetime = new DateTime($selected_date . ' ' . $time); 
                                    if ($show_datetime > $now):
                                ?>
                                    <form method="POST" action="shows.php" style="display: inline;">
                                        <input type="hidden" name="theater" value="<?php echo $theater['name']; ?>">
                                        <input type="hidden" name="date" value="<?php echo $selected_date; ?>">
                                        <input type="hidden" name="time" value="<?php echo $time; ?>">
                                        <button class="showtime-btn" type="submit"><?php echo date('H:i', strtotime($time)); ?></button>
                                    </form>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (empty($theaters)): ?>
                    <p class="no-shows">No shows available for this date.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php mysqli_close($conn); ?> 
    </body>
</html>