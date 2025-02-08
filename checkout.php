use Razorpay\Api\Api;

<?php
require('razorpay-php/Razorpay.php');

$api_key = "YOUR_API_KEY";
$api_secret = "YOUR_API_SECRET";

$api = new Api($api_key, $api_secret);

$orderData = [
    'receipt'         => 3456,
    'amount'          => 50000, // 50000 paise = 500 INR
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$order_id = $razorpayOrder['id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <form action="verify.php" method="POST">
        <script
            src="https://checkout.razorpay.com/v1/checkout.js"
            data-key="<?php echo $api_key; ?>"
            data-amount="50000"
            data-currency="INR"
            data-order_id="<?php echo $order_id; ?>"
            data-buttontext="Pay with Razorpay"
            data-name="Your Company Name"
            data-description="Test Transaction"
            data-image="https://your-logo-url.com/logo.png"
            data-prefill.name="John Doe"
            data-prefill.email="john.doe@example.com"
            data-theme.color="#F37254"
        ></script>
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
    </form>
</body>
</html>