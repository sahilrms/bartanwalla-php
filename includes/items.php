<head>
    <style>
        /* Styling for the Navbar */
        .navbar {
            background-color: #dc3545; /* Red background for navbar */
            padding: 10px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 5px;
            display: block;
            font-weight: bold;
            border-radius: 4px;
        }

        .navbar a:hover {
            background-color: #c82333; /* Darker red on hover */
        }

        /* Active category styling */
        .navbar .active {
            background-color: #ff6347; /* Tomato red for active category */
        }

        /* Styling for the content display */
        #clicked_item {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        .category-content {
            font-size: 16px;
        }

        /* Layout for the product grid */
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .product-grid .product-item {
            width: 30%;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .product-item img {
            width: 100%;
            height: 230px;
            object-fit: cover;
        }

        .product-item .box-footer {
            padding: 10px;
            background-color: #f1f1f1;
            text-align: center;
        }

        .product-item .box-footer b {
            color: #dc3545;
            font-size: 16px;
        }

        /* Styling for the sort dropdown */
        .sort-dropdown {
            margin: 20px 0;
            padding: 10px;
            border-radius: 4px;
            background-color: #f8f9fa;
            margin-left: auto !important;
            display: flex !important;
            justify-content: end;
            align-items: center !important;
        }

        .sort-heading {
            margin-right: 5px;
            margin-top: 5px;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 992px) {
            .product-grid .product-item {
                width: 45%; /* Two items per row on medium screens */
            }

            .sort-dropdown {
                justify-content: center; /* Center the sort dropdown on smaller screens */
            }
        }

        @media (max-width: 576px) {
            .product-grid .product-item {
                width: 100%; /* One item per row on small screens */
            }

            .navbar a {
                padding: 10px;
                margin: 5px 0;
                width: 100%; /* Stack navbar items vertically */
                text-align: center;
            }

            .sort-dropdown {
                width: 100%; /* Make sort dropdown full width */
                justify-content: center;
            }

            .sort-heading {
                margin-right: 10px;
            }
        }

        /* Additional CSS for image gallery */
        .product-item .image-gallery img {
            display: none; /* Hide all images initially */
        }

        .product-item .image-gallery img:first-child {
            display: block; /* Show the first image initially */
        }
    </style>
</head>

<body>

<div>
    <!-- Navbar for Categories -->
    <div class="navbar" role="menu">
        <?php
        // Database connection
        $conn = $pdo->open();

        // Fetch categories from the database
        $stmt = $conn->prepare("SELECT * FROM category");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Default category if none is selected
        $currentCategory = isset($_GET['category']) ? $_GET['category'] : (isset($categories[0]['cat_slug']) ? $categories[0]['cat_slug'] : '');

        // Check if selected category has products
        $hasProducts = false;
        foreach ($categories as $row) {
            // Check if the current category has products
            if ($row['cat_slug'] == $currentCategory) {
                // Check if this category has products
                $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = :catid");
                $stmt->execute(['catid' => $row['id']]);
                $productCount = $stmt->fetchColumn();

                // Set the flag if there are products in this category
                if ($productCount > 0) {
                    $hasProducts = true;
                }
            }
        }

        // Display categories as navbar links
        foreach ($categories as $row) {
            // Only add 'active' class to categories with products
            $activeClass = ($row['cat_slug'] == $currentCategory && $hasProducts) ? 'active' : ''; 
            echo "
            <a href='?category=" . $row['cat_slug'] . "' class='$activeClass'>" . $row['name'] . "</a>
        ";
        }

        $pdo->close();
        ?>
    </div>

    <!-- Content Section below the navbar -->
    <?php
    // Display content based on the selected category
    if ($currentCategory) {
        try {
            $stmt = $conn->prepare("SELECT * FROM category WHERE cat_slug = :slug");
            $stmt->execute(['slug' => $currentCategory]);
            $cat = $stmt->fetch(); // Check if the category exists
            if ($cat) {
                $catid = $cat['id']; // Only set catid if category is found
            } else {
                // Category not found
                echo "<p>Category not found. Please select a valid category.</p>";
                exit; // Stop further processing
            }
        } catch (PDOException $e) {
            echo "There is some problem in connection: " . $e->getMessage();
            exit;
        }
    } else {
        echo "<p>Select a category to see details.</p>";
        exit;
    }
    ?>

    <div id="clicked_item">
        <!-- Sort Dropdown -->
        <form method="GET" action="">
            <div class="sort-dropdown">
                <span class="sort-heading">Sort</span>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="asc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'selected' : ''; ?>>Price Low to High</option>
                    <option value="desc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'selected' : ''; ?>>Price High to Low</option>
                </select>
            </div>
        </form>
        
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    // Check if the category is found before proceeding
                    if (isset($cat)) {
                        echo "<h1 class='page-header'>" . $cat['name'] . "</h1>";

                        // Sort option: default is 'asc' (Low to High)
                        $sortOrder = isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'DESC' : 'ASC';

                        try {
                            $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = :catid ORDER BY price $sortOrder");
                            $stmt->execute(['catid' => $catid]);
                            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($products) > 0) {
                                echo "<div class='product-grid'>";
                                foreach ($products as $row) {
                                    // Check if product has multiple images
                                    $photos = (!empty($row['photo'])) ? explode(',', $row['photo']) : ['noimage.jpg'];
                                    echo "
                                        <div class='product-item'>
                                            <div class='box-body prod-body'>
                                                <div class='image-gallery' id='image-gallery-" . $row['id'] . "'>";
                                                // Display each image for the product
                                                foreach ($photos as $photo) {
                                                    echo "<img src='images/" . trim($photo) . "' alt='" . $row['name'] . "' class='product-image'>";
                                                }
                                                echo "</div>
                                                <h5><a href='product.php?product=" . $row['slug'] . "'>" . $row['name'] . "</a></h5>
                                            </div>
                                            <div class='box-footer'>
                                                <b>₹ " . number_format($row['price'], 2) . "</b>
                                            </div>
                                        </div>
                                    ";
                                }
                                echo "</div>";
                            } else {
                                echo "<p>No products available in this category.</p>";
                            }
                        } catch (PDOException $e) {
                            echo "There is some problem in connection: " . $e->getMessage();
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    // JavaScript to cycle through images every 2 seconds
    const cycleImages = (productId) => {
        const gallery = document.getElementById('image-gallery-' + productId);
        const images = gallery.getElementsByClassName('product-image');
        let currentIndex = 0;
        
        setInterval(() => {
            // Hide all images
            for (let i = 0; i < images.length; i++) {
                images[i].style.display = 'none';
            }
            // Show the current image
            images[currentIndex].style.display = 'block';
            currentIndex = (currentIndex + 1) % images.length;
        }, 2000); // Change image every 2 seconds
    };

    // Start cycling images for each product
    document.addEventListener('DOMContentLoaded', () => {
        const productIds = <?php echo json_encode(array_column($products, 'id')); ?>;
        productIds.forEach(productId => {
            cycleImages(productId);
        });
    });
</script>

</body>
