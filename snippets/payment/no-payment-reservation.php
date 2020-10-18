<p>Enter your details below:</p>

<div class="row">
	<div class="field">
		<input class="has-data" type="text" name="fName" value="" required />
		<label for="fName">First Name <span class="required">*</span></label>
	</div>
</div>
<div class="row">
	<div class="field">
		<input class="has-data" type="text" name="lName" value="" required />
		<label for="lName">Last Name <span class="required">*</span></label>
		<div role="alert">
			<div rel="lName" class="message hide" message="required">Your last name is required</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="field">
		<input class="has-data" type="text" name="email" value="" required />
		<label for="emailAddress">E-mail Address <span class="required">*</span></label>
		<div role="alert">
			<div rel="email" class="message hide" message="required">Your email address is required</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="field">
		<input class="has-data" type="text" name="telephone" value="" />
		<label for="telephone">Telephone #</label>
	</div>
</div>
<div class="action-button">
	<button class="enableOnInput" type="submit">Schedule Appointment</button>
	<div role="alert">
		<div rel="system" class="message hide" message="required">A system error occurred and we could not save your appointment. Send an email to <?= $data->portal->emailAddress ?> for assistance.</div>
		<div rel="saving" class="message hide" message="required">An error occurred and we could not reserve this appointment. Send an email to <?= $data->portal->emailAddress ?> for assistance.</div>
	</div>
</div>

<script type="text/javascript">
	$("[name='scheduleAppointment']").on("submit", function(e) {

		e.preventDefault();

		$('.enableOnInput').prop('disabled', true);

		var form = $(this).serializeArray();
		var errors = 0;

		if (_.isUndefined(form[2].value) || _.isNull(form[2].value) || form[2].value.length < 5) {
			$("[rel='email']").removeClass("hide");
			errors++;
		} else {
			$("[rel='email']").addClass("hide");
		}

		if (_.isUndefined(form[1].value) || _.isNull(form[1].value) || form[1].value.length < 1) {
			$("[rel='lName']").removeClass("hide");
			errors++;
		} else {
			$("[rel='lName']").addClass("hide");
		}

		if (_.isUndefined(form[0].value) || _.isNull(form[0].value) || form[0].value.length < 1) {
			$("[rel='fName']").removeClass("hide");
			errors++;
		} else {
			$("[rel='fName']").addClass("hide");
		}

		if (errors === 0) {
			var appointment = {};
			var startDateTime = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').unix();
			var endDateTime = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').add(<?= $data->info->duration ?>, '<?= $data->info->durationType ?>').unix();

			appointment.emailAddress = form[2].value;
			appointment.firstName = form[0].value;
			appointment.lastName = form[1].value;
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
			appointment.workflowTemplateId = <?= $data->info->workflowTemplateId ?>;

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
					
					
					// **************  add to alert log
					var alert = {};
					alert.userId = appointment.photographerId;
					alert.documentId = response.data.id;
					alert.contentType = 'event';
					alert.itemRead = 0;
					alert.deleted = 0;
					alert.type = 'action';
					alert.content = appointment.firstName+" "+appointment.lastName+" has booked <?=$data->info->title?> for "+appointment.startDate+".";
					
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
					});
					
					

					<? if($reschedule == 1){ ?>
					var confirmationURL = "/reserved?i=<?= $data->info->block->id ?>&r=<?= $data->info->recurringId ?>&reschedule=1";
					<? } else { ?>
					var confirmationURL = "/reserved?i=<?= $data->info->block->id ?>&r=<?= $data->info->recurringId ?>";
					<? } ?>

					window.location.replace(confirmationURL);
				} else {
					$("[rel='saving']").removeClass("hide");
					$('.enableOnInput').prop('disabled', false);
				}
			}).fail(function() {
				$("[rel='system']").removeClass("hide");
				$('.enableOnInput').prop('disabled', false);
			});

		} else {
			$('.enableOnInput').prop('disabled', false);
		}

		return false;
	});
</script>