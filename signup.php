<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "MovieDB";
$conn = mysqli_connect($server, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$errorMessage = ""; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $mail = $_POST['mail'];
    $uname = $_POST['Uname'];
    $pass = $_POST['pass'];
    $checkQuery = "SELECT * FROM users WHERE email = '$mail' OR username = '$uname'";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        $errorMessage = "Email or Username already exists. Please try a different one.";
    } else {
        $sql = "INSERT INTO users (name, email, username, password) 
                VALUES ('$name', '$mail', '$uname', '$pass')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>
                    window.location.href = 'login.php';
                  </script>";
            exit();
        } else {
            $errorMessage = "Signup failed: " . mysqli_error($conn);
        }
    }
}
mysqli_close($conn);
?>
<html>
    <head>
        <title>Sign Up</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <style>
            .logincard{
                background: linear-gradient(315deg,black,rgb(154,9,9));                       
                width: 400px;
                margin-top: 160px;
                box-shadow: 5px 5px 5px rgb(30,30,30);
                padding:30px;
                border-radius:15px;
                height:560px;
                font-family:"Playfair";
            }
            
            .login-form-button{
                width:75%;

            }
            .login-form-button:hover{
                background-color:rgb(214, 214, 214);
                color:white;
            }
            .forgot{
                text-decoration:none;
                font-size:12px;
                color:white;
                display:block;
                text-align: right;
            }
            .loginpage{
                background-image: url(login-page-background.jpg.jpg);
                background-size: cover;
                height: 100vh;
            }
            .error{
                color: white;
                font-size:13px;
            }
        </style>
    </head>
    <body>
        <div class="loginpage d-flex flex-row justify-content-center">
            <div class="logincard">
                <form action="signup.php" method="POST" onsubmit="return sign()">
                    <h2 class="text-white  text-center mb-4">Sign Up</h2>
                    <input type="text"class="form-control" name="name" placeholder="Name" id="name">
                    <p id="name_error" class="error mb-2"></p>
                    <input type="email"class="form-control mt-4" name="mail" placeholder="Email" id="email">
                    <p id="mail_error" class="error mb-2"></p>
                    <input type="text"class="form-control mt-4" name="Uname" placeholder="User Name" id="uname">
                    <p id="user_error" class="error mb-2"></p>
                    <input type="password" class="form-control mt-4"name="pass" placeholder="Enter Password" id="pass"> 
                    <p id="password_error" class="error mb-2"></p>
                    <input type="password" class="form-control mt-2"name="pass2" placeholder="Confirm Password" id="pass2">   
                    <p id="pass2_error" class="error mb-2"></p>                
                  
                    <div class="text-center mb-3">
                    <button type="submit" class="btn btn-light text-danger login-form-button mt-4 mb-3">Sign Up</button><br>
                    </div> 
                    <?php if (!empty($errorMessage)): ?>
                <centre><p class="error"><?php echo $errorMessage; ?></p></centre>
            <?php endif; ?>
                   </form>
            </div>
        </div>
        <script>
            
            function sign() {
    var name = document.getElementById("name").value;
    var email = document.getElementById("email").value;
    var Username = document.getElementById("uname").value;
    var password = document.getElementById("pass").value;
    var password2 = document.getElementById("pass2").value;
    var isValid = true;

    if (/^[a-zA-Z ]+$/.test(name)) {
        document.getElementById("name_error").innerText = "";
    } else if (name === "") {
        document.getElementById("name_error").innerText = "*This field is required.";
        isValid = false;
    } else {
        document.getElementById("name_error").innerText = "*The name should contain only alphabets and spaces.";
        isValid = false;
    }

    if (/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(email)) {
        document.getElementById("mail_error").innerText = "";
    } else if (email === "") {
        document.getElementById("mail_error").innerText = "*This field is required.";
        isValid = false;
    } else {
        document.getElementById("mail_error").innerText = "*Enter a valid email ID.";
        isValid = false;
    }

    if (Username === "") {
        document.getElementById("user_error").innerText = "*This field is required.";
        isValid = false;
    } else {
        document.getElementById("user_error").innerText = "";
    }

    if (password.length >= 8 && /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password)) {
        document.getElementById("password_error").innerText = "";
    } else if (password === "") {
        document.getElementById("password_error").innerText = "*This field is required.";
        isValid = false;
    } else {
        document.getElementById("password_error").innerText = "*Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.";
        isValid = false;
    }

    if (password2 === "") {
        document.getElementById("pass2_error").innerText = "*This field is required.";
        isValid = false;
    } else if (password2 !== password) {
        document.getElementById("pass2_error").innerText = "*Passwords must match.";
        isValid = false;
    } else {
        document.getElementById("pass2_error").innerText = "";
    }
return isValid;
}
        </script>
    </body>
</html>