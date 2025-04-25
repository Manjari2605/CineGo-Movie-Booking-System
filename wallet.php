<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = (float)$_POST['amount'];
    $conn->query("INSERT INTO wallet (user_id, balance) VALUES ($user_id, $amount)
                  ON DUPLICATE KEY UPDATE balance = balance + $amount");
    header("Location: wallet.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_booking'])) {
    $ticket_price = $_POST['ticket_price']; 
    $check = mysqli_query($conn, "SELECT balance FROM wallet WHERE user_id = $user_id");

    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $current_balance = $row['balance'];

        if ($current_balance >= $ticket_price) {
            $new_balance = $current_balance - $ticket_price;
            mysqli_query($conn, "UPDATE wallet SET balance = $new_balance WHERE user_id = $user_id");

            echo "Ticket booking successful! Your new balance is ₹" . $new_balance;
        } else {
            echo "Insufficient balance! Please add funds to your wallet.";
        }
    } else {
        echo "Wallet not found for the user.";
    }
}

$result = $conn->query("SELECT balance FROM wallet WHERE user_id = $user_id");
$row = $result->fetch_assoc();
$balance = $row ? $row['balance'] : 0;
?>
<html>
    <head>
        <title>Wallet</title>
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
            .container-box {
                background: #222;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
            }  
            strong{
                color:#8d929c;
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
            .wallet-container {
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
                background: linear-gradient(315deg, black, rgb(154, 9, 9));
                border-radius: 15px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            }
            .wallet-balance {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .form-control {
                margin-bottom: 15px;
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
        
        <div class="wallet-container text-center">
            <h2>Wallet</h2>
            <p class="wallet-balance">Total Balance: ₹<span id="balance"><?php echo $balance; ?></span></p>
            <form id="addFundsForm" method="post" action="wallet.php" onsubmit="return openModal()">
                <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount to add" required>
                <select class="form-control" id="paymentMethod" required>
                    <option value="" disabled selected>Select Payment Method</option>
                    <option value="UPI">UPI</option>
                    <option value="Credit">Credit</option>
                </select>
                <button type="submit" name="addFunds" class="btn btn-light text-danger">Add Funds</button>
            </form>
        </div>
        <div id="paymentModal" class="modal">
            <div class="modal-content">
                <h4>Enter Payment Details</h4>
                <div id="cardDetails">
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
                <div id="upiDetails">
                    <input type="text" class="form-control mb-2" placeholder="UPI ID" id="upiId">
                </div>
                <button type="button" class="btn btn-success mt-3" onclick="confirmPayment()">Confirm Payment</button>
                <button type="button" class="btn btn-danger mt-3" onclick="closeModal()">Close</button>
            </div>   
        </div>
        <script>

    function openModal(){
        const paymentMethod = document.getElementById("paymentMethod").value;

        if (!paymentMethod) {
            alert("Please select a payment method.");
            return;
        }

        document.getElementById("paymentModal").style.display = "flex";

        if (paymentMethod === "Credit") {
            document.getElementById("cardDetails").style.display = "block";
            document.getElementById("upiDetails").style.display = "none";
        } else if (paymentMethod === "UPI") {
            document.getElementById("cardDetails").style.display = "none";
            document.getElementById("upiDetails").style.display = "block";
        }
        return false;
    }

    function closeModal() {
        document.getElementById("paymentModal").style.display = "none";
    }

    function confirmPayment() {
    const paymentMethod = document.getElementById("paymentMethod").value;
    const amount = parseInt(document.getElementById("amount").value);
   
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
        if (!upiId || !/^[a-zA-Z0-9.\-_]+@[a-zA-Z]+$/.test(upiId)) {
            alert("Please enter a valid UPI ID.");
            return;
        }
    }

    alert("Payment successful! Money added to the wallet 🎉");

    closeModal();

    document.getElementById("addFundsForm").submit(); 
}
        </script>
    </body>
</html>