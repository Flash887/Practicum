<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($select_cart) > 0) {
        $message[] = 'Product already added to cart!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
        $message[] = 'Product added to cart!';
    }
}

if (isset($_POST['update_cart'])) {
    $update_quantity = $_POST['cart_quantity'];
    $update_id = $_POST['cart_id'];
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
    $message[] = 'Cart quantity updated successfully!';
}

if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
    header('location:index.php');
}

if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    header('location:index.php');
}

?>

<script>
    // Save scroll position
    window.onbeforeunload = function() {
        var scrollPos = window.scrollY;
        localStorage.setItem('scrollPos', scrollPos);
    };

    // Restore scroll position
    window.onload = function() {
        var scrollPos = localStorage.getItem('scrollPos');
        if (scrollPos) {
            window.scrollTo({
                top: scrollPos,
                behavior: 'smooth'
            });
            localStorage.removeItem('scrollPos');
        }
    };
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/style.css">
<div class="search-container">
    <form action="" method="get">
        
     
    </form>
    <?php if (isset($_GET['search'])) { ?>
        <a href="index.php" class="btn">Повернутися до всіх товарів</a>
    <?php } ?>
</div>

    <style>
        .logout-btn-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .message {
            background: #ffcc00;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 80%;
            margin-right: 10px;
        }
        .search-container input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #ffcc00;
            cursor: pointer;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                let messages = document.querySelectorAll('.message');
                messages.forEach(function(message) {
                    message.style.display = 'none';
                });
            }, 1500);
        });
    </script>
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '<div class="message">' . $message . '</div>';
    }
}
?>

<div class="container">

<div class="logout-btn-container">
    <a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('Are you sure you want to logout?');" class="delete-btn">Logout</a>
</div>

<div class="search-container">
    <form action="" method="get">
        <input type="text" name="search" placeholder="Search for products..." value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>">
        <input type="submit" value="Search">
    </form>
</div>

<div class="products">

    <h1 class="heading">Latest Products</h1>

    <div class="box-container">

    <?php
    if (isset($_GET['search'])) {
        $search = mysqli_real_escape_string($conn, $_GET['search']);
        $select_product = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%$search%'") or die('query failed');
    } else {
        $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
    }

    if (mysqli_num_rows($select_product) > 0) {
        while ($fetch_product = mysqli_fetch_assoc($select_product)) {
    ?>
        <form method="post" class="box" action="">
            <img src="images/<?php echo $fetch_product['image']; ?>" alt="">
            <div class="name"><?php echo $fetch_product['name']; ?></div>
            <div class="price">$<?php echo $fetch_product['price']; ?>/-</div>
            <input type="number" min="1" name="product_quantity" value="1">
            <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
            <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
        </form>
    <?php
        }
    } else {
        echo '<p class="empty">No products found!</p>';
    }
    ?>

    </div>

</div>

<div class="shopping-cart">

    <h1 class="heading">Shopping Cart</h1>

    <table>
        <thead>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Action</th>
        </thead>
        <tbody>
        <?php
        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        $grand_total = 0;
        if (mysqli_num_rows($cart_query) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
        ?>
            <tr>
                <td><img src="images/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
                <td><?php echo $fetch_cart['name']; ?></td>
                <td>$<?php echo $fetch_cart['price']; ?>/-</td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                        <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                        <input type="submit" name="update_cart" value="Update" class="option-btn">
                    </form>
                </td>
                <td>$<?php echo $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</td>
                <td><a href="index.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn" onclick="return confirm('Remove item from cart?');">Remove</a></td>
            </tr>
        <?php
                $grand_total += $sub_total;
            }
        } else {
            echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">No item added</td></tr>';
        }
        ?>
        <tr class="table-bottom">
            <td colspan="4">Grand Total:</td>
            <td>$<?php echo $grand_total; ?>/-</td>
            <td><a href="index.php?delete_all" onclick="return confirm('Delete all from cart?');" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Delete all</a></td>
        </tr>
    </tbody>
    </table>

    <div class="cart-btn">  
        <form action="checkout.php" method="post">
            <input type="submit" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>" value="Proceed to Checkout" <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>>
        </form>
    </div>

</div>

</div>

</body>
</html>
