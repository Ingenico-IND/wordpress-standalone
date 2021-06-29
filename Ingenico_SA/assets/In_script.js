function togglePopup(){
	document.getElementById("popup-1").classList.toggle("active");
}

function passvalue_ov(ajaxurl,merchantref, date){
	var msg = "Offline Verification Successfull";
	$.ajax({
		url: ajaxurl,

		data: {
			'action' 		: 'display_ov_popup_window',
			'merchant_txn'	: merchantref,
			'date'			: date
		},
		contentType: "application/json; charset=utf-8",
		success:function(data){
			Insert_data(data,msg);
		},
		error:function(error){
			console.log("The error is : ",error);
		}
	});
}

function passvalue_refund(ajaxurl, order_id, transaction_id, date, currency_type, amount){
	var msg = "Refund Result";
	$.ajax({
		url : ajaxurl,
		data: {
			'action'			: 	"display_refund_popup_window",
			'order_id'			: 	order_id,
			'transaction_id'	: 	transaction_id,
			'date'				: 	date,
			'currency_type'		: 	currency_type,
			'amount'			: 	amount
		},
		contentType: "application/json; charset=utf-8",
		success:function(data){
			Insert_data(data, msg);
		},
		error:function(error){
		 	console.log("The error is : ",error);
		}
	});
}

function Insert_data(data , msg){
	var OBJ = JSON.parse(data);
	var model_html = `
	<div class="overlay"></div>
	<div class="content">
		<div class="close-btn" onclick="togglePopup()">×</div>
		<h2> ${msg} </h2>
		<table id="popup-table">
			<tr>
				<th id = "left-item">Status Code</th>
				<th> ${OBJ.status_code} </th>
			</tr>
			<tr>
				<th id = "left-item">Merchant Transaction ID</th>
				<th> ${OBJ.merchantTransactionIdentifier} </th>
			</tr>
			<tr>
				<th id = "left-item">Ingenico Transaction ID</th>
				<th> ${OBJ.identifier} </th>
			</tr>
			<tr>
				<th id = "left-item">Amount</th>
				<th> ${OBJ.amount} </th>
			</tr>
			<tr>
				<th id = "left-item">Message</th>
				<th> ${OBJ.errorMessage} </th>
			</tr>
			<tr>
				<th id = "left-item">Status Message</th>
				<th> ${OBJ.status_message ?? "Not Found"}  </th>
			</tr>
			<tr>
				<th id = "left-item">Date Time</th>
				<th> ${OBJ.dateTime}</th>
			</tr>
		</table>	
	</div>`;
	document.getElementById('popup-1').innerHTML = model_html;
	togglePopup();
}