<?php include 'includes/session.php'; ?>
<?php
$slug = $_GET['category'];

$conn = $pdo->open();

try {
    // Fetch category details based on the category slug
    $stmt = $conn->prepare("SELECT * FROM category WHERE cat_slug = :slug");
    $stmt->execute(['slug' => $slug]);
    $cat = $stmt->fetch();
    $catid = $cat['id'];
} catch (PDOException $e) {
    echo "There is some problem in connection: " . $e->getMessage();
}

$pdo->close();
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">
        <?php include 'includes/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="container">

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-sm-9">
                            <h1 class="page-header"><?php echo $cat['name']; ?></h1>

                            <!-- Sort Dropdown -->
                            <form method="GET" action="">
                                <input type="hidden" name="category" value="<?php echo $slug; ?>">
                                <div class="sort-dropdown">
                                    <label for="sort">Sort by price: </label>
                                    <select name="sort" id="sort" onchange="this.form.submit()">
                                        <option value="asc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'selected' : ''; ?>>Price Low to High</option>
                                        <option value="desc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'selected' : ''; ?>>Price High to Low</option>
                                    </select>
                                </div>
                            </form>

                            <?php
                            // Set sort order based on user's choice, default is 'asc'
                            $sortOrder = isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'DESC' : 'ASC';

                            $conn = $pdo->open();

                            try {
                                $inc = 3;
                                // Fetch products from the database based on the selected category
                                $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = :catid ORDER BY price $sortOrder");
                                $stmt->execute(['catid' => $catid]);

                                foreach ($stmt as $row) {
                                    // Default image or the first image if there are multiple
                                    $image = (!empty($row['photo'])) ? 'images/' . $row['photo'] : 'images/noimage.jpg';

                                    // Check if additional images exist (comma-separated)
                                    $additionalImages = [];
                                    if (!empty($row['photo'])) {
                                        $additionalImages = explode(',', $row['photo']); // Split the comma-separated values into an array
                                    }

                                    // If there are additional images, use the first image for display initially
                                    if (count($additionalImages) > 0) {
                                        $image = 'images/' . $additionalImages[0]; // Show only the first image initially
                                    }

                                    // Layout for product grid
                                    $inc = ($inc == 3) ? 1 : $inc + 1;
                                    if ($inc == 1) echo "<div class='row'>"; // Start a new row every 3 products

                                    // Display product and handle image rotation if multiple images exist
                                    echo "
                                    <div class='col-sm-4'>
                                        <div class='box box-solid'>
                                            <div class='box-body prod-body'>
                                                <img src='" . $image . "' width='100%' height='230px' class='thumbnail' id='product-image-".$row['id']."'>
                                                <h5><a href='product.php?product=" . $row['slug'] . "'>" . $row['name'] . "</a></h5>
                                            </div>
                                            <div class='box-footer'>
                                                <b>&#36; " . number_format($row['price'], 2) . "</b>
                                            </div>
                                        </div>
                                    </div>
                                    ";

                                    // Close the row after 3 products
                                    if ($inc == 3) echo "</div>";
                                    
                                    // Add JS code to rotate images every 3 seconds if there are multiple images
                                    if (count($additionalImages) > 1) {
                                        echo "
                                        <script>
                                            var images = ".json_encode($additionalImages).";
                                            var currentIndex = 0;
                                            var productImageElement = document.getElementById('product-image-".$row['id']."');

                                            // Check if there are multiple images
                                            if (images.length > 1) {
                                                setInterval(function() {
                                                    // Update the index to loop through images
                                                    currentIndex = (currentIndex + 1) % images.length; // Loop through the images
                                                    
                                                    // Update the image source to the next image
                                                    productImageElement.src = 'images/' + images[currentIndex];
                                                    
                                                }, 3000); // Change image every 3 seconds
                                            }
                                        </script>
                                        ";
                                    }
                                }

                                // Close any open rows if the number of products is less than 3
                                if ($inc == 1) echo "<div class='col-sm-4'></div><div class='col-sm-4'></div></div>";
                                if ($inc == 2) echo "<div class='col-sm-4'></div></div>";
                            } catch (PDOException $e) {
                                echo "There is some problem in connection: " . $e->getMessage();
                            }

                            $pdo->close();
                            ?>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-sm-3">
                            <?php include 'includes/sidebar.php'; ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
