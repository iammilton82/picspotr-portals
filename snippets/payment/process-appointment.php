if(response.status === 1 ||  response.status === true){
			
	var payment = response.data;
	
	var appointment = {};
	var startDateTime = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').unix();
	var endDateTime = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').add(<?= $data->info->duration ?>, '<?= $data->info->durationType ?>').unix();

	appointment.emailAddress = email;
	appointment.firstName = firstName;
	appointment.lastName = lastName;
	appointment.scheduleId = <?= $data->info->block->id ?>;
	appointment.photographerId = <?= $data->info->userId ?>;
	appointment.recurringId = '<?= $data->info->recurringId ?>';
	appointment.customerId = <?= $_COOKIE['user'] ? $_COOKIE['user'] : 0 ?>;
	appointment.startDate = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').format("YYYY-MM-DD HH:mm:ss");
	appointment.endDate = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').add(<?= $data->info->duration ?>, '<?= $data->info->durationType ?>').format("YYYY-MM-DD HH:mm:ss");
	appointment.startDateTime = startDateTime;
	appointment.endDateTime = endDateTime;
	appointment.startDate1 = moment.unix(startDateTime).format("YYYY-MM-DD HH:mm:ss");
	appointment.endDate1 = moment.unix(endDateTime).format("YYYY-MM-DD HH:mm:ss");
	appointment.workflowTemplateId = <?= $data->info->workflowTemplateId ? $data->info->workflowTemplateId : 0 ?>;

	<? if($reschedule == 1){ ?>
	var postURL = "<?=API?>/appointments/reschedule/<?=$data->info->userId?>/<?=$oldCustomerId?>/<?=$oldEventId?>/<?=$oldTimeSlotId?>";
	<? } else { ?>
	var postURL = "<?=API?>/appointments/reserve";
	<? } ?>

	$.ajax({
		url: postURL,
		method: "POST",
		data: appointment,
		dataType: 'json',
		async: false,
		crossDomain: true,
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
			'X-ACCESS_TOKEN': '1403b8cc3cdaf3f01361daefeeb8c182adcb6286',
			'TimezoneOffset': new Date().getTimezoneOffset()
		}
	}).done(function(response) {

		if (response.status === 1 || response.status === true) {
			
			var theReservation = response.data;
			
			// update the payment with the customer and event ID
			payment.customerId = theReservation.customers[0].id;
			payment.description = "Payment for <?=$data->info->title?>";
			
			$.ajax({
				url: "<?=API?>/Payment/save",
				method: "POST",
				data: payment,
				dataType: 'json',
				async: false,
				crossDomain: true,
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
					'X-ACCESS_TOKEN': '1403b8cc3cdaf3f01361daefeeb8c182adcb6286',
					'TimezoneOffset': new Date().getTimezoneOffset()
				}
			});
			
			// **************  add to alert log
			var alert = {};
			alert.userId = appointment.photographerId;
			alert.documentId = response.data.id;
			alert.contentType = 'event';
			alert.itemRead = 0;
			alert.deleted = 0;
			alert.type = 'action';
			alert.content = appointment.firstName+" "+appointment.lastName+" has booked <?=$data->info->title?> for "+appointment.startDate+" and paid the session fee.";
			
			$.ajax({
				url: "<?=API?>/v2/public/alerts/save",
				method: "POST",
				data: alert,
				dataType: 'json',
				async: false,
				crossDomain: true,
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
					'X-ACCESS_TOKEN': '1403b8cc3cdaf3f01361daefeeb8c182adcb6286',
					'TimezoneOffset': new Date().getTimezoneOffset()
				}
			}).done(function(response){
				
				<? if($reschedule == 1){ ?>
				var confirmationURL = "/reserved?i=<?= $data->info->block->id ?>&r=<?= $data->info->recurringId ?>&reschedule=1";
				<? } else { ?>
				var confirmationURL = "/reserved?i=<?= $data->info->block->id ?>&r=<?= $data->info->recurringId ?>";
				<? } ?>

				window.location.replace(confirmationURL);
				
			}).fail(function(){
				
				$(errorDiv).html("<div class='alert error'><p>Your payment was processed and your appointment has been booked but a system error occurred.  Please contact technical support.</p></div>");
				$('#sTSubmit').prop('disabled', false);
				
			});
			
			
		} else {
			$("[rel='saving']").removeClass("hide");
			$('#sTSubmit').prop('disabled', false);
		}
	}).fail(function() {
		$("[rel='system']").removeClass("hide");
		$('#sTSubmit').prop('disabled', false);
	});
	
} else {
	$(errorDiv).html("<div class='alert error'><p>We unfortunately could not approve your payment. <br />Please check the card, expiration and CVV to ensure they are correct.</p></div>");
	$(loader).removeClass("active").addClass("none");
	$('#sTSubmit').prop('disabled', false);
}