<head>
    <style>
        /* Hover effect on thumbnails */
        .product-thumbnail {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        /* Apply hover effect on thumbnails */
        .product-thumbnail:hover {
            transform: scale(1.1);
            /* Slightly scale up thumbnails on hover */
        }

        /* No zoom effect on the main image */
        .main-image-container img {
            transition: transform 0.3s ease;
        }
    </style>
</head>

<?php include 'includes/session.php'; ?>

<?php
$conn = $pdo->open();

$slug = $_GET['product'];

try {
    $stmt = $conn->prepare("SELECT *, products.name AS prodname, category.name AS catname, products.id AS prodid FROM products LEFT JOIN category ON category.id=products.category_id WHERE slug = :slug");
    $stmt->execute(['slug' => $slug]);
    $product = $stmt->fetch();
} catch (PDOException $e) {
    echo "There is some problem in connection: " . $e->getMessage();
}

// Page view
$now = date('Y-m-d');
if ($product['date_view'] == $now) {
    $stmt = $conn->prepare("UPDATE products SET counter=counter+1 WHERE id=:id");
    $stmt->execute(['id' => $product['prodid']]);
} else {
    $stmt = $conn->prepare("UPDATE products SET counter=1, date_view=:now WHERE id=:id");
    $stmt->execute(['id' => $product['prodid'], 'now' => $now]);
}
?>

<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue layout-top-nav">
    <script>
        function detectClick(element) {

            console.log("Clicked image src: " + element.src);
            const imageSrc = element.getAttribute("data-image-src");


            const mainImage = document.getElementById('mainImage');
            mainImage.src = imageSrc;

            // Update the data-magnify-src attribute for zoom functionality
            mainImage.setAttribute('data-magnify-src', 'images/large-' + imageSrc);
        }
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.12';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>


    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="container">

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="callout" id="callout" style="display:none">
                                <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
                                <span class="message"></span>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <!-- Main Product Image -->
                                    <div class="main-image-container">
                                        <?php
                                        // Default image if no images exist
                                        $first_image = 'images/noimage.jpg';

                                        if (!empty($product['photo'])) {
                                            // Split the string of images into an array
                                            $photos = explode(',', $product['photo']);
                                            // Set the first image as the main image
                                            $first_image = 'images/' . trim($photos[0]);
                                        }
                                        ?>
                                        <img id="mainImage" src="<?php echo $first_image; ?>" width="100%"
                                            data-magnify-src="<?php echo 'images/large-' . basename($first_image); ?>">
                                    </div>
                                    <br><br>

                                    <!-- Small Images Thumbnails -->
                                    <div class="product-images">
                                        <?php
                                        if (!empty($product['photo'])) {
                                            $photos = explode(',', $product['photo']);
                                            foreach ($photos as $photo) {
                                                $photo_path = 'images/' . trim($photo);
                                                // Use 'this' to pass the clicked image element to the JavaScript function
                                                echo '<img src="' . $photo_path . '" alt="Product Image" width="100" onclick="detectClick(this)" height="100" class="product-thumbnail" style="margin: 5px; cursor: pointer;" data-image-src="' . $photo_path . '">';
                                            }
                                        } else {
                                            echo '<img src="images/noimage.jpg" alt="No Image Available" width="100" height="100" class="product-thumbnail" style="margin: 5px; cursor: pointer;" data-image-src="images/noimage.jpg">';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h1 class="page-header"><?php echo $product['prodname']; ?></h1>
                                    <h3><b>₹ <?php echo number_format($product['price'], 2); ?></b></h3>
                                    <p><b>Category:</b> <a
                                            href="category.php?category=<?php echo $product['cat_slug']; ?>"><?php echo $product['catname']; ?></a>
                                    </p>
                                    <p><b>Description:</b></p>
                                    <p><?php echo $product['description']; ?></p>

                                    <!-- Product Quantity and Add to Cart -->
                                    <form class="form-inline" id="productForm">
                                        <div class="form-group">
                                            <div class="input-group col-sm-5">
                                                <span class="input-group-btn">
                                                    <button type="button" id="minus"
                                                        class="btn btn-default btn-flat btn-lg"><i
                                                            class="fa fa-minus"></i></button>
                                                </span>
                                                <input type="text" name="quantity" id="quantity"
                                                    class="form-control input-lg" value="1">
                                                <span class="input-group-btn">
                                                    <button type="button" id="add"
                                                        class="btn btn-default btn-flat btn-lg"><i
                                                            class="fa fa-plus"></i></button>
                                                </span>
                                                <input type="hidden" value="<?php echo $product['prodid']; ?>"
                                                    name="id">
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-lg btn-flat"><i
                                                    class="fa fa-shopping-cart"></i> Add to Cart</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <br>
                            <div class="fb-comments"
                                data-href="http://localhost/ecommerce/product.php?product=<?php echo $slug; ?>"
                                data-numposts="10" width="100%"></div>
                        </div>
                        <div class="col-sm-3">
                            <?php include 'includes/sidebar.php'; ?>

                        </div>
                    </div>
                </section>
                <h3>Customer Reviews</h3>
                <?php include './show_product_review.php'; ?>
                <?php
                if (isset($_SESSION['user'])) {
                    include './product_review.php';
                }
                ?>
                
            </div>
        </div>

        <?php $pdo->close(); ?>

        <?php include 'includes/footer.php'; ?>
    </div>



    <script>


        // Function to change the main image when a thumbnail is clicked
        function changeMainImage(imageSrc) {
            // Change the src of the main image to the clicked thumbnail image
            const mainImage = document.getElementById('mainImage');
            mainImage.src = imageSrc;

            // Update the data-magnify-src attribute for zoom functionality
            mainImage.setAttribute('data-magnify-src', 'images/large-' + imageSrc.split('/').pop());
        }

        // Add event listener to all thumbnails after the DOM is fully loaded
        document.addEventListener("DOMContentLoaded", function () {
            // Ensure the first image is set as the default selected image on page load
            var firstImage = "<?php echo $first_image; ?>";
            changeMainImage(firstImage);
        });
    </script>
    <?php include 'includes/scripts.php'; ?>
    <script>
        $(function () {
            $('#add').click(function (e) {
                e.preventDefault();
                var quantity = $('#quantity').val();
                quantity++;
                $('#quantity').val(quantity);
            });
            $('#minus').click(function (e) {
                e.preventDefault();
                var quantity = $('#quantity').val();
                if (quantity > 1) {
                    quantity--;
                }
                $('#quantity').val(quantity);
            });

        });
    </script>
</body>

</html>