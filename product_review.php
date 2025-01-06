<?php
$product_id = $_GET['product']; // Get product ID from URL parameter
$user_id =$_SESSION['user']; // Get the logged-in user ID from the session (assumes user is logged in)

try {
    // Fetch product details to display on the review page
    $stmt = $conn->prepare("SELECT * FROM products WHERE slug = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch();
    
} catch (PDOException $e) {
    echo "There was an error: " . $e->getMessage();
}
?>

<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">
        <div class="content-wrapper">
            <div class="container">
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-sm-9">
                            <h2>Write a Review for <?php echo $product['name']; ?></h2>
                            
                            <!-- Review Form -->
                            <form action="submit_review.php" method="POST">
                                <div class="form-group">
                                    <label for="review_heading">Review Heading</label>
                                    <input type="text" class="form-control" id="review_heading" name="review_heading" required>
                                </div>

                                <div class="form-group">
                                    <label for="review_text">Review Text</label>
                                    <textarea class="form-control" id="review_text" name="review_text" rows="5" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="rating">Rating</label>
                                    <select class="form-control" id="rating" name="rating" required>
                                        <option value="">Select Rating</option>
                                        <option value="1">1 - Poor</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="3">3 - Good</option>
                                        <option value="4">4 - Very Good</option>
                                        <option value="5">5 - Excellent</option>
                                    </select>
                                </div>

                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>

       
    </div>

    
</body>
</html>