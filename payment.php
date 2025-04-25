<?php
session_start();
if (!isset($_SESSION['food_total']) || $_SESSION['food_total'] === "") {
    $_SESSION['food_total'] = 0;
}

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to access this page.";
    die();
}
$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$user_id = $_SESSION['user_id'];
$query = "SELECT free_tickets_remaining FROM subscriptions WHERE user_id = $user_id AND end_date >= CURDATE()";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $subscription_data = mysqli_fetch_assoc($result);
    $user_is_subscribed = true;
    $free_tickets_left = $subscription_data['free_tickets_remaining'];
} else {
    $user_is_subscribed = false;
    $free_tickets_left = 0;
}

$query = "SELECT balance FROM wallet WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $balance = $row['balance'];
} else {
    $balance = 0; 
}

mysqli_free_result($result);

if (!isset($_SESSION['seats']) || empty($_SESSION['seats'])) {
    echo "No seats selected. Please go back and select seats.";
    die();
}

$seats = explode(',', $_SESSION['seats']);
foreach ($seats as $seat) {
    mysqli_query($conn, "UPDATE seat SET status='booked' WHERE seat_number='$seat'");
}

$total_price = isset($_SESSION['total_price']) ? $_SESSION['total_price'] : 0;
$food_total = isset($_SESSION['food_total']) ? $_SESSION['food_total'] : 0;
$total_before_gst = $total_price + $food_total;
$gst = $total_before_gst * 0.10;
$grand_total = $total_before_gst + $gst;

?>

