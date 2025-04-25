<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food_total = 0;

    if (isset($_POST['food']) && is_array($_POST['food'])) {
        foreach ($_POST['food'] as $item => $qty) {
            $qty = intval($qty);
            switch ($item) {
                case 'popcorn_cold_drink_combo': $price = 150; break;
                case 'nachos': $price = 120; break;
                case 'french_fries': $price = 100; break;
                case 'samosa': $price = 50; break;
                case 'sandwich': $price = 70; break;
                case 'popcorn': $price = 90; break;
                default: $price = 0;
            }
            $food_total += $qty * $price;
        }
    }

    $_SESSION['food_total'] = $food_total;

    echo "<script>window.location.href = 'payment.php';</script>";
    die();
}
?>

<html>
<head>
    <title>Food Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #111;
            font-family: 'Playfair', serif;
            color: #fff;
        }
        .navbar-body {
            background: linear-gradient(315deg, black, rgb(154, 9, 9));
        }
        nav a {
            text-decoration: none;
        }
        nav a.home:hover {
            background-color: #8a1313;
            border-radius: 5px;
        }
        .container-box {
            background: #222;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.08);
        }
        .food-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
        }
        .food-item img {
            height: 60px;
            width: 60px;
            border-radius: 10px;
            margin-right: 20px;
            object-fit: cover;
            border: 1px solid #444;
        }
        .food-item span {
            flex: 1;
            font-size: 16px;
            font-weight: 500;
        }
        .food-item input {
            max-width: 80px;
            text-align: center;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }
        .summary-item strong {
            color: #8d929c;
        }
        h3, h4 {
            font-weight: 700;
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
    <h3 class="text-center  mt-3 mb-2">Food & Beverages</h3>

    <div class="container mt-2 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="container-box">
                    <form id="foodForm" method="POST" action="food.php">
                        <div class="food-item">
                            <img src="https://media.istockphoto.com/id/681903568/photo/popcorn-in-box-with-cola.jpg?s=612x612&w=0&k=20&c=0rXGh6COImJ8iYpv99Yt2dQOyMneVw_rJw6QwsZPrh4=" alt="Popcorn Combo">
                            <span>Popcorn + Cold Drink Combo</span>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, -1)">-</button>
                                <input type="text" name="food[popcorn_cold_drink_combo]" value="0" class="form-control text-center mx-2" readonly data-price="150" style="width: 60px;">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, 1)">+</button>
                            </div>
                        </div>
                        <div class="food-item">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSITT_Fj97fJ_ceEDgLjSuHpUOV3YVy5qKjMg&s" alt="Nachos">
                            <span>Nachos</span>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, -1)">-</button>
                                <input type="text" name="food[nachos]" value="0" class="form-control text-center mx-2" readonly data-price="120" style="width: 60px;">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, 1)">+</button>
                            </div>
                        </div>
                        <div class="food-item">
                            <img src="https://whisperofyum.com/wp-content/uploads/2024/10/whisper-of-yum-homemade-french-fries.jpg" alt="French Fries">
                            <span>French Fries</span>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, -1)">-</button>
                                <input type="text" name="food[french_fries]" value="0" class="form-control text-center mx-2" readonly data-price="100" style="width: 60px;">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, 1)">+</button>
                            </div>
                        </div>
                        <div class="food-item">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQitLczDKtPwJ3Okg3MgBIFDQSYaQI7-EivNg&s" alt="Samosa">
                            <span>Samosa</span>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, -1)">-</button>
                                <input type="text" name="food[samosa]" value="0" class="form-control text-center mx-2" readonly data-price="50" style="width: 60px;">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, 1)">+</button>
                            </div>
                        </div>
                        <div class="food-item">
                            <img src="https://rakskitchen.net/wp-content/uploads/2017/03/paneer-sandwich-featured.jpg" alt="Sandwich">
                            <span>Sandwich</span>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, -1)">-</button>
                                <input type="text" name="food[sandwich]" value="0" class="form-control text-center mx-2" readonly data-price="70" style="width: 60px;">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, 1)">+</button>
                            </div>
                        </div>
                        <div class="food-item">
                            <img src="https://www.shutterstock.com/image-photo/wide-classic-box-theater-popcorn-260nw-49079185.jpg" alt="popcorn">
                            <span>Popcorn</span>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, -1)">-</button>
                                <input type="text" name="food[popcorn]" value="0" class="form-control text-center mx-2" readonly data-price="90" style="width: 60px;">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="updateQuantity(this, 1)">+</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="container-box mt-4" id="summaryBox" style="display:none;">
                    <h4 class="text-center mb-3">Order Summary</h4>
                    <div id="summaryContent"></div>
                    <div class="summary-item border-top pt-3 mt-3">
                        <span><strong>Total</strong></span>
                        <span><strong>&#8377;<span id="totalAmount">0</span></strong></span>
                    </div>
                    <button class="btn btn-primary w-100 mt-3" onclick="document.getElementById('foodForm').submit();">Proceed to Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateSummary() {
            const inputs = document.querySelectorAll('#foodForm input[type="text"]');
            let summary = '';
            let total = 0;
            inputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                const price = parseInt(input.getAttribute('data-price'));
                const name = input.parentElement.parentElement.querySelector('span').textContent;
                if (qty > 0) {
                    const itemTotal = qty * price;
                    total += itemTotal;
                    summary += `
                        <div class="summary-item">
                            <span>${name} x ${qty}</span>
                            <span>&#8377;${itemTotal}</span>
                        </div>`;
                }
            });

            const summaryBox = document.getElementById('summaryBox');
            if (total > 0) {
                document.getElementById('summaryContent').innerHTML = summary;
                document.getElementById('totalAmount').innerText = total;
                summaryBox.style.display = 'block';
            } else {
                summaryBox.style.display = 'none';
            }
        }

        function updateQuantity(button, change) {
            const input = button.parentElement.querySelector('input');
            let currentValue = parseInt(input.value) || 0;
            currentValue = Math.max(0, currentValue + change); 
            input.value = currentValue;
            updateSummary();
        }
        updateSummary();
    </script>
</body>
</html>