<?php
include 'includes/session.php';
$conn = $pdo->open();

if (!isset($_GET['payment_id']) || !isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$payment_id = $_GET['payment_id'];
$order_id = $_GET['order_id'];

// Update order with payment details
$stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid', payment_id = ? WHERE order_id = ?");
$stmt->execute([$payment_id, $order_id]);

// Fetch user ID for redirection
$stmt = $conn->prepare("SELECT user_id FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
$user_id = $order['user_id'];

// Redirect to order success page
header("Location: ordered_successfully.php?order_id=$order_id&user_id=$user_id");
exit();
