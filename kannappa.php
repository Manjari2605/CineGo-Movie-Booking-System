<?php
session_start();
if (!isset($_SESSION['movie_name'])) {
    header('Location: homepage.php');
    exit();
}

$movie_name = $_SESSION['movie_name'];

$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT * FROM movies WHERE name = '$movie_name'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) === 1) {
    $movie = mysqli_fetch_assoc($result);
} else {
    echo "Movie not found.";
    exit();
}

mysqli_close($conn);
?>  
<html>
    <head>
        <title>Kannappa Movie Details</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://kit.fontawesome.com/0631155e40.js" crossorigin="anonymous"></script>
        <style>
            body {
                background-color: rgb(8, 8, 8);
                font-family:'Playfair';
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
            .Top-section {
                position: relative;
                width: 100%;
                height: 70vh;
            }
            .video-container {
                width: 100%;
                height: 100%;
                position: absolute;
                z-index: -1;
            }
            .video-container video {
                width: 100%;
                height: 100%;
                object-fit: cover;

            }
            .overlay {
                background: rgba(0, 0, 0, 0.5);
                z-index: 0;
            }
            .content {
                position: absolute;
                bottom: 10%;
                left: 5%;
                display: flex;
                align-items: center;
                 gap: 30px;
                color: white;
                z-index: 1;
            }
            .image {
                width: 250px;
                border-radius: 10px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            }
            .details {
                max-width: 400px;
            }
            .cast-container{
                display: flex;
                flex-direction:column;
                gap: 20px;
                padding: 10px;

            }
            .cast-member{
                gap:10px;
                display: flex;
                align-items:center;
                flex-direction: row;

            }
            .cast-image-container {
                width: 100px; 
                height: 100px;
                display:flex;
                align-items:center;
                justify-content:center;
            }
            .cast-image{
                width: 80px;
                height:80px;
                object-fit:cover;
                object-position:Top;
                border: 2px solid #ddd;
                border-radius:50%;
            }
            .cast-details{
                display: flex;
        flex-direction: column;
            }
            .upcoming-text {
                color: #df212e;
                font-weight:bolder;
                font-size: 25px;
                margin-left:15px;
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

        <div class="Top-section mt-3">
            <div class="video-container">
                <video autoplay loop muted>
                    <source src="kannappa.trailer.mp4" type="video/mp4">
                </video>
            </div>
            <div class="overlay"></div>
            <div class="content">
                <div class="poster">
                    <img src="https://upload.wikimedia.org/wikipedia/en/e/ed/Kannappa_%28film%29.jpg" alt="Image not found" class="image">
                </div>
                <div class="details">
                    <h1>Kannappa</h1>
                    <p><strong>Release Date:</strong> 27 June, 2025</p>
                    <div>
                        <span class="badge bg-white text-dark">2D, IMAX 2D</span>
                        <span class="badge bg-white text-dark">Telugu</span>
                        <span class="badge bg-white text-dark">UA 13+</span>
                    </div>
                    <p><strong>Duration:</strong> 2h 35m</p>
                    <p><strong>Genre:</strong> Fantasy</p>
                    <p class="upcoming-text">Upcoming</p>
                </div>
            </div>
        </div>
        <div class="Bottom-section mt-5 mb-3 m-3">
            <h2><strong>About the movie</strong></h2>
            <p>An atheist hunter Kannappa becomes a devotee of Lord Shiva and plucked out his eyes in an act of devotion.</p>
           <hr calss="mt-5">   
           <h2><strong>Top Cast</strong></h2>
          <div class="cast-container mb-2">
           <div class="cast-member">
            <div class="cast-image-container"><img src="https://m.media-amazon.com/images/M/MV5BNWM4ZmUyNWUtNGI4MS00NzBiLTg1OGEtMjk1MWQ1NzIxZTNkXkEyXkFqcGc@._V1_.jpg" class="cast-image"/>
            </div>
            <div class="cast-details ">
            <p><Strong>Vishnu Manchu</Strong></p>
            <p>Actor</p>
        </div>
        </div>
         </div>
         <div class="cast-container mb-2">
            <div class="cast-member">
             <div class="cast-image-container"><img src="https://static.india.com/wp-content/uploads/2023/12/prabhas2.jpg?impolicy=Medium_Widthonly&w=400&h=800" class="cast-image"/>
             </div>
             <div class="cast-details ">
             <p><Strong>Prabhas</Strong></p>
             <p>Actor</p>
         </div>
         </div>
          </div>
          <div class="cast-container mb-2">
            <div class="cast-member">
                <div class="cast-image-container"><img src="https://rukminim2.flixcart.com/image/850/1000/xif0q/poster/y/e/t/small-spos14543-poster-magadheera-kajal-aggarwal-movie-sl-14556-original-imagkb9bpxqqsfcc.jpeg?q=20&crop=false" class="cast-image"/>
             </div>
             <div class="cast-details">
            <p><Strong>Kajal Aggarwal</Strong></p>
             <p>Actress</p>
         </div>
         </div>
          </div>
          <div class="cast-container">
            <div class="cast-member">
             <div class="cast-image-container"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/Mohanlal_Viswanathan_BNC.jpg/800px-Mohanlal_Viswanathan_BNC.jpg" class="cast-image"/>
             </div>
             <div class="cast-details">
             <p><Strong>Mohanlal</Strong></p>
             <p>Actor</p>
         </div>
         </div>
          </div>
        </div>
    </body>
</html>