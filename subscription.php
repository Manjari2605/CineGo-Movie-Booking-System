<?php
 $conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$check = "SELECT * FROM subscriptions WHERE user_id = $user_id AND end_date >= CURDATE()";
 $result = mysqli_query($conn, $check);

if (mysqli_num_rows($result) > 0) {
    header("Location: subscribed.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+30 days'));
    $free_tickets = 4;

    $insert_query = "INSERT INTO subscriptions (user_id, start_date, end_date, free_tickets_remaining) VALUES ($user_id, '$start_date', '$end_date', $free_tickets)";
     mysqli_query($conn, $insert_query);

    header("Location: subscribed.php");
    exit();
}
?>

<html>
    <head>
        <title>Subscription</title>
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
            .modal {
                position: fixed;
                top:0;
                left:0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                display: none;
                justify-content: center;
                align-items: center;               
            }
            .modal-content {
                background: #222;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
                width: 300px;
                text-align: center;
            }   
            .loginpage{  
                background-image: url(login-page-background.jpg.jpg);  
                background-size: cover;  
                height: 100vh;  
            } 
            .subscription-container{
                width: 350px;
                margin: 100px ;
                padding: 20px;
                background: linear-gradient(315deg, black, rgb(154, 9, 9));
                border-radius: 15px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            }
            .premium{
                text-align: center;
                text-shadow: 0 0 10px #f54242;
            }
            .subscribe-button{
                font-weight: bold;
                background: #b10000;
                box-shadow: 0 0 6px #f54242;
                border: none;
                color: white;
                border-radius: 8px;
                padding: 7px;
            }
            .subscribe-button:hover {
                background-color: #d20303;
                box-shadow: 0 0 10px #ff4d4d;
            }
            label{
                font-size: 13px;          
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
        <div class="d-flex justify-content-center align-items-start">
        <div class="subscription-container text-center">
            <h2 class="premium">Premium Plan</h2><br>
            <p>Join CineGo Premium and enjoy access to our top features without restrictions.</p>
            <p>Access at just <strong class="text-warning fs-4">₹499</strong></p>
            <p>Valid for <span class="text-warning">30 days</span> from date of activation.</p>
            <form id="subscriptionForm" action="subscription.php" method="POST">
                <input type="checkbox" name="terms" required>
                <label class="ms-1">I agree to all <a href="terms.html" target="_blank" class=" text-light text-decoration-underline">terms & conditions</a></label><br>
                <button class="subscribe-button mt-3" type="button" onclick="payment()">Subscribe</button>            
            </form>
        </div>
        <div class="subscription-container text-center ms-4" style="width: 400px;">
                <h2 class="premium">Premium Benefits You Get</h2><br>          
                <p>🎬 <strong class="ms-1">4 Free Tickets Every Month</strong></p>
                <p>Enjoy 4 complimentary tickets per month to the latest blockbuster movies. That's almost <strong>₹600+ value</strong> each month, completely free, just for you!</p><br>
                <p>🍿<strong class="ms-1">Exclusive Discounts & Offers</strong></p>
                <p>Get special deals and discounts on movie tickets and food combos, available only to Premium users. Enjoy special offers that make your movie nights more affordable and rewarding!</p>
                <br>
                <p>⭐️ Ready to enjoy more movies, more perks, and more fun? You're just one click away from unlocking all these amazing benefits!</p>
        </div>
    </div>

    <div id="paymentModal" class="modal">
        <div class="modal-content">
          <h4>Enter Payment Details</h4>
          <p id="paymentAmount"></p>
          <select class="form-select mb-2" id="paymentMethod" required onchange="togglePaymentInputs()">
            <option value="" disabled selected>Select Payment Method</option>
            <option value="UPI">UPI</option>
            <option value="Credit">Credit</option>
          </select>
          <div id="creditDetails" style="display: none;">
            <input type="text" class="form-control mb-2" placeholder="Card Number (12 digits)" id="cardNumber">
            <input type="text" class="form-control mb-2" placeholder="CVV (3 digits)" id="cvv">
            <div class="d-flex">
                <select class="form-select mb-2 me-2" id="expiryMonth">
                    <option value="" disabled selected>Month</option>
                    <option value="01">01 - January</option>
                    <option value="02">02 - February</option>
                    <option value="03">03 - March</option>
                    <option value="04">04 - April</option>
                    <option value="05">05 - May</option>
                    <option value="06">06 - June</option>
                    <option value="07">07 - July</option>
                    <option value="08">08 - August</option>
                    <option value="09">09 - September</option>
                    <option value="10">10 - October</option>
                    <option value="11">11 - November</option>
                    <option value="12">12 - December</option>
                </select>
                <select class="form-select mb-2" id="expiryYear">
                    <option value="" disabled selected>Year</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                </select>
            </div>
          </div>
          <div id="upiDetails" style="display: none;">
            <input type="text" class="form-control mb-2" placeholder="UPI ID" id="upiId">
          </div>
          <button class="btn btn-success mt-3" type="button" onclick="confirmPayment()">Confirm Payment</button>
          <button class="btn btn-danger mt-3"  onclick="closeModal()">Close</button>
        </div>
      </div>
        
        <script>
            function payment() {
                if (!document.querySelector('input[name="terms"]').checked) {
                    alert("Please agree to the terms and conditions first.");
                    return;
                }

                document.getElementById("paymentModal").style.display = "flex";
                document.getElementById("creditDetails").style.display = "none";
                document.getElementById("upiDetails").style.display = "none";
                document.getElementById("paymentMethod").selectedIndex = 0;
            }

            function togglePaymentInputs() {
                const selected = document.getElementById("paymentMethod").value;               
                if (selected === "Credit") {
                    document.getElementById("creditDetails").style.display = "block";
                } else {
                    document.getElementById("creditDetails").style.display = "none";
                }
                if (selected === "UPI") {
                    document.getElementById("upiDetails").style.display = "block";
                } else {
                    document.getElementById("upiDetails").style.display = "none";
                }
            }

            function closeModal() {
                document.getElementById("paymentModal").style.display = "none";
            }

            function confirmPayment() {
                const paymentMethod = document.getElementById("paymentMethod").value;

                if (paymentMethod === "Credit") {
                    const cardNumber = document.getElementById("cardNumber").value;
                    const cvv = document.getElementById("cvv").value;
                    const expiryMonth = document.getElementById("expiryMonth").value;
                    const expiryYear = document.getElementById("expiryYear").value;

                    if (!/^\d{12}$/.test(cardNumber)) {
                        alert("Please enter a valid 12-digit numeric card number.");
                        return;
                    }

                    if (!/^\d{3}$/.test(cvv)) {
                        alert("Please enter a valid 3-digit numeric CVV.");
                        return;
                    }

                    if (!expiryMonth || !expiryYear) {
                        alert("Please select the expiry month and year.");
                        return;
                    }
                } else if (paymentMethod === "UPI") {
                    const upiId = document.getElementById("upiId").value;
                    if (!upiId || upiId.length < 10 || !/^[a-zA-Z0-9._-]{10}@[a-zA-Z]{3,}$/.test(upiId)) {
                        alert("Please enter a valid UPI ID.");
                        return;
                    }
                }
                alert("Payment successful! You are now a premium member 🎉");
                setTimeout(() => {
        document.getElementById("subscriptionForm").submit();
    }, 100);
            }
            
        </script>
    </body>
</html>