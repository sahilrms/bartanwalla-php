<?php
include 'includes/session.php';

if(isset($_POST['coupon_code'])) {
    // Debugging: Log the coupon_code received from the POST request
    // //error_log("Received coupon_code: " . $_POST['coupon_code']);

    // Open database connection
    $conn = $pdo->open();

    // Sanitize the input coupon code
    $coupon_code = $_POST['coupon_code'];

    // Debugging: Check if the coupon code is correctly assigned
    //error_log("Sanitized coupon_code: " . $coupon_code);

    // Prepare SQL query to fetch coupon details
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = :code AND expiration_date > NOW()");
    $stmt->execute([':code' => $coupon_code]);

    // Debugging: Check the SQL query execution and the fetched coupon
    if($stmt->rowCount() > 0) {
        $coupon = $stmt->fetch();
        //error_log("Coupon found: " . print_r($coupon, true));  // Debugging: Print the coupon data
    } else {
        //error_log("No coupon found for code: " . $coupon_code);
    }

    if($coupon) {
        // Coupon found and valid
        $discount_value = $coupon['discount_value'];
        
        // Debugging: Log the discount applied
        //error_log("Discount applied: " . $discount_value);

        // Prepare the response
        $response = [
            'success' => true,
            'discount' => $discount_value,
            'discount_type' => $coupon['discount_type']
        ];
    } else {
        // Invalid or expired coupon
        $response = [
            'success' => false,
            'error_message' => 'Invalid or expired coupon code.'
        ];
    }
    
    // Debugging: Output the final response
    //error_log("Response: " . json_encode($response));

    // Return the response as JSON
    echo json_encode($response);
}
?>
