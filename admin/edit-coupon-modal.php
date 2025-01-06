<div id="edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Edit Coupon</h4>
      </div>
      <div class="modal-body">
        <form id="edit-form" method="POST" action="update_coupon.php">
          <input type="hidden" name="product_id" id="product_id"> <!-- Hidden field for coupon ID -->
          
          <div class="form-group">
            <label for="edit_name">Coupon Code</label>
            <input type="text" class="form-control" id="edit_name" name="edit_name">
          </div>

          <div class="form-group">
            <label for="edit_price">Discount Value</label>
            <input type="number" class="form-control" id="edit_price" name="edit_price">
          </div>


          <div class="form-group">
            <label for="edit_expiration">Expiration Date</label>
            <input type="datetime-local" class="form-control" id="edit_expiration" name="edit_expiration">
          </div>

          <div class="form-group">
            <label for="edit_min_cart_value">Minimum Cart Value</label>
            <input type="number" class="form-control" id="edit_min_cart_value" name="edit_min_cart_value">
          </div>

          <div class="form-group">
            <label for="edit_usage_limit">Usage Limit</label>
            <input type="number" class="form-control" id="edit_usage_limit" name="edit_usage_limit">
          </div>

          <div class="form-group">
            <label for="edit_applicable_users">Applicable Users</label>
            <input type="text" class="form-control" id="edit_applicable_users" name="edit_applicable_users">
          </div>
          


          <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
      </div>
    </div>
  </div>
</div>