<head>
    <style>
        /* Main Review Container */
        .reviewcontainer {
            width: max-content;

            height: 40rem !important;
            /* Set a specific height */
            overflow-y: auto;
            /* Enable vertical scrolling if content overflows */
            border: 1px solid #ccc;
            /* Optional: Add a border to visually separate the container */
            padding: 10px;
            /* Optional: Add some padding for better spacing inside */
            background-color: #f9f9f9;
            /* Optional: Light background color */
            margin-bottom: 10px;

        }

        /* Card Styling for Each Review */
        .card {
            background-color: #f1f3f5;
            /* White background for each review */
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            /* Soft border */
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            /* Soft shadow effect */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* Hover transition */
        }

        /* Hover Effect on Cards */
        .card:hover {
            background-color: #ffffff;
            transform: translateY(-5px);
            /* Slight lift when hovered */
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
            /* Stronger shadow */
        }

        /* Review Title */
        .card-title {
            font-size: 14px !important;
            font-weight: 600;
            color: #333;
            /* Dark color for the title */
            margin-bottom: 10px;
        }

        /* Review Text */
        .card-text {
            font-size: 1rem;
            color: #555;
            /* Slightly muted color for better readability */
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 12px;
        }

        /* Username and Rating */
        .card-body .d-flex {
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .card-body strong {
            font-size: 1.1rem;
            color: #333;
            /* Darker text for the username */
        }

        .card-body .badge {
            background-color: #f39c12;
            color: white;
            font-weight: bold;
            padding: 5px 15px;
            border-radius: 30px;
            /* Rounded badge */
            font-size: 1rem;
        }

        /* Review Footer (Date) */
        .card-footer {
            font-size: 10px;
            text-align: right;
            color: #888;
            /* Muted color for the date */
        }

        /* No Reviews Message */
        .alert-info {
            background-color: #e7f3fe;
            color: #3178c6;
            font-weight: bold;
            padding: 1rem;
            border-radius: 10px;
        }
    </style>

</head>
<?php
// Fetch the product_id using the slug from the products table
$stmt = $conn->prepare("SELECT id FROM products WHERE slug = :slug");
$stmt->execute(['slug' => $slug]);
$product = $stmt->fetch();

if ($product) {
    $product_id = $product['id'];

    // Fetch reviews for the product
    $stmt = $conn->prepare("SELECT reviews.*, users.firstname AS username FROM reviews 
                            LEFT JOIN users ON reviews.user_id = users.id 
                            WHERE reviews.product_id = :product_id");
    $stmt->execute(['product_id' => $product_id]);
    $reviews = $stmt->fetchAll();

} else {
    // Handle the case where the product was not found
    echo "Product not found.";
}
?>

<!-- Display reviews -->
<div class="reviewcontainer">

   
    <?php if (empty($reviews)): ?>
        <div class="alert alert-info text-center" role="alert">
            No reviews yet.
        </div>
    <?php else: ?>
        <div class="container mt-4">
            <div class="row">
                <?php foreach ($reviews as $review): ?>
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($review['review_heading']); ?></h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                    <span class="badge bg-warning text-dark"><?php echo $review['rating']; ?>/5</span>
                                </div>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                            </div>
                            <div class="card-footer text-end">
                                Reviewed on <?php echo date("F j, Y", strtotime($review['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>