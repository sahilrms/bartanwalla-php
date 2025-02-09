<?php include 'includes/session.php'; ?>
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
							<h1 class="page-header">YOUR CART</h1>
							<div class="box box-solid">
								<div class="box-body">
									<table class="table table-bordered">
										<thead>
											<th></th>
											<th>Photo</th>
											<th>Name</th>
											<th>Price</th>
											<th width="20%">Quantity</th>
											<th>Subtotal</th>
										</thead>
										<tbody id="tbody">
										</tbody>
									</table>
									<!-- coupons  -->
									<div class="form-group">
										<label for="coupon_code">Coupon Code</label>
										<input type="text" class="form-control" id="coupon_code" name="coupon_code"
											placeholder="Enter coupon code">
										<button type="button" id="apply_coupon" class="btn btn-primary mt-4">Apply
											Coupon</button>
										<div id="coupon_message" class="mt-2"></div>
									</div>
									<!-- total table  -->
									<div class="box box-solid">
										<div class="box-body">
											<h4>Final Bill</h4>
											<table class="table table-bordered">
												<tr>
													<td>Items Total</td>
													<td id="items_total">$0.00</td>
												</tr>
												<tr>
													<td>Coupon Discount</td>
													<td id="coupon_discount">$0.00</td>
												</tr>
												<tr>
													<td>Shipping Charges</td>
													<td id="shipping">$50.00</td>
												</tr>
												<tr>
													<td>Other Discounts</td>
													<td id="other_discount">$0.00</td>
												</tr>
												<tr>
													<th>Grand Total</th>
													<th id="grand_total">$0.00</th>
												</tr>
											</table>
										</div>
									</div>

								</div>
							</div>

							<div class="box box-solid">
								<div class="box-body">
									<div class="address-section border p-3 rounded mt-3">
										<h4 class="mb-3">Shipping Address</h4>
										<form action="place_order.php" method="POST">
											<div class="row">
												<div class="col-md-6">
													<label>Full Name</label>
													<input type="text" name="full_name" class="form-control" required>
												</div>

												<div class="col-md-6">
													<label>Phone Number</label>
													<input type="tel" name="phone" class="form-control"
														pattern="[0-9]{10}" required>
												</div>

												<div class="col-md-12">
													<label>Address Line 1</label>
													<input type="text" name="address1" class="form-control" required>
												</div>

												<div class="col-md-12">
													<label>Address Line 2</label>
													<input type="text" name="address2" class="form-control" required>
												</div>

												<div class="col-md-6">
													<label>City</label>
													<input type="text" name="city" class="form-control" required>
												</div>

												<div class="col-md-6">
													<label>State</label>
													<input type="text" name="state" class="form-control" required>
												</div>

												<div class="col-md-6">
													<label>PIN Code</label>
													<input type="text" name="pincode" class="form-control"
														pattern="[0-9]{6}" required>
												</div>
												<input type="hidden" name="cart_items" id="cart_items">
												<input type="hidden" name="items_total" id="items_total_hidden">
												<input type="hidden" name="grand_total" id="grandTotal_hidden">
												<input type="hidden" name="coupon_discount" id="coupon_discount_hidden">
												<input type="hidden" name="shipping" id="shipping_hidden" value="50.00">
												<input type="hidden" name="other_discount" id="other_discount_hidden">


											</div>
											<div class="row" style="margin-top: 10px;">
												<div class="col-md-12 mt-3 d-flex justify-content-end">
													<button type="submit" class="btn btn-success">Place Order</button>
												</div>
											</div>

										</form>
									</div>

								</div>
							</div>



							//payment gateway here
						</div>
						<div class="col-sm-3">
							<?php include 'includes/sidebar.php'; ?>
						</div>
					</div>
				</section>

			</div>
		</div>

		<?php $pdo->close(); ?>
		<?php include 'includes/footer.php'; ?>
	</div>

	<?php include 'includes/scripts.php'; ?>
	<script>
		window.addEventListener('unload', function () {
			// Clear the localStorage
			localStorage.clear();
		});
		// document.addEventListener("DOMContentLoaded", updateCartValue());
		var total = 0;
		$(function () {
			$(document).on('click', '.cart_delete', function (e) {
				e.preventDefault();
				var id = $(this).data('id');
				$.ajax({
					type: 'POST',
					url: 'cart_delete.php',
					data: { id: id },
					dataType: 'json',
					success: function (response) {
						if (!response.error) {
							getDetails();
							getCart();
							getTotal();
							updateCartValue();
						}
					}
				});
			});

			$(document).ready(function () {
				$("form").submit(function (e) {
					// Ensure hidden fields are updated before submission
					updateCartValue();
				});
			});

			$(document).on('click', '.minus', function (e) {
				e.preventDefault();
				var id = $(this).data('id');
				var qty = $('#qty_' + id).val();
				if (qty > 1) {
					qty--;
				}
				$('#qty_' + id).val(qty);
				$.ajax({
					type: 'POST',
					url: 'cart_update.php',
					data: { id: id, qty: qty },
					dataType: 'json',
					success: function (response) {
						if (!response.error) {
							getDetails();
							getCart();
							getTotal(); // Update total after quantity change
							updateCartValue();
						}
					}
				});
			});

			$(document).on('click', '.add', function (e) {
				e.preventDefault();
				var id = $(this).data('id');
				var qty = $('#qty_' + id).val();
				qty++;
				$('#qty_' + id).val(qty);
				$.ajax({
					type: 'POST',
					url: 'cart_update.php',
					data: { id: id, qty: qty },
					dataType: 'json',
					success: function (response) {
						if (!response.error) {
							getDetails();
							getCart();
							getTotal(); // Update total after quantity change
						}
					}
				});
			});

			getDetails();
			getTotal();
			updateCartValue();

		});

		function getDetails() {
			$.ajax({
				type: 'POST',
				url: 'cart_details.php',
				dataType: 'json',
				success: function (response) {
					console.log('isResponse', response)
					$('#tbody').html(response);
					getCart();
				}
			});
		}

		function getTableDataIds() {
			let ids = [];
			console.log()
			document.querySelectorAll("#tbody tr[data-item-id]").forEach(td => {
				ids.push(td.getAttribute("data-item-id"));
			});
			return ids;
		}


		function getTotal() {
			var coupon_code = window.localStorage.getItem('Coupon')
			$.ajax({
				type: 'POST',
				url: 'cart_total.php',
				data: { coupon_code: coupon_code },
				dataType: 'json',
				success: function (response) {
					if (response.success) {
						$('#items_total').text('₹' + response.items_total.toFixed(2));
						$('#coupon_discount').text('-₹' + response.coupon_discount.toFixed(2));
						$('#shipping').text('₹' + response.shipping.toFixed(2));
						$('#other_discount').text('-₹' + response.other_discount.toFixed(2));
						$('#grand_total').text('₹' + response.grand_total.toFixed(2));
						updateCartValue();
					}
				}
			});
		}

		function updateCartValue() {

			// Get cart data from localStorage
			var cartItems = getTableDataIds();
			var couponDiscount = document.getElementById("coupon_discount").textContent.replace(/[₹,]/g, '');
			var otherDiscount = document.getElementById("other_discount").textContent.replace(/[₹,]/g, '');
			var itemsTotal = document.getElementById("items_total").textContent.replace(/[₹,]/g, '');
			var shipping = document.getElementById("shipping").textContent.replace(/[₹,]/g, '');
			var grandTotal = document.getElementById("grand_total").textContent.replace(/[₹,]/g, '');


			console.log('cartItems', cartItems)
			console.log('couponDiscount', couponDiscount)
			console.log('otherDiscount', otherDiscount)
			console.log('itemsTotal', itemsTotal)
			console.log('shipping', shipping)
			console.log('grandTotal', grandTotal)


			// Set form hidden values
			document.getElementById("cart_items").value = JSON.stringify(cartItems);
			document.getElementById("items_total_hidden").value = itemsTotal;
			document.getElementById("grandTotal_hidden").value = grandTotal;
			document.getElementById("coupon_discount_hidden").value = couponDiscount;
			document.getElementById("other_discount_hidden").value = otherDiscount;
			document.getElementById("shipping_hidden").value = shipping;

		}

		$(document).on('click', '#apply_coupon', function (e) {
			e.preventDefault();
			function findTotal(coupon_code) {

				$.ajax({
					type: 'POST',
					url: 'cart_total.php',
					data: { coupon_code: coupon_code },
					dataType: 'json',
					success: function (response) {
						if (response.success) {
							console.log(response)
							$('#items_total').text('₹' + response.items_total.toFixed(2));
							$('#coupon_discount').text('-₹' + response.coupon_discount);
							$('#shipping').text('₹' + response.shipping.toFixed(2));
							$('#other_discount').text('-₹' + response.other_discount.toFixed(2));
							$('#grand_total').text('₹' + response.grand_total.toFixed(2));
							updateCartValue();
						}
					}
				});
			}
			var coupon_code = $('#coupon_code').val();

			if (coupon_code !== '') {
				window.localStorage.setItem('Coupon', coupon_code)
				$.ajax({
					type: 'POST',
					url: 'apply_coupon.php',
					data: { coupon_code: coupon_code },
					dataType: 'json',
					success: function (response) {
						if (response.success) {
							var discountMessage = '';
							if (response.discount_type === 'percentage') {
								discountMessage = '<span class="text-success">Coupon applied successfully! Discount: ' + response.discount + '%</span>';
								findTotal(coupon_code); // Recalculate total after coupon application
							} else if (response.discount_type === 'fixed') {
								findTotal(coupon_code); // Recalculate total after coupon application
								discountMessage = '<span class="text-success">Coupon applied successfully! Discount: ₹' + response.discount + '</span>';
							}
							$('#coupon_message').html(discountMessage);
							updateCartValue();

						} else {
							$('#coupon_message').html('<span class="text-danger">Invalid coupon code!</span>');
						}
					},
					error: function (xhr, status, error) {
						console.log('Error: ' + error);
						$('#coupon_message').html('<span class="text-danger">There was an error applying the coupon!</span>');
					}
				});
			} else {
				$('#coupon_message').html('<span class="text-danger">Please enter a coupon code.</span>');
			}
		});


	</script>

</body>

</html>