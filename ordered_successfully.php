<?php
include 'includes/session.php';
$conn = $pdo->open();

if (!isset($_GET['order_id']) || !isset($_GET['user_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_GET['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Clear the user's cart
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
?>

<?php include 'includes/header.php'; ?>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 bg-white text-center" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="text-success fw-bold">Payment Successful! ✅</h2>
                <p class="fs-5 text-muted">Your order <strong>#<?= $order['order_id'] ?></strong> has been processed.
                </p>

                <h4 class="fw-bold text-primary">Amount Paid:</h4>
                <p class="fs-4 text-success fw-bold">₹<?= number_format($order['final_price'], 2) ?></p>

                <h5 class="fw-bold text-secondary">Transaction ID:</h5>
                <p class="fs-6 text-muted"><?= htmlspecialchars($order['payment_id']) ?></p>

                <a href="order_details.php?order_id=<?= $order['order_id'] ?>"
                    class="btn btn-primary btn-lg w-100 mt-3">
                    <i class="fas fa-box"></i> View Order Details
                </a>
            </div>
        </div>
    </div>
    <!-- Clear Local Storage -->
    <script>
        localStorage.clear(); // Clears all local storage data
        sessionStorage.clear(); // Clears session storage too, if used
        console.log("Local storage cleared after order success.");
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>