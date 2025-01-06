<?php
include 'includes/session.php'; // Include session to get user info

$conn = $pdo->open();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $slug = $_POST['product_id'];
    $user_id = $_SESSION['user'];
    $review_heading = $_POST['review_heading'];
    $review_text = $_POST['review_text'];
    $rating = $_POST['rating'];

    try {
        $stmt1 = $conn->prepare("SELECT id FROM products WHERE slug = :slug LIMIT 1");
        $stmt1->execute([':slug' => $slug]);
        $product = $stmt1->fetch(PDO::FETCH_ASSOC);

        $product_id = $product['id'];
        print_r($product);
        // Insert the review into the reviews table
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, review_heading, review_text, rating) 
                                VALUES (:product_id, :user_id, :review_heading, :review_text, :rating)");
        $stmt->execute([
            ':product_id' => $product_id,
            ':user_id' => $user_id,
            ':review_heading' => $review_heading,
            ':review_text' => $review_text,
            ':rating' => $rating
        ]);

        // Redirect to the product page or a success page
        header("Location: product.php?product=" . $slug);
        exit;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$pdo->close();
