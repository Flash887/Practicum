<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_cart) > 0) {
        $total_price = 0;
        while ($cart_item = mysqli_fetch_assoc($select_cart)) {
            $total_price += $cart_item['price'] * $cart_item['quantity'];
        }

        
        mysqli_query($conn, "INSERT INTO `orders` (user_id, total_price) VALUES ('$user_id', '$total_price')") or die('query failed');
        $order_id = mysqli_insert_id($conn);

        
        mysqli_data_seek($select_cart, 0);
        while ($cart_item = mysqli_fetch_assoc($select_cart)) {
            $product_name = $cart_item['name'];
            $product_price = $cart_item['price'];
            $product_quantity = $cart_item['quantity'];
            mysqli_query($conn, "INSERT INTO `order_items` (order_id, product_name, product_price, product_quantity) VALUES ('$order_id', '$product_name', '$product_price', '$product_quantity')") or die('query failed');
        }

        
        mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');

        
        header('location:thankyou.php');
        exit;
    } else {
        header('location:index.php');
        exit;
    }
} else {
    header('location:index.php');
    exit;
}
?>
