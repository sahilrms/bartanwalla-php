<?php
echo 'hello';
// Include necessary files
include './includes/session.php';
// Check if the form is submitted
if (isset($_POST['edit_name'])) {

    // Get the data from the form
    $id = $_POST['product_id']; // Coupon ID
    $coupon_code = $_POST['edit_name']; // Coupon code
    $discount_value = $_POST['edit_price']; // Discount value
    $expiration_date = $_POST['edit_expiration']; // Expiration date
    $min_cart_value = $_POST['edit_min_cart_value']; // Minimum cart value
    $usage_limit = $_POST['edit_usage_limit']; // Usage limit
    $applicable_users = $_POST['edit_applicable_users']; // Applicable users (can be empty or a list)

    // Validate input (optional but recommended)
    if (empty($coupon_code) || empty($discount_value) || empty($expiration_date)) {
        $_SESSION['error'] = "Please fill all required fields!";
        header('location: coupons.php'); // Redirect back to the coupons page
        exit();
    }

    try {
        // Prepare SQL statement to update the coupon record
        $stmt = $conn->prepare("UPDATE coupons SET 
            code = :code,
            discount_value = :discount_value,
            expiration_date = :expiration_date,
            min_cart_value = :min_cart_value,
            usage_limit = :usage_limit,
            applicable_to_users = :applicable_to_users
            WHERE id = :id");

        // Bind the data to the SQL query parameters
        $stmt->bindParam(':code', var: $coupon_code);
        $stmt->bindParam(':discount_value', var: $discount_value);
        $stmt->bindParam(':expiration_date', var: $expiration_date);
        $stmt->bindParam(':min_cart_value', var: $min_cart_value);
        $stmt->bindParam(':usage_limit', var: $usage_limit);
        $stmt->bindParam(':applicable_to_users', var: $applicable_users);
        $stmt->bindParam(':id', var: $id);
      
        // Execute the update query
        if ($stmt->execute()) {
            $_SESSION['success'] = "Coupon updated successfully!";
           
        } else {
            $_SESSION['error'] = "Failed to update coupon!";
        }

        // Redirect back to the coupons page after update
        header('location: coupons.php');
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header('location: coupons.php');
        exit();
    }
}
?>
