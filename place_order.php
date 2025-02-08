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
        $price = $_POST['items_total'];
        $discount = $_POST['coupon_discount'];
        $shipping = $_POST['shipping'];
        $other_discount = $_POST['other_discount'];

        // Calculate final price
        $final_price = $price - $discount - $other_discount + $shipping;

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO orders (full_name, phone, address, city, state, pin, cart_items, price, discount, final_price) 
                               VALUES (:full_name, :phone, :address, :city, :state, :pin, :cart_items, :price, :discount, :final_price)");
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
            'final_price' => $final_price
        ]);

        // Redirect to confirmation page
        header("Location: order_success.php?order_id=" . $conn->lastInsertId());
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
