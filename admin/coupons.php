<?php include 'includes/session.php'; ?>
<?php
$where = '';
if (isset($_GET['category'])) {
  $catid = $_GET['category'];
  $where = 'WHERE category_id =' . $catid;
}

?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Coupons
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li>Coupons</li>
          <li class="active">Coupon List</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <?php
        if (isset($_SESSION['error'])) {
          echo "
        <div class='alert alert-danger alert-dismissible'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
          <h4><i class='icon fa fa-warning'></i> Error!</h4>
          " . $_SESSION['error'] . "
        </div>
      ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "
        <div class='alert alert-success alert-dismissible'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
          <h4><i class='icon fa fa-check'></i> Success!</h4>
          " . $_SESSION['success'] . "
        </div>
      ";
          unset($_SESSION['success']);
        }
        ?>
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header with-border">
                <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat" id="addproduct"><i
                    class="fa fa-plus"></i> New</a>
                <div class="pull-right">
                  <!-- items in right  -->
                </div>
              </div>
              <div class="box-body">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <th>Coupon Code</th>
                    <th>Discount Type</th>
                    <th>Discount Value</th>
                    <th>Expiration Date</th>
                    <th>Usage Limit</th>
                    <th>Applicable Users</th>
                    <th>Tools</th>
                  </thead>
                  <tbody>
                    <?php
                    $conn = $pdo->open();

                    try {
                      // Get current date for comparison
                      $now = date('Y-m-d');

                      // Query to fetch all coupons (you can modify the query to filter by conditions, like expiry date, etc.)
                      $stmt = $conn->prepare("SELECT * FROM coupons");
                      $stmt->execute();

                      // Loop through each coupon record
                      foreach ($stmt as $row) {
                        // Check if the coupon has expired
                        $expiration_date = new DateTime($row['expiration_date']);
                        $is_expired = $expiration_date < new DateTime();

                        // Display coupon data in the table
                        echo "<tr>";
                        echo "<td>" . $row['code'] . "</td>";  // Coupon code
                        echo "<td>" . ucfirst($row['discount_type']) . "</td>";  // Discount type (percentage or fixed)
                        echo "<td>&#36; " . number_format($row['discount_value'], 2) . "</td>";  // Discount value
                        echo "<td>" . ($row['expiration_date'] ? date('Y-m-d H:i', strtotime($row['expiration_date'])) : 'N/A') . "</td>";  // Expiration date
                        echo "<td>" . $row['usage_limit'] . "</td>";  // Usage limit
                        echo "<td>" . ($row['applicable_to_users'] ? $row['applicable_to_users'] : 'All users') . "</td>";  // Applicable users (can be a list of user IDs)
                        echo "<td>
          <button class='btn btn-success btn-sm edit btn-flat' data-id='" . $row['id'] . "'><i class='fa fa-edit'></i> Edit</button>
          <button class='btn btn-danger btn-sm delete btn-flat' data-id='" . $row['id'] . "'><i class='fa fa-trash'></i> Delete</button>
        </td>";
                        echo "</tr>";
                      }
                    } catch (PDOException $e) {
                      echo $e->getMessage();
                    }

                    $pdo->close();
                    ?>
                  </tbody>
                </table>

              </div>
            </div>
          </div>
        </div>
      </section>


    </div>
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/new-coupon-modal.php'; ?>
    <?php include './edit-coupon-modal.php'; ?>
    <?php include './delete-coupon-modal.php'; ?>
    
  </div>
  <!-- ./wrapper -->

  <?php include 'includes/scripts.php'; ?>
  <script>

    $(function () {
      $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        $('#edit').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      $(document).on('click', '.delete', function (e) {
        e.preventDefault();
        $('#delete').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      $(document).on('click', '.photo', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        getRow(id);
      });

      $(document).on('click', '.desc', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        getRow(id);
      });

      $('#select_category').change(function () {
        var val = $(this).val();
        if (val == 0) {
          window.location = 'products.php';
        }
        else {
          window.location = 'products.php?category=' + val;
        }
      });

      $('#addproduct').click(function (e) {
        e.preventDefault();
        getCategory();
      });

      $("#addnew").on("hidden.bs.modal", function () {
        $('.append_items').remove();
      });

      $("#edit").on("hidden.bs.modal", function () {
        $('.append_items').remove();
      });

    });

    function getRow(id) {
      $.ajax({
        type: 'POST',
        url: 'coupon_row.php',
        data: { id: id },
        dataType: 'json',
        success: function (response) {
          console.log(response);  // Check if the data is coming correctly

          // Populate the modal fields with the response data
          $('#delete_product_id').val(response.product_id);  // Coupon ID
          $('#product_id').val(response.product_id);  // Coupon ID
          $('#edit_name').val(response.code);  // Coupon Code
          $('#edit_price').val(response.discount_value);  // Discount Value
          // Optional: If you have a specific format for the expiration date
          var expirationDate = new Date(response.expiration_date);
          $('#edit_expiration').val(expirationDate.toISOString().slice(0, 16));  // Expiration Date (for <input type="datetime-local">)

          // Other fields like min_cart_value, usage_limit, applicable_to_users can be populated similarly if needed
          $('#edit_min_cart_value').val(response.min_cart_value);  // Minimum Cart Value
          $('#edit_usage_limit').val(response.usage_limit);  // Usage Limit
          $('#edit_applicable_users').val(response.applicable_to_users);  // Applicable Users (if needed)

        },
        error: function (xhr, status, error) {
          console.log('Error: ' + error);
        }
      });
    }


    function getCategory() {
      $.ajax({
        type: 'POST',
        url: 'category_fetch.php',
        dataType: 'json',
        success: function (response) {
          $('#category').append(response);
          $('#edit_category').append(response);
        }
      });
    }
  </script>
</body>

</html>