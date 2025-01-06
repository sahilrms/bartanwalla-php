<?php
	include 'includes/session.php';

	if(isset($_POST['delete'])){
		$id = $_POST['delete_product_id'];
		
		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("DELETE FROM coupons WHERE id=:id");
			$stmt->execute(['id'=>$id]);

			$_SESSION['success'] = 'Coupon deleted successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Select coupon to delete first';
	}

	header('location: coupons.php');
	
?>