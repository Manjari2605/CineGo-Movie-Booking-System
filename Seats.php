<?php 
$servername="localhost"; 
$username="root"; 
$password=""; 
$database="movie_booking"; 
$conn=new mysqli($servername,$username,$password,$database); 
if($conn->connect_error){ 
    die("Connection failed" . $conn->connect_error); 
} 
$seats_booked=[]; 
$result=$conn->query("SELECT seat_number FROM seats WHERE status='booked'"); 
while($row=$result->fetch_assoc()){ 
    $seats_booked[]=$row['seat_number']; 
} 
$conn->close(); 
?> 
<html> 
    <head> 
        <title>seat booking</title> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
rel="stylesheet" 
integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwI
 H" crossorigin="anonymous"> 
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
crossorigin="anonymous"></script> 
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
                width:35px; 
                height:35px; 
                margin:5px; 
                border-radius: 10px; 
                text-align:center; 
                background:rgb(202, 202, 202); 
                color:white; 
                display:flex;                
            } 
            .seat-booked { 
            background:rgb(76, 76, 76); 
            cursor: not-allowed; 
            } 
            h3{ 
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
                              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" 
aria-label="Toggle navigation"> 
                        <span class="navbar-toggler-icon"></span> 
                    </button> 
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup"> 
                        <div class="navbar-nav ms-auto"> 
                            <a class="nav-link active home text-white ms-auto" 
href="homepage.html">Home</a> 
                            <div class="dropdown"> 
                                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" 
id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"> 
                                    <img 
src="https://www.vhv.rs/dpng/d/436-4363443_view-user-icon-png-font-awesome-user-circle.png" 
alt="Profile" width="30" height="30" class="rounded-circle ms-4"> 
                                </a> 
                                <ul class="dropdown-menu dropdown-menu-end" 
aria-labelledby="profileDropdown"> 
                                    <li><a class="dropdown-item" href="#">Edit Profile</a></li> 
                                    <li><a class="dropdown-item" href="#">Booking History</a></li> 
                                    <li><hr class="dropdown-divider"></li> 
                                    <li><a class="dropdown-item text-danger" href="login.html">Log Out</a></li> 
                                </ul> 
                            </div> 
             
                        </div> 
                    </div> 
                </div> 
            </nav> 
        <h3 class="text-danger">Screen this way</h3> 
        <div class="screen "></div> 
        <div id="seat_container" class="mt-5"></div> 
        <form id="seatForm" method="POST" action="pay.html"> 
            <input type="hidden" name="seats" id="seatInput"> 
        </form> 
        <div class="d-flex justify-content-center mt-3"> 
            <div class="d-flex align-items-center me-5 text-light"> 
                <div class="seat" style="background:rgb(202, 202, 202);"></div> Available 
            </div> 
            <div class="d-flex align-items-center me-5 text-light"> 
                <div class="seat" style="background:rgb(31, 154, 9);"></div> Selected 
            </div> 
            <div class="d-flex align-items-center text-light"> 
                <div class="seat" style="background:rgb(76, 76, 76);"></div> Sold 
            </div> 
        </div> 
        <center><button type="button" onclick="bookseats()" class="btn btn-danger mt-4">Book 
Now</button></center> 
        <script> 
            var seats_booked=<?php echo json_encode($seats_booked); ?>; 
            var rows=8; 
            var cols=12; 
            var seat=""; 
            var label="ABCDEFGH".split(""); 
            for(var i=rows-1;i>=0;i--){ 
                seat+='<div class="row">'; 
                seat+='<div class="seat fw-bold" style="background:None; color:rgb(190, 9, 9)">' + label[i] + 
'</div>'; 
                    for(var j=1;j<=cols;j++){ 
                        var seat_num=label[i]+j; 
                        var booked=false; 
                        for(var k=0;k<seats_booked.length;k++){ 
                            if(seats_booked[k]==seat_num){ 
                                booked=true; 
                                break; 
                            } 
                        } 
                        seat+='<div class="seat'; 
                            if(booked){ 
                                seat+=' booked'; 
                            } else{ 
                                seat+='" onclick="select_seat(this)"'; 
                            } 
                            seat+='" data-id="'+seat_num+'"></div>';         
                    } 
                seat += '</div>';     
            } 
            seat+='<div class="row">'; 
            seat+='<div class="seat" style="visibility:hidden;"></div>'; 
            for(var j=1;j<=cols;j++){ 
                seat+='<div class="seat fw-bold" style="background:none;color:rgb(190, 9, 9);">'+j+'</div>'; 
            } 
            document.getElementById("seat_container").innerHTML=seat; 
            function select_seat(seat){ 
                if(seat.style.background === "rgb(31, 154, 9)"){ 
                    seat.style.background = "rgb(202, 202, 202)"; 
                } else { 
                    seat.style.background = "rgb(31, 154, 9)"; 
                } 
            } 
            function bookseats(){ 
                var seats=document.getElementsByClassName("seat"); 
                var selected_seats=""; 
                for(var i=0;i<seats.length;i++){ 
                    if(seats[i].style.background==="rgb(31, 154, 9)"){ 
                        selected_seats+=seats[i].getAttribute("data-id")+","; 
                    } 
                } 
                if(selected_seats===""){ 
                    alert("Atleast one seat should be selected."); 
                    return; 
                } 
                document.getElementById("seatInput").value = selected_seats; 
                document.getElementById("seatForm").submit(); 
            } 
             
        </script> 
    </body> 
</html>
