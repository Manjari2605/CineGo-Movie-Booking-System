<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "MovieDB"; 
$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Connection failed");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mail'], $_POST['pass1'])) {
        $email = $_POST['mail'];
        $newPassword = $_POST['pass1'];

        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $update = "UPDATE users SET password = '$newPassword' WHERE email = '$email'";
            mysqli_query($conn, $update);
            mysqli_close($conn);
            echo "<script>window.location.href = 'login.php';</script>";
            exit();
        } else {
            echo "Email not found.";
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

mysqli_close($conn);
?>

<html>
    <head>
        <title>Forgot Password</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <style>
        
            .logincard{
                background: linear-gradient(315deg,rgb(0, 0, 0),rgb(154, 9, 9));
                width:350px;
                margin-top: 160px;
                box-shadow: 5px 5px 5px rgb(30,30,30);
                padding:25px;
                border-radius:15px;
                height:430px;
                font-family:playfair;
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
                <form action="forgot.php" method="POST" onsubmit="return pass()">
                    <h2 class="text-white  text-center mb-4">Forgot Password</h2>
                    <input type="text"class="form-control mb-2"  id="mail" name="mail" placeholder="Email">
                    <p class="error" id="mail_error"></p>
                    <input type="password" class="form-control mb-2 mt-4"name="pass1" id="passw1" placeholder="Enter new Password">
                    <p class="error" id="password_error"></p>
                    <input type="password" class="form-control mb-2 mt-4"name="pass2" id="passw2" placeholder="Confirm Password">
                    <p class="error" id="password_error2"></p>
                    <div class="text-center mb-3">
                    <button type="submit" class="btn btn-light text-danger login-form-button mt-2">Reset Password</button><br>
                    </div> 
            </div>
        </div>
        <script>
            function pass(){
                var email=document.getElementById("mail").value;
                var password=document.getElementById("passw1").value;
                var cpassword=document.getElementById("passw2").value;
                var valid=true;
                if(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(email)){
                    document.getElementById("mail_error").innerText="";
                }
                else if(email===""){
                    document.getElementById("mail_error").innerText="*This field is required.";
                    valid=false;
                }
                else {
                    document.getElementById("mail_error").innerText="*Enter valid email id.";
                    valid=false;
                }

                if(password.length>=8 && /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$/.test(password)){
                    document.getElementById("password_error").innerText="";
                } 
                else if(password===""){
                    document.getElementById("password_error").innerText="*This field is required.";
                    valid=false;
                }else{
                    document.getElementById("password_error").innerText="*The password should contain at least one uppercase letter, lowercase letter, digit, and a special character.";
                    valid=false;
                }

                if(cpassword===password){
                    document.getElementById("password_error2").innerText="";
                } 
                else if(cpassword===""){
                    document.getElementById("password_error2").innerText="*This field is required.";
                    valid=false;
                }else{
                    document.getElementById("password_error2").innerText="*The password should be same as entered before.";
                    valid=false;
                }

                return valid;
                
            }
        </script>
    </body>
</html>