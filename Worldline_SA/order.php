<?php
require_once('worldline_SA.php');
include_once(dirname(__FILE__) . '/database_worldline.php');

add_action('admin_menu', 'orderpage');
add_action('wp_ajax_display_ov_popup_window', 'display_ov_popup_window');
add_action('wp_ajax_display_refund_popup_window', 'display_refund_popup_window');

function orderpage()
{
	add_menu_page('Worldline Order', 'Worldline Order', 'manage_options', 'worldline-order', 'check_order');
}

function check_order()
{
?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<title>Worldline Orders</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://www.paynimo.com/paynimocheckout/client/lib/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
		<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
		<script type="text/javascript">
			$(document).ready(function() {
				$('#order-data').DataTable({
					"pagingType": "full_numbers"
				});
			});
		</script>
		<style type="text/css">
			select {
				width: 50px;
				margin-bottom: 10px;
			}
		</style>
		<?php enqueue_style_script(); ?>
	</head>

	<body>
		<div class="container">
			<div class="row">
				<div class="text">
					<h1>Worldline Orders</h1>
				</div>
				<table id="order-data" class="display" style="width:100%">
					<thead>
						<tr>
							<th>Id</th>
							<th>Order Id</th>
							<th>Merchant Id</th>
							<th>Transaction Id</th>
							<th>Date Time</th>
							<th>Status</th>
							<th>Currency</th>
							<th>Amount</th>
							<th>Offline Verification</th>
							<th>Refund</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$ajaxurl = esc_url(admin_url('admin-ajax.php'));
						global $wpdb;
						$table_name = $wpdb->prefix . 'worldline_orders_sa';
						$results = $wpdb->get_results("SELECT * FROM $table_name");
						foreach ($results as $row) {
							$date = explode(' ', $row->created_dt);
						?>
							<tr>
								<td><?php echo $row->id; ?></td>
								<td><?php echo $row->order_id; ?></td>
								<td><?php echo $row->merchant_identifier; ?></td>
								<td><?php echo $row->worldline_identifier; ?></td>
								<td><?php echo $row->created_dt; ?></td>
								<?php
								if ($row->status == "success") { ?>
									<td id="status" style="background: #b9e09f;"><?php echo ucfirst($row->status); ?></td> <?php
																														} else if ($row->status == "failure") { ?>
									<td id="status" style="background: lightpink;"><?php echo ucfirst($row->status); ?></td> <?php
																															} else if ($row->status == "in-process") { ?>
									<td id="status" style="background: #ffc26e;"><?php echo ucfirst($row->status); ?>
									</td> <?php
																															} else if ($row->status == "User Aborted") { ?>
									<td id="status" style="background: #fc5858;"><?php echo ucfirst($row->status); ?>
									</td> <?php
																															} else if ($row->status == "AWAITED") { ?>
									<td id="status" style="background: #b091ff;"><?php echo ucfirst($row->status); ?>
									</td> <?php
																															} else { ?>
									<td id="status" style="background: #f2ea9d;">Cancelled</td> <?php
																															} ?>
								<td><?php echo $row->currency_type; ?></td>
								<td><?php echo $row->amount; ?></td>
								<td><button id="offline-verification" onclick="passvalue_ov(
									'<?php echo $ajaxurl; ?>',
									'<?php echo $row->merchant_identifier; ?>',
									'<?php echo $date[0]; ?>'
									)">Offline Verification</button></td>
								<?php if ($row->response !== "" && $row->status !== "User Aborted") { ?>
									<td><button id="offline-verification" onclick="passvalue_refund(
										'<?php echo $ajaxurl; ?>',
										'<?php echo $row->order_id; ?>',
										'<?php echo $row->worldline_identifier; ?>',
										'<?php echo $date[0]; ?>',
										'<?php echo $row->currency_type; ?>',
										'<?php echo $row->amount; ?>'
										)">Refund</button></td>
								<?php } else { ?>
									<td></td><?php
											} ?>
							</tr> <?php
								} ?>
					</tbody>
				</table>
				<div class="popup" id="popup-1"></div>
			</div>
		</div>
	</body>

	</html>
