<?php
    include 'includes/session.php';
    include 'includes/slugify.php';

    if(isset($_POST['add'])){
        $name = $_POST['name'];
        $slug = slugify($name);
        $category = $_POST['category'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        // Process multiple photos
        $photos = $_FILES['photos'];
        $photo_names = [];

        $conn = $pdo->open();

        // Check if product already exists
        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM products WHERE slug=:slug");
        $stmt->execute(['slug'=>$slug]);
        $row = $stmt->fetch();

        if($row['numrows'] > 0){
            $_SESSION['error'] = 'Product already exists';
        }
        else{
            // Process all the files uploaded
            if (!empty($photos['name'][0])) {
                $upload_dir = '../images/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true); // Create directory if it doesn't exist
                }

                foreach ($photos['name'] as $key => $photo) {
                    $ext = pathinfo($photo, PATHINFO_EXTENSION);
                    $new_filename = $slug.'_'.time().'_'.$key.'.'.$ext;

                    // Move the file to the upload directory
                    if (move_uploaded_file($photos['tmp_name'][$key], $upload_dir . $new_filename)) {
                        $photo_names[] = $new_filename;
                    } else {
                        $_SESSION['error'] = 'Failed to upload some photos';
                    }
                }
            }

            try {
                // Insert product and store photo names as a comma-separated string
                $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, slug, price, photo) 
                                       VALUES (:category, :name, :description, :slug, :price, :photo)");
                $stmt->execute([
                    'category' => $category,
                    'name' => $name,
                    'description' => $description,
                    'slug' => $slug,
                    'price' => $price,
                    'photo' => implode(',', $photo_names) // Save photo names as a comma-separated string
                ]);
                $_SESSION['success'] = 'Product added successfully';
            }
            catch(PDOException $e){
                $_SESSION['error'] = $e->getMessage();
            }
        }

        $pdo->close();
    }
    else{
        $_SESSION['error'] = 'Fill up product form first';
    }

    header('location: products.php');
?>
