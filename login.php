<?php
session_start();
$errorMessage = ""; 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["mail"];
    $password = $_POST["pass"];
    $conn = mysqli_connect("localhost", "root", "", "MovieDB");
    if (!$conn) {
        $errorMessage = "Database connection failed.";
    } else {
        $sql = "SELECT id FROM users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $row['id'];
            header("Location: homepage.php");
            exit();
        } else {
            $errorMessage = "Invalid email or password."; 
        }
        mysqli_close($conn);
    }
}
?>
<html>
<head>
    <title>Login</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
    <style>  
        .logincard{  
            background: linear-gradient(315deg,black,rgb(154,9,9));                       
            width: 350px;  
            margin-top: 160px;  
            box-shadow: 5px 5px 5px rgb(30,30,30);  
            padding:25px;  
            border-radius:15px;  
            height:430px;  
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
        b:hover{  
            text-decoration: underline;  
        }  
    </style>  
</head>
<body>
<div class="loginpage d-flex flex-row justify-content-center">  
    <div class="logincard">  
        <form method="POST" action="" onsubmit="return validateLogin();">  
            <h2 class="text-white text-center mb-4">Login</h2>  
            <input type="text" class="form-control mb-2" id="mail" name="mail" placeholder="Email" value="<?php echo isset($_POST['mail']) ? $_POST['mail'] : ''; ?>">  
            <p class="error" id="mail_error"></p>  

            <input type="password" class="form-control mb-2 mt-4" name="pass" id="passw" placeholder="Password">  
            <p class="error" id="password_error"></p>  

            <a href="forgot.php" class="forgot"><b>Forgot Password?</b></a>  
            <div class="text-start mt-2 ">  
                <input type="checkbox">  
                <label class="text-white">Remember me</label>  
            </div>  
            <div class="text-center mb-3">  
                <button type="submit" class="btn btn-light text-danger login-form-button mt-2">Sign In</button><br>  
            </div>  
            <?php if (!empty($errorMessage)): ?>
                <p class="error text-center"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <center>  
                <a href="signup.php" style="text-decoration:none; font-size:13px; color:white;">  
                    Don't have an Account ? <b>Sign Up</b>  
                </a>  
            </center>  
        </form>  
    </div>  
</div>  

<script>
function validateLogin() {
    var email = document.getElementById("mail").value;
    var password = document.getElementById("passw").value;
    var isValid = true;

    if (/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(email)) {
        document.getElementById("mail_error").innerText = "";
    } else if (email === "") {
        document.getElementById("mail_error").innerText = "*This field is required.";
        isValid = false;
    } else {
        document.getElementById("mail_error").innerText = "*Enter a valid email id.";
        isValid = false;
    }

    if (/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\W]{8,}$/.test(password)) {
        document.getElementById("password_error").innerText = "";
    } else if (password === "") {
        document.getElementById("password_error").innerText = "*This field is required.";
        isValid = false;
    } else {
        document.getElementById("password_error").innerText = "*Password must contain at least one letter, one number, and one special character.";
        isValid = false;
    }

    return isValid;
}
</script>
</body>
</html>