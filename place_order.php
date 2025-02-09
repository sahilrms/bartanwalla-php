<?php
require_once 'includes/conn.php';
$conn = $pdo->open();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Retrieve form data
        $full_name = $_POST['full_name'];
        $phone = $_POST['phone'];
        $address = $_POST['address1'] . ", " . $_POST['address2'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $pin = $_POST['pincode'];
        $cart_items = json_encode($_POST['cart_items']); // Convert cart items array to JSON
        $price = isset($_POST['items_total']) ? (float) $_POST['items_total'] : 0;
        $final_price = isset($_POST['grand_total']) ? (float) $_POST['grand_total'] : 0;
        $discount = isset($_POST['coupon_discount']) ? (float) $_POST['coupon_discount'] : 0;
        $shipping = isset($_POST['shipping']) ? (float) $_POST['shipping'] : 0;
        $other_discount = isset($_POST['other_discount']) ? (float) $_POST['other_discount'] : 0;
        // exit; // Remove this line to proceed with insertion after viewing
        
        if ($price == 0 || $final_price == 0) {
            echo <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Invalid Order</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f8d7da; color: #721c24; 
                        display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                    .container { background: white; padding: 30px; border-radius: 10px; 
                        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); text-align: center; max-width: 500px; }
                    h1 { font-size: 24px; margin-bottom: 10px; }
                    p { font-size: 18px; margin-bottom: 20px; }
                    .btn { display: inline-block; padding: 10px 20px; font-size: 16px; font-weight: bold;
                        color: #fff; background-color: #dc3545; border: none; border-radius: 5px; text-decoration: none;
                        cursor: pointer; transition: background 0.3s; }
                    .btn:hover { background-color: #c82333; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Invalid Order</h1>
                    <p>Oops! Your order cannot be processed because the price or final price is zero.</p>
                    <a href="cart_view.php" class="btn">Go Back to Cart</a>
                </div>
            </body>
            </html>
            HTML;
            exit;
        }
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO orders (full_name, phone, address, city, state, pin, cart_items, price, discount, final_price,shipping,other_discount,user_id,payment_status) 
                               VALUES (:full_name, :phone, :address, :city, :state, :pin, :cart_items, :price, :discount, :final_price, :shipping, :other_discount, :user_id, :payment_status)");
        $stmt->execute([
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'pin' => $pin,
            'cart_items' => $cart_items,
            'price' => $price,
            'discount' => $discount,
            'final_price' => $final_price,
            'shipping'=> $shipping,
            'other_discount'=> $other_discount,
            'user_id'=> $user['id'],
            'payment_status'=> 'Pending'
        ]);

        // Redirect to confirmation page
        header("Location: order_success.php?order_id=" . $conn->lastInsertId());
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
