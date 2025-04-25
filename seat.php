<?php
session_start();
if (!isset($_SESSION['theater'], $_SESSION['show_date'], $_SESSION['show_time'])) {
    echo "<script>window.location.href = 'shows.php';</script>";
    die();
}
$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['seats']) && $_POST['seats'] !== "") {
        $seats = rtrim($_POST['seats'], ',');
        $_SESSION['seats'] = $seats;

        $seatArray = explode(',', $seats);
        $total_price = 0;

        foreach ($seatArray as $seat) {
            if ($seat !== "") {
                $query = "UPDATE seat SET status='booked' WHERE seat_number='$seat'";
                mysqli_query($conn, $query);

                $row = substr($seat, 0, 1);
                if ($row === "A") {
                    $total_price += 210;
                } else {
                    $total_price += 150;
                }
            }
        }

        $_SESSION['total_price'] = $total_price;
        if (isset($_POST['from_food']) && $_POST['from_food'] === 'yes') {
            echo "<script>window.location.href = 'food.php';</script>";
        } else {
            $_SESSION['food_total'] = 0;
            echo "<script>window.location.href = 'payment.php';</script>";
        }
        die();
    } else {
        $_SESSION['error'] = "Please select at least one seat.";
        echo "<script>window.location.href = 'seat.php';</script>";
        die();
    }
}

$theater = $_SESSION['theater'];
$show_date = $_SESSION['show_date'];
$show_time = $_SESSION['show_time'];

$seats_booked = [];
$query = "SELECT s.seat_number FROM seat s
          WHERE s.show_id = (
              SELECT show_id FROM shows 
              WHERE theater_id = (
                  SELECT theater_id FROM theaters WHERE theater_name = '$theater'
              ) AND show_date = '$show_date' AND show_time = '$show_time'
          ) AND s.status = 'booked'";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $seats_booked[] = $row['seat_number'];
}
mysqli_close($conn);
?>

