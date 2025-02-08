<?php
include 'includes/session.php';
$conn = $pdo->open();

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 bg-white text-center" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="text-success fw-bold">Thank you, <?= htmlspecialchars($order['full_name']) ?>! 🎉</h2>
                <p class="fs-5 text-muted">Your order <strong>#<?= $order['order_id'] ?></strong> has been created successfully.</p>

                <hr>

                <h4 class="fw-bold text-primary">Final Amount to be Paid:</h4>
                <p class="fs-4 text-danger fw-bold">₹<?= number_format($order['final_price'], 2) ?></p>

                <h5 class="fw-bold text-secondary">Shipping Address:</h5>
                <p class="fs-6 text-muted">
                    <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, 
                    <?= htmlspecialchars($order['state']) ?> - <?= htmlspecialchars($order['pin']) ?>
                </p>

                <a href="payment_gateway.php?order_id=<?= $order['order_id'] ?>" class="btn btn-success btn-lg w-100 mt-3">
                    <i class="fas fa-credit-card"></i> Pay Now
                </a>
                <a href="payment_gateway.php?order_id=<?= $order['order_id'] ?>" class="btn btn-success btn-lg w-100 mt-3">
                    <i class="fas fa-credit-card"></i> Pay latter
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
