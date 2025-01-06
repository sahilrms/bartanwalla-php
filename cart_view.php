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


							<!-- payment gateway  -->
							<?php
							if (isset($_SESSION['user'])) {
								echo "
	        					<div id='paypal-button'></div>
	        				";
							} else {
								echo "
	        					<h4>You need to <a href='login.php'>Login</a> to checkout.</h4>
	        				";
							}
							?>
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
						}
					}
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

		});

		function getDetails() {
			$.ajax({
				type: 'POST',
				url: 'cart_details.php',
				dataType: 'json',
				success: function (response) {

					$('#tbody').html(response);
					getCart();
				}
			});
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
						$('#items_total').text('$' + response.items_total.toFixed(2));
						$('#coupon_discount').text('-$' + response.coupon_discount.toFixed(2));
						$('#shipping').text('$' + response.shipping.toFixed(2));
						$('#other_discount').text('-$' + response.other_discount.toFixed(2));
						$('#grand_total').text('$' + response.grand_total.toFixed(2));
					}
				}
			});
		}



		// function getTotal() {
		// 	$.ajax({
		// 		type: 'POST',
		// 		url: 'cart_total.php',
		// 		dataType: 'json',
		// 		success: function (response) {
		// 			total = response;
		// 		}
		// 	});
		// }

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
							$('#items_total').text('$' + response.items_total.toFixed(2));
							$('#coupon_discount').text('-$' + response.coupon_discount);
							$('#shipping').text('$' + response.shipping.toFixed(2));
							$('#other_discount').text('-$' + response.other_discount.toFixed(2));
							$('#grand_total').text('$' + response.grand_total.toFixed(2));
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
	<!-- Paypal Express -->
	<script>
		paypal.Button.render({
			env: 'sandbox', // change for production if app is live,

			client: {
				sandbox: 'ASb1ZbVxG5ZFzCWLdYLi_d1-k5rmSjvBZhxP2etCxBKXaJHxPba13JJD_D3dTNriRbAv3Kp_72cgDvaZ',
				//production: 'AaBHKJFEej4V6yaArjzSx9cuf-UYesQYKqynQVCdBlKuZKawDDzFyuQdidPOBSGEhWaNQnnvfzuFB9SM'
			},

			commit: true, // Show a 'Pay Now' button

			style: {
				color: 'gold',
				size: 'small'
			},

			payment: function (data, actions) {
				return actions.payment.create({
					payment: {
						transactions: [
							{
								//total purchase
								amount: {
									total: total,
									currency: 'USD'
								}
							}
						]
					}
				});
			},

			onAuthorize: function (data, actions) {
				return actions.payment.execute().then(function (payment) {
					window.location = 'sales.php?pay=' + payment.id;
				});
			},

		}, '#paypal-button');
	</script>
</body>

</html>