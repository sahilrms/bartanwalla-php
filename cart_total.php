<?php
include 'includes/session.php';

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    $conn = $pdo->open(); // Open database connection

    // Retrieve cart items for the logged-in user
    $stmt = $conn->prepare("SELECT * FROM cart LEFT JOIN products on products.id=cart.product_id WHERE user_id=:user_id");
    $stmt->execute(['user_id' => $user['id']]);

    // Initialize variables for calculations
    $items_total = 0;       // Total cost of all items in the cart
    $coupon_discount = 0;   // Discount applied from coupon
    $shipping_charges = 50; // Flat shipping charge
    $other_discounts = 0;   // Placeholder for additional discounts
    $grand_total = 0;       // Final total after all calculations

    // Calculate the total cost of items in the cart
    foreach ($stmt as $row) {
        $subtotal = $row['price'] * $row['quantity']; // Calculate item cost
        $items_total += $subtotal;                   // Add to total
    }

    /**
     * Check if a valid coupon code is applied
     */
   
    if (isset($_POST['coupon_code']) && !empty($_POST['coupon_code'])) {
        // $coupon_code = $_SESSION['coupon_code'];
        $coupon_code = $_POST['coupon_code'];

        // Debug: Log the received coupon code
        error_log("Coupon code received: $coupon_code");

        // Fetch coupon details from the database
        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = :code AND expiration_date > NOW()");
        $stmt->execute([':code' => $coupon_code]);
        $coupon = $stmt->fetch();

        if ($coupon) {
            // Apply percentage discount if the coupon type is percentage
            if ($coupon['discount_type'] == 'percentage') {
                $coupon_discount = ($items_total * $coupon['discount_value']) / 100;
            } else {
                // Apply fixed discount
                $coupon_discount = $coupon['discount_value'];
            }

            // Update total table with applied coupon discount
            // $update_stmt = $conn->prepare(
            //     "INSERT INTO total (user_id, items_total, coupon_code, discount, grand_total) 
            //      VALUES (:user_id, :items_total, :coupon_code, :discount, :grand_total)
            //      ON DUPLICATE KEY UPDATE 
            //         items_total = :items_total, 
            //         coupon_code = :coupon_code, 
            //         discount = :discount, 
            //         grand_total = :grand_total"
            // );
            // $update_stmt->execute([
            //     ':user_id' => $user['id'],
            //     ':items_total' => $items_total,
            //     ':coupon_code' => $coupon_code,
            //     ':discount' => $coupon_discount,
            //     ':grand_total' => $items_total - $coupon_discount + $shipping_charges - $other_discounts
            // ]);
        } else {
            // Debug: Log if coupon is invalid
            error_log("Invalid coupon code: $coupon_code");
        }
    } else {
        // Debug: Log if no coupon code is set
        error_log("No coupon code found in session.");
    }

    // Calculate the grand total
    $grand_total = $items_total - $coupon_discount + $shipping_charges - $other_discounts;

    // Close database connection
    $pdo->close();

    // Return response as JSON
    echo json_encode([
        'success' => true,
        'items_total' => $items_total,
        'coupon_discount' => $coupon_discount,
        'shipping' => $shipping_charges,
        'other_discount' => $other_discounts,
        'grand_total' => $grand_total
    ]);
} else {
    // If the user is not logged in, return an error response
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in.'
    ]);
}
?>
