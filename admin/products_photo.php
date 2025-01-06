<?php
    include 'includes/session.php';

    if(isset($_POST['upload'])){
        $id = $_POST['id'];
        $photos = $_FILES['photos'];
        $photo_names = [];

        $conn = $pdo->open();

        $stmt = $conn->prepare("SELECT * FROM products WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        $row = $stmt->fetch();

        // Debugging: Check if file uploads are successful
        if ($_FILES['photos']['error'][0] != UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Error uploading files: " . $_FILES['photos']['error'][0];
        }

        if (!empty($photos['name'][0])) {
            $upload_dir = '../images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Create directory if it doesn't exist
            }

            foreach ($photos['name'] as $key => $photo) {
                $ext = pathinfo($photo, PATHINFO_EXTENSION);
                $new_filename = $row['slug'].'_'.time().'_'.$key.'.'.$ext;

                // Move uploaded files
                if (move_uploaded_file($photos['tmp_name'][$key], $upload_dir . $new_filename)) {
                    $photo_names[] = $new_filename;
                } else {
                    $_SESSION['error'] = 'Failed to upload some photos';
                }
            }

            // Update product photo field with new image names
            try {
                $existing_photos = explode(',', $row['photo']); // Existing photos in database
                $all_photos = array_merge($existing_photos, $photo_names); // Merge old and new photo names
                $stmt = $conn->prepare("UPDATE products SET photo=:photo WHERE id=:id");
                $stmt->execute(['photo'=>implode(',', $all_photos), 'id'=>$id]);
                $_SESSION['success'] = 'Product photos updated successfully';
            }
            catch(PDOException $e){
                $_SESSION['error'] = $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'No photos selected';
        }

        $pdo->close();
    } else {
        $_SESSION['error'] = 'Select product to update photo first';
    }

    header('location: products.php');
?>