<html>
    <head>
        <title>Payment</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            body {
                background-color: rgb(17, 17, 17);
                font-family: "Playfair";
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
            .container-box {
                background: #222;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
            }
            strong {
                color: #8d929c;
            }
            .modal {
                position: fixed;
                top: 0;
                left: 0;
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
            p {
                font-size: 20px;
            }
            h3 {
                color: #a51717;
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

        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="container-box mb-4">
                        <h3 class="text-center fw-bold">Payment Details</h3>
                        <p><strong>Tickets:</strong> <?php echo count($seats); ?> booked</p>
                        <p style="display:none;"><strong>Price per Ticket:</strong> ₹<span id="ticket_price">150</span></p>
                        <p><strong>Subtotal:</strong> ₹<?php echo $total_price; ?></p>
                        <?php if ($food_total > 0): ?>
                        <p><strong>Food Total:</strong> ₹<?php echo $food_total; ?></p>
                        <?php endif; ?>
                        <p><strong>GST (10%):</strong> ₹<?php echo round($gst, 2); ?></p>
                        <p><strong>Total:</strong> ₹<span id="total_amount"><?php echo round($grand_total, 2); ?></span></p>
                        
                        <?php
                        if ($user_is_subscribed && $free_tickets_left > 0 ) {
                        echo '<div><label><input type="checkbox" id="freeTicketCheckbox" onchange="applyFreeTicket()"> Use 1 Free Ticket</label></div>';
                        }
                        ?>
                        </div>

                    <div class="container-box mt-4">
                        <h3 class="text-center fw-bold">Payment Method</h3>
                        <form id="paymentForm" method="POST" action="processing.php">
                            <input type="hidden" name="total_price" value="<?php echo $grand_total; ?>">
                            <input type="hidden" name="use_free_ticket" id="hidden_use_free_ticket" value="0">                    
                            <div class="form-check fs-5">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="card" value="card" onclick="showModal('card')">
                                <label class="form-check-label" for="card">Credit/Debit Card</label>
                            </div>
                            <div class="form-check fs-5">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="upi" value="upi" onclick="showModal('upi')">
                                <label class="form-check-label" for="upi">UPI</label>
                            </div>
                            <div class="form-check fs-5">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="wallet" value="wallet" onclick="showModal('wallet')">
                                <label class="form-check-label" for="wallet">Wallet (Balance:₹<?php echo $balance; ?>)</label>
                            </div>          
                </div>
            </div>
            <div id="paymentModal" class="modal">
                <div class="modal-content">
                    <h4>Enter Payment Details</h4>
                    <div id="cardDetails" style="display:none;">
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
                    <div id="upiDetails" style="display:none;">
                        <input type="text" class="form-control mb-2" placeholder="UPI ID" id="upiId">
                    </div>
                    <button  type="submit" class="btn btn-success mt-3">Confirm Payment</button>
                    </form>
                    <button class="btn btn-danger mt-3" onclick="closeModal()">Close</button>
                </div>   
                <div id="walletDetails" style="display:none;">
                </div>
        </div>  
<script>
    function showModal(type) {
        var card = document.getElementById("card").checked;
        var upi = document.getElementById("upi").checked;
        var wallet = document.getElementById("wallet").checked;

        if (!card && !upi && !wallet) {
            alert("Select a payment method.");
            return;
        }

        document.getElementById("paymentModal").style.display = "flex";

        if (type === 'card') {
            document.getElementById("cardDetails").style.display = "block";
            document.getElementById("upiDetails").style.display = "none";
            document.getElementById("walletDetails").style.display = "none";
        } else if (type === 'upi') {
            document.getElementById("cardDetails").style.display = "none";
            document.getElementById("upiDetails").style.display = "block";
            document.getElementById("walletDetails").style.display = "none";
        } else if (type === 'wallet') {
            document.getElementById("cardDetails").style.display = "none";
            document.getElementById("upiDetails").style.display = "none";
            document.getElementById("walletDetails").style.display = "block";
        }
    }

    function closeModal() {
        document.getElementById("paymentModal").style.display = "none";
    }

    function applyFreeTicket() {
    const checkbox = document.getElementById('freeTicketCheckbox');
    const hiddenInput = document.getElementById('hidden_use_free_ticket');
    const totalSpan = document.getElementById('total_amount');
    const ticketPrice = 150;
    const totalTickets = <?php echo count($seats); ?>;
    const subtotal = <?php echo $total_price; ?>;
    const gstRate = 0.10;

    if (checkbox.checked) {
        hiddenInput.value = 1;
        const food = <?php echo $food_total; ?>;
        const newSubtotal = subtotal - ticketPrice + food;
        const newGST = newSubtotal * gstRate;
        const newTotal = newSubtotal + newGST;
        totalSpan.innerText = newTotal;
    } else {
        hiddenInput.value = 0;
        const food = <?php echo $food_total; ?>;
        const newSubtotal = subtotal + food;
        const gst = newSubtotal * gstRate;
        const newTotal = newSubtotal + gst;
        totalSpan.innerText = newTotal;
    }
}

    function submitPayment(event) {
        event.preventDefault(); // Prevent the default form submission behavior

        const card = document.getElementById("card").checked;
        const upi = document.getElementById("upi").checked;
        const wallet = document.getElementById("wallet").checked;

        let isValid = true;

        if (!card && !upi && !wallet) {
            alert("Select a payment method.");
            isValid = false;
        }

        if (card) {
            const cardNumber = document.getElementById("cardNumber").value;
            const cvv = document.getElementById("cvv").value;
            const expiryMonth = document.getElementById("expiryMonth").value;
            const expiryYear = document.getElementById("expiryYear").value;

            if (!/^\d{12}$/.test(cardNumber)) {
                alert("Please enter a valid 12-digit numeric card number.");
                isValid = false;
            }

            if (!/^\d{3}$/.test(cvv)) {
                alert("Please enter a valid 3-digit numeric CVV.");
                isValid = false;
            }

            if (!expiryMonth || !expiryYear) {
                alert("Please select the expiry month and year.");
                isValid = false;
            }
        } else if (upi) {
            const upiId = document.getElementById("upiId").value;

            if (!upiId || upiId.length < 10 || !/^[a-zA-Z0-9._-]{10}@[a-zA-Z]{3,}$/.test(upiId)) {
                alert("Please enter a valid UPI ID (at least 10 characters and in the correct format, e.g., example@upi).");
                isValid = false;
            }
        }

        if (!isValid) {
            return; // Stop execution if validation fails
        }

        const checkbox = document.getElementById("freeTicketCheckbox");
        if (checkbox) {
            document.getElementById("hidden_use_free_ticket").value = checkbox.checked ? 1 : 0;
        } else {
            document.getElementById("hidden_use_free_ticket").value = 0;
        }

        document.getElementById("paymentForm").submit(); // Submit the form only if validation passes
    }

    // Attach the submitPayment function to the form's submit event
    document.getElementById("paymentForm").addEventListener("submit", submitPayment);
</script>
    </body>    
</html>