<html>
    <head>
        <title>Seat Booking</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <style>
            .screen{
                background: rgb(215, 215, 215);
                margin:20px auto;
                width:60%;
                height:50px;
                box-shadow: 3px 6px 6px rgb(68, 68, 68);
                border-width: 3px;
                border-color: rgb(176, 175, 175);
                border-style:solid;
                border-radius: 5px;
            }
            .row{
                display:flex;
                justify-content: center;
            }
            .seat{
                width:30px;
                height:30px;
                margin:5px;
                border-radius: 5px;
                text-align:center;
                background:rgb(202, 202, 202);
                color:black;
                font-size:11px;
                display:flex; 
                justify-content: center;
                align-items: center; 
                font-weight:bold;             
            }
            .seat.selected {
                background: rgb(31, 154, 9);
            }
            .sold {
                background: rgb(76, 76, 76);
                cursor: not-allowed;
            }
            h5{
                text-align:center;
                margin:25px;
                font-weight:bold;
            }
            nav a {
                text-decoration: none;
            }
            nav a.home:hover {
                background-color: #8a1313;
                border-radius: 5px;
            }
            .navbar-body{
                background: linear-gradient(315deg,black,#9a0909);                      
            }
            body{
                font-family:playfair;
            }
            
        </style>
    </head>
    <body style="background-color:black;">
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
    
    <div id="seat_container" class="mt-4"></div>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (!isset($_SESSION['seats'])): ?>
            <div class="alert alert-warning text-center">
                Seats session variable is not set.
            </div>
        <?php endif; ?>
        <form id="seatForm" method="POST" action="seat.php">
        <input type="hidden" name="from_food" id="from_food_input" value="no">
            <input type="hidden" name="seats" id="seatInput">
        </form>
        <div class="d-flex justify-content-center mt-3">
            <div class="d-flex align-items-center me-5 text-light">
                <div class="seat"></div> Available
            </div>
            <div class="d-flex align-items-center me-5 text-light">
                <div class="seat selected"></div> Selected
            </div>
            <div class="d-flex align-items-center text-light">
                <div class="seat sold"></div> Sold
            </div>
        </div>
        <div class="screen "></div>
        <h5 class="text-danger">Screen this way</h5>
        <center><button type="button" onclick="bookseats()" class="btn btn-danger">Book Now</center>
        <div id="foodModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); justify-content:center; align-items:center;">
        <div style="background:#fff; padding:30px; border-radius:10px; text-align:center; max-width:400px;">
            <h5>Do you want to order food?</h5>
            <button onclick="goToFood()" class="btn btn-success m-3">Yes</button>
            <button onclick="goToPayment()" class="btn btn-danger m-3">No</button>
        </div>
        </div>
        <script>
            var seats_booked = <?php echo json_encode($seats_booked); ?>;
            var layout = [
                { label: "A", blocks: [15] },
                { empty: true },
                { label: "B", blocks: [6, 3, 6] },
                { label: "C", blocks: [6, 3, 6] },
                { label: "D", blocks: [6, 3, 6] },
                { label: "E", blocks: [6, 3, 6] },
                { label: "F", blocks: [6, 3, 6] },
                { empty: true },
                { label: "G", blocks: [2, 5, 1, 5, 2] },
                { label: "H", blocks: [2, 5, 1, 5, 2] },
                { label: "I", blocks: [1, 6, 1, 6, 1] },
                { label: "J", blocks: [1, 6, 1, 6, 1] }
            ];

            var seat = "";
            var i = 0;

            while (i < layout.length) {
                if (layout[i].empty == true) {
                    seat += '<div class="row" style="height:12px;"></div>';
                    i++;
                    continue;
                }

                if (layout[i].label === "A") {
                    seat += '<div class="row">';
                    
                    seat += '<div class="row"><div class="seat fw-bold" style="background:none; color:#ffd700; font-size:14px; width:auto;">Platinum Class - ₹210</div></div>';
                    
                }

                if (layout[i].label === "B") {
                    seat += '<div class="row"><div class="seat  fw-bold" style="background:none; color:#ffd700; font-size:14px; width:auto;">Gold Class - ₹150</div></div>';
                }

                seat += '<div class="row">';
                seat += '<div class="seat fw-bold" style="background:none; color:rgb(190, 9, 9); font-size:16px;">' + layout[i].label + '</div>';

                var count = 1;
                var j = 0;

                while (j < layout[i].blocks.length) {
                    var block = layout[i].blocks[j];
                    var k = 0;

                    while (k < block) {
                        if (block == 3 || block == 2 || block == 1) {
                            seat += '<div class="seat" style="visibility:hidden;"></div>';
                        } else {
                            var seat_num = layout[i].label + count;
                            var booked = false;
                            for (var b = 0; b < seats_booked.length; b++) {
                                if (seats_booked[b] === seat_num) {
                                    booked = true;
                                    break;
                                }
                            }

                            if (booked) {
                                seat += '<div class="seat sold" data-id="' + seat_num + '">' + seat_num + '</div>';
                            } else {
                                seat += '<div class="seat" onclick="select_seat(this)" data-id="' + seat_num + '">' + seat_num + '</div>';
                            }
                            count++;
                        }
                        k++;
                    }
                    j++;
                }

                seat += '</div>';

                i++;
            }

            document.getElementById("seat_container").innerHTML = seat;
            function select_seat(seat){
                if (seat.classList.contains("sold")) return; 
                seat.classList.toggle("selected");
            }

            function bookseats() {
                var selectedSeats = document.querySelectorAll('.seat.selected');
                if (selectedSeats.length === 0) {
                    alert("Please select at least one seat.");
                    return; 
                }

                var seats = "";
                selectedSeats.forEach(function(seat) {
                    var seatId = seat.getAttribute('data-id'); 
                    if (seatId && seatId !== "") { 
                        seats += seatId + ",";
                    }
                });

                if (seats === "") {
                    alert("Invalid seat selection. Please try again.");
                    return; 
                }

                document.getElementById('seatInput').value = seats.substring(0, seats.length - 1); 
                document.getElementById("foodModal").style.display = "flex";
            }
            function showFoodModal() {
                document.getElementById("foodModal").style.display = "flex";
            }

            function goToFood() {
                document.getElementById('from_food_input').value = "yes";
                document.getElementById('seatForm').submit();
            }

            function goToPayment() {
                document.getElementById('from_food_input').value = "no";
                document.getElementById('seatForm').submit();
            }
            
        </script>
    </body>
</html>