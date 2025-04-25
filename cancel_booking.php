<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $user_id = $_SESSION['user_id'];
    $booking_id = intval($_POST['booking_id']);

    $conn = mysqli_connect("localhost", "root", "", "MovieDB");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT amount_paid, seats, show_date, show_time, use_free_ticket, theater 
              FROM bookings 
              WHERE id = $booking_id AND user_id = $user_id AND status = 'active'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();

        $show_datetime = strtotime($booking['show_date'] . ' ' . $booking['show_time']);
        $current_time = time();

        error_log("Show DateTime: " . date('Y-m-d H:i:s', $show_datetime));
        error_log("Current Time: " . date('Y-m-d H:i:s', $current_time));

        if ($current_time >= $show_datetime) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Cancellation not allowed. The movie\'s show time has already started or completed.'
            ];
            error_log("Alert set: " . print_r($_SESSION['alert'], true));
            header("Location: booking_history.php");
            exit();
        }

        $theater_name = $booking['theater'];
        $show_query = "SELECT show_id FROM shows 
                       JOIN theaters ON shows.theater_id = theaters.theater_id 
                       WHERE shows.show_date = '{$booking['show_date']}' 
                       AND shows.show_time = '{$booking['show_time']}' 
                       AND theaters.theater_name = '$theater_name'";
        $show_result = $conn->query($show_query);

        if ($show_result && $show_result->num_rows > 0) {
            $show_row = $show_result->fetch_assoc();
            $show_id = $show_row['show_id'];
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Show not found. Please check the show date, time, and theater.'
            ];
            error_log("Alert set: " . print_r($_SESSION['alert'], true));
            header("Location: booking_history.php");
            exit();
        }

        $refund_amount = $booking['amount_paid'];
        $seats = explode(',', $booking['seats']);
        $use_free_ticket = intval($booking['use_free_ticket']);

        

        if ($refund_amount > 0) {
            $update_wallet = "INSERT INTO wallet (user_id, balance) VALUES ($user_id, $refund_amount)
                              ON DUPLICATE KEY UPDATE balance = balance + $refund_amount";
            if (!$conn->query($update_wallet)) {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => 'Error updating wallet: ' . $conn->error
                ];
                header("Location: booking_history.php");
                exit();
            }
        }

        $update_booking = "UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id";
        if (!$conn->query($update_booking)) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Error updating booking status: ' . $conn->error
            ];
            header("Location: booking_history.php");
            exit();
        }

        foreach ($seats as $seat) {
            $seat = trim($seat); 
            $update_seat = "UPDATE seat 
                            SET status = 'available' 
                            WHERE seat_number = '$seat' 
                            AND show_id = $show_id";
            if (!$conn->query($update_seat)) {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => 'Error freeing seats: ' . $conn->error
                ];
                header("Location: booking_history.php");
                exit();
            }
        }
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Money has been refunded to your wallet. Please check your wallet.'
        ];
        header("Location: booking_history.php");
        exit();

    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Booking not found or already canceled.'
        ];
        header("Location: booking_history.php");
        exit();
    }

    $conn->close();
    exit();
}
?>