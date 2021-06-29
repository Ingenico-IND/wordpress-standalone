<?php

	require_once('Ingenico_SA.php');
	add_action('admin_menu', 'Reconcilation_page');
	function Reconcilation_page(){
		add_menu_page('Ingenico Reconcilation','Ingenico Reconcilation','manage_options', 'ingenico-reconcilation','Reconcilation');
	}

	function Reconcilation(){
		$strCurrentDate = date('Y-m-d');
		$paynimo_class  = new WP_Ingenico();
?>
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<title></title>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<style>
			.container{
				font-family: Times New Roman, serif;
				padding-left: 10px;
				padding-top: 20px;
				font-size: 20px;
			}
			label, .btn{
				font-size: 20px;
				font-weight: bold;
			}
			.btn{
				margin-left: 250px;
				margin-top: 20px;
			}
		</style>
		</head>	
		<body>
		<div class="container">
			<div class="row">
				<div class="text">
				<h1>Reconcilation</h1>
			</div>
			<form class="form-inline" method="POST">

				<label>From Date:</label> 		<input type="date" name="from_date"  placeholder="dd-mm-YYYY" max="<?php echo $strCurrentDate;?>" required>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label>To Date:</label>		<input type="date" name="to_date" placeholder="dd-mm-YYYY" max="<?php echo $strCurrentDate;?>" required><br>        
							<input type="submit" class="btn btn-primary" id="tbtn" name="submit" value="Submit" />
			</form>
		</div>
		<br>
		<br>
		</body>
	</html>	
	<?php 
	global $wpdb;
	$from_date = null;
	$to_date = null;
	if( isset( $_POST["from_date"] ) ) {
		$from_date = $_POST["from_date"]." 00:00:00";
	}
	if( isset( $_POST["to_date"] ) ) {
		$to_date  = $_POST["to_date"]." 23:59:59";
	}
	if ( isset( $_POST["submit"] ) ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix.'ingenico_orders_sa';
		$orderids = $wpdb->get_results("SELECT order_id FROM $table_name WHERE created_dt BETWEEN '$from_date' AND '$to_date' and status = 'in-process' or status = 'awaited'" );

		$merchant_code = $paynimo_class->ingenico_merchant_code;
		$successFullOrdersIds = [];

		foreach ( $orderids as $order_array ) {

			$id = $order_array->order_id;

			$result = $wpdb->get_results("SELECT * FROM $table_name WHERE order_id ='$id'");

			$order_id = $result[0]->order_id;
			$currency = $result[0]->currency_type;
			
			$response_date = explode(' ', $result[0]->updated_dt);
			$orgDate = $response_date[0];
			$date = date("d-m-Y", strtotime($orgDate));  

			$merchantTxnRefNumber = $result[0]->merchant_identifier;	

			$request_array = array("merchant"=>array("identifier"=>$merchant_code),
									"transaction"=>array(
										"deviceIdentifier"=>"S",
										"currency"=>$currency,
										"identifier"=>$merchantTxnRefNumber,
										"dateTime"=>$date,
										"requestType"=>"O"			
								));
			$return_data = json_encode($request_array);
			$url = "https://www.paynimo.com/api/paynimoV2.req";
			$options = array(
				'http' => array(
					'method'  => 'POST',
					'content' => json_encode($request_array),
					'header' =>  "Content-Type: application/json\r\n" .
					"Accept: application/json\r\n"
				)
			);

			$context     = stream_context_create($options);
			$response_array = json_decode(file_get_contents($url, false, $context));

			$status_code = $response_array->paymentMethod->paymentTransaction->statusCode; 
			$status_message = $response_array->paymentMethod->paymentTransaction->statusMessage;
			$txn_id = $response_array->paymentMethod->paymentTransaction->identifier;

			if ( $status_code == '0300' ) {
				$success_ids = $order_id;
				array_push($successFullOrdersIds, $success_ids);
				
				$wpdb->update( 
					$table_name, 
					array(
						'status'   =>  'success',
					),
					array('order_id' => $success_ids)
				);
			} else if ( $status_code=="0397" || $status_code=="0399" || $status_code=="0396" || $status_code=="0392" ) {
				$success_ids = $order_id;
				array_push($successFullOrdersIds, $success_ids);
				$wpdb->update( 
					$table_name, 
					array(
						'status'   =>  'cancelled',
					),
					array('order_id' => $success_ids)
				);
			} else {
				null;
			}
		}
		if ( $successFullOrdersIds ) {
			$message = "Updated Order Status for Order ID : <br><br>  " . implode("<br><br> ", $successFullOrdersIds);
		} else {
			$message = "Updated Order Status for Order ID: None";
		}
		?>
		<h2> <?php echo $message; ?> </h2> 	
		<?php
	}
}