<?php }

function display_ov_popup_window()
{
	if ($_REQUEST['merchant_txn'] && $_REQUEST['date']) {
		$paynimo_class  = new WP_worldline();
		$merchantTxnRefNumber = $_REQUEST['merchant_txn'];
		$date = $_REQUEST['date'];

		$merchant_code = $paynimo_class->worldline_merchant_code;
		$currency = $paynimo_class->currency_type;

		$request_array = array(
			"merchant" => array(
				"identifier" => $merchant_code
			),
			"transaction" => array(
				"deviceIdentifier" => "S",
				"currency" => $currency,
				"identifier" => $merchantTxnRefNumber,
				"dateTime" => $date,
				"requestType" => "O"
			)
		);
		$refund_data = json_encode($request_array);
		$refund_url = "https://www.paynimo.com/api/paynimoV2.req";
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
		$response = array();
		$response['status_code'] = $response_array->paymentMethod->paymentTransaction->statusCode;
		$response['status_message'] = $response_array->paymentMethod->paymentTransaction->statusMessage;
		$response['identifier'] = $response_array->paymentMethod->paymentTransaction->identifier;
		$response['amount'] = $response_array->paymentMethod->paymentTransaction->amount;
		$response['errorMessage'] = $response_array->paymentMethod->paymentTransaction->errorMessage;
		$response['dateTime'] = $response_array->paymentMethod->paymentTransaction->dateTime;
		$response['merchantTransactionIdentifier'] 	= $response_array->merchantTransactionIdentifier;
		echo json_encode($response);
		wp_die();
	}
}

function display_refund_popup_window()
{
	if ($_REQUEST['order_id'] && $_REQUEST['transaction_id'] && $_REQUEST['date'] && $_REQUEST['currency_type'] && $_REQUEST['amount']) {

		$paynimo_class  = new WP_worldline();
		$order_id = $_REQUEST['order_id'];
		$merchant_code = $paynimo_class->worldline_merchant_code;
		$transaction_id = $_REQUEST['transaction_id'];
		$date = $_REQUEST['date'];
		$currency_type = $_REQUEST['currency_type'];
		$amount = $_REQUEST['amount'];

		$request_array = array(
			"merchant" => array(
				"identifier" => $merchant_code
			),
			"cart" => (object) null,
			"transaction" => array(
				"deviceIdentifier" => "S",
				"amount" => $amount,
				"currency" => $currency_type,
				"dateTime" => $date,
				"token" => $transaction_id,
				"requestType" => "R"
			)
		);

		$refund_data = json_encode($request_array);
		$refund_url = "https://www.paynimo.com/api/paynimoV2.req";
		$options = array(
			'http' => array(
				'method'  => 'POST',
				'content' => $refund_data,
				'header' =>  "Content-Type: application/json\r\n" .
					"Accept: application/json\r\n"
			)
		);

		$context = stream_context_create($options);
		$response_array = json_decode(file_get_contents($refund_url, false, $context));
		$response = array();
		$response['status_code'] = $response_array->paymentMethod->paymentTransaction->statusCode;
		$response['status_message'] = $response_array->paymentMethod->paymentTransaction->statusMessage;
		$response['merchantTransactionIdentifier'] = $response_array->merchantTransactionIdentifier;
		$response['errorMessage'] = $response_array->paymentMethod->paymentTransaction->errorMessage;
		$response['dateTime'] = $response_array->paymentMethod->paymentTransaction->dateTime;
		$response['identifier'] = $response_array->paymentMethod->paymentTransaction->identifier;
		$response['amount'] = $response_array->paymentMethod->paymentTransaction->amount;

		if ($response['status_code'] == '0400') {
			global $wpdb;
			$table_name = $wpdb->prefix . 'worldline_orders_sa';
			$wpdb->update(
				$table_name,
				array(
					'status'	=> 	'Refunded'
				),
				array('order_id' => $order_id)
			);
		}
		echo json_encode($response);
		wp_die();
	}
}
