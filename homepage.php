<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['movie_name'])) {
    $_SESSION['movie_name'] = $_POST['movie_name'];
    header("Location: " . $_POST['movie_page']);
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "MovieDB");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$user_id = $_SESSION['user_id'];
$name = ''; 
$userQuery = "SELECT name FROM users WHERE id = $user_id";
$userResult = mysqli_query($conn, $userQuery);
if ($userResult && mysqli_num_rows($userResult) === 1) {
    $userRow = mysqli_fetch_assoc($userResult);
    $name = $userRow['name'];
}
$recommendedMovieIds = [1, 2, 3, 4, 5, 6]; 
$upcomingMovieIds = [7, 8, 9, 10, 11, 12]; 
$rereleaseMovieIds = [13, 14, 15, 16, 17, 18]; 

$recommendedMovies = [];
$upcomingMovies = [];
$rereleaseMovies = [];

$sql = "SELECT id, name, image FROM movies WHERE id IN (" . implode(',', $recommendedMovieIds) . ")";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $recommendedMovies[] = $row;
}

$sql = "SELECT id, name, image FROM movies WHERE id IN (" . implode(',', $upcomingMovieIds) . ")";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $upcomingMovies[] = $row;
}

$sql = "SELECT id, name, image FROM movies WHERE id IN (" . implode(',', $rereleaseMovieIds) . ")";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $rereleaseMovies[] = $row;
}

$searchResults = [];
$query = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query']) && !empty($_POST['query'])) {
    $query = $_POST['query'];
    $searchSql = "SELECT name, image FROM movies WHERE name LIKE ? OR genre LIKE ?";
    $searchStmt = $conn->prepare($searchSql);
    $searchTerm = '%' . $query . '%';
    $searchStmt->bind_param("ss", $searchTerm, $searchTerm);
    $searchStmt->execute();
    $searchResult = $searchStmt->get_result();
    while ($row = $searchResult->fetch_assoc()) {
        $searchResults[] = $row;
    }
    $searchStmt->close();
}

$conn->close();

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
    'HIT' => 'hit3.php',
    'Gymkhana' => 'gymkhana.php',
    'Salaar' => 'salaar.php',
    'Yug' => 'yug.php',
    'Khaleja' => 'khaleja.php',
    'arya-2' => 'arya2.php'
];
?>
<html>
    <head>
        <title>Homepage</title>
        <link href="home.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://kit.fontawesome.com/0631155e40.js" crossorigin="anonymous"></script>
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
            .search-results ul {
        display: flex; 
        flex-wrap: wrap; 
        list-style-type: none; 
        padding: 0;
        margin: 0;
        justify-content: center;
            } 
    .search-results.active {
                display: block; 
            }
    .search-results li {
        margin: 10px;
        text-align: center; 
        flex: 0 0 200px; 
    }

    .search-results img {
        width: 100%; 
        height: 250px;
        border-radius: 5px;
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
                        <form class="d-flex ms-3" role="search" method="POST" action="homepage.php">
                            <input class="form-control me-2" type="search" name="query" placeholder="Search movies..." aria-label="Search" value="<?php echo htmlspecialchars($query); ?>">
                            <button class="btn btn-outline-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
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
                        <span class="nav-link text-white">Welcome, <?php echo htmlspecialchars($name); ?></span>
                    </div>
                </div>
            </div>
        </nav>
        <div id="searchResults" class="search-results text-center <?php echo ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) ? 'active' : ''; ?>">
        <form method="POST" action="homepage.php" style="position: absolute; top: 10px; right: 10px;">
        <button type="submit" style="background: none; border: none; font-size: 24px; color: white; cursor: pointer;">✖</button>
    </form>

    <center><h4>Search Results:</h4></center>
    <?php if (!empty($searchResults)): ?>
        <ul>
            <?php foreach ($searchResults as $result): ?>
                <li>
                    <img src="<?php echo htmlspecialchars($result['image']); ?>" alt="<?php echo htmlspecialchars($result['name']); ?>">
                    <p><?php echo htmlspecialchars($result['name']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div style="text-align: center; margin-top: 50px;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 100px; color: gray; opacity: 0.7;"></i>
                <p style="color: white; font-size: 20px; margin-top: 20px;">No results found for "<?php echo htmlspecialchars($query); ?>"</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
        <div id="carouselExampleIndicators" class="carousel slide mt-4" data-bs-ride="carousel" data-bs-interval="2500">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://static.businessworld.in/Picture%203%20-%202024-11-11T085317.997_20241111085351_original_image_36.webp" class="d-block carousel-image w-100" alt="Ad 1">
                </div>
                <div class="carousel-item">
                    <img src="https://www.royalenfield.com/content/dam/royal-enfield/super-meteor-650/motorcycles/super-meteor-650-desktop_new.jpg" class="d-block carousel-image w-100" alt="Ad 2">
                </div>
                <div class="carousel-item">
                    <img src="https://i.ytimg.com/vi/sB67kk5JNXI/maxresdefault.jpg" class="d-block carousel-image w-100" alt="Ad 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <div class="container mt-4">
            <h4 class="home-heading">Recommended Movies</h4>
            <div id="recommended-movies" class="movie-row"></div>
            <h4 class="home-heading">Upcoming Movies</h4>
            <div id="upcoming-movies" class="movie-row"></div>
            <h4 class="home-heading">Rereleases</h4>
            <div id="rerelease-movies" class="movie-row"></div>
        </div>
        <footer class="bg-dark text-white text-center p-2 mt-4">
            <div class="container p-3">
                <p>&copy; 2025 CineGo. All Rights Reserved.</p>
                <p>
                    <a href="#" class="text-white p-2">Privacy Policy</a>
                    <a href="#" class="text-white p-2">Terms of Service</a>
                    <a href="#" class="text-white p-2">Contact Us</a>
                </p>
                <div class="mt-2 p-2">
                    <a href="https://www.facebook.com/" class="text-white icon-circle">
                        <i class="fa-brands fa-facebook-f"></i> 
                    </a>
                    <a href="https://x.com/?lang=en-in" class="text-white  icon-circle">
                        <i class="fab fa-twitter"></i> 
                    </a>
                    <a href="https://www.instagram.com/" class="text-white icon-circle">
                        <i class="fab fa-instagram"></i> 
                    </a>
                </div>
            </div>
        </footer>
        <script>
            const recommendedMovies = <?php echo json_encode($recommendedMovies); ?>;
            const upcomingMovies = <?php echo json_encode($upcomingMovies); ?>;
            const rereleaseMovies = <?php echo json_encode($rereleaseMovies); ?>;
            const moviePages = <?php echo json_encode($moviePages); ?>;

            function renderMovies(movies, containerId) {
                const container = document.getElementById(containerId);
                container.innerHTML = movies.map(movie => `
                    <div class="movie-card">
                        <form method="POST" action="homepage.php">
                            <input type="hidden" name="movie_name" value="${movie.name}">
                            <input type="hidden" name="movie_page" value="${moviePages[movie.name] || 'default-movie.php'}">
                            <button type="submit" style="border: none; background: none;">
                                <img src="${movie.image}" class="card1-image" alt="${movie.name}">
                            </button>
                        </form>
                    </div>
                `).join('');
            }

            renderMovies(recommendedMovies, 'recommended-movies');
            renderMovies(upcomingMovies, 'upcoming-movies');
            renderMovies(rereleaseMovies, 'rerelease-movies');
        </script>
    </body>
</html>