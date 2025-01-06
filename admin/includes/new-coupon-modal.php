<?php
// Include the database connection
// include('db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the data from the form
  print_r($_POST);
  $code = $_POST['code'];
  $discount_type = $_POST['discount_type'];
  $discount_value = $_POST['discount_value'];
  $expiration_date = $_POST['expiration_date'];
  $min_cart_value = $_POST['min_cart_value'] ?? 0;
  $usage_limit = $_POST['usage_limit'] ?? 1;
  $applicable_to_users = $_POST['applicable_to_users'] ?? '';

  // Prepare the SQL query to insert the coupon
  $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, expiration_date, min_cart_value, usage_limit, applicable_to_users) 
                            VALUES (:code, :discount_type, :discount_value, :expiration_date, :min_cart_value, :usage_limit, :applicable_to_users)");

  // Execute the query
  if (
    $stmt->execute([
      ':code' => $code,
      ':discount_type' => $discount_type,
      ':discount_value' => $discount_value,
      ':expiration_date' => $expiration_date,
      ':min_cart_value' => $min_cart_value,
      ':usage_limit' => $usage_limit,
      ':applicable_to_users' => $applicable_to_users
    ])
  ) {
    echo "Coupon saved successfully!";
    // Redirect the page to itself to reload
  } else {
    echo "Error saving coupon!";
  }
 
}
?>
<!-- Description -->
<div class="modal fade" id="description">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b><span class="name"></span></b></h4>
      </div>
      <div class="modal-body">
        <p id="desc"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i
            class="fa fa-close"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Add -->
<!-- Add New Product -->
<div class="modal fade" id="addnew">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Add New Coupon</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">
          <!-- Coupon Code -->
          <div class="mb-3">
            <label for="code" class="form-label">Coupon Code</label>
            <input type="text" class="form-control" id="code" name="code" required>
          </div>

          <!-- Discount Type -->
          <div class="mb-3">
            <label for="discount_type" class="form-label">Discount Type</label>
            <select class="form-control" id="discount_type" name="discount_type" required>
              <option value="percentage">Percentage</option>
              <option value="fixed">Fixed Amount</option>
            </select>
          </div>

          <!-- Discount Value -->
          <div class="mb-3">
            <label for="discount_value" class="form-label">Discount Value</label>
            <input type="number" class="form-control" id="discount_value" name="discount_value" required>
          </div>

          <!-- Expiration Date -->
          <div class="mb-3">
            <label for="expiration_date" class="form-label">Expiration Date</label>
            <input type="datetime-local" class="form-control" id="expiration_date" name="expiration_date">
          </div>

          <!-- Minimum Cart Value -->
          <div class="mb-3">
            <label for="min_cart_value" class="form-label">Minimum Cart Value</label>
            <input type="number" class="form-control" id="min_cart_value" name="min_cart_value" value="0">
          </div>

          <!-- Usage Limit -->
          <div class="mb-3">
            <label for="usage_limit" class="form-label">Usage Limit</label>
            <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="1">
          </div>

          <!-- Applicable Users -->
          <div class="mb-3">
            <label for="applicable_to_users" class="form-label">Applicable to Users (User IDs, Comma Separated)</label>
            <textarea class="form-control" id="applicable_to_users" name="applicable_to_users"></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Save Coupon</button>
        </form>
      </div>
    </div>
  </div>
</div>