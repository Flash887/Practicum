<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Thank You for Your Order!</h1>
        <p>Your order has been placed successfully.
And will arrive to you adress it`s new system that can dilivery to you adrress by using you IP.
But also we will contact to your email you use in registration to confirm you adress it will take less than 1 minutes.</p>
        <a href="index.php" class="btn">Go to Home</a>
    </div>
</body>
</html>
