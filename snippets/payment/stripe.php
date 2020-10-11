<div id="billing">
	<div class="row">
		<div>
			<div id="payForm">
				<div id="errorMessage"></div>
				<div class="loading none"></div>
				<input type="hidden" name="photographerId" value="<?=$data->portal->photographerId?>" />
				<input type="hidden" name="environment" value="<?=ENVIRONMENT?>" />


				<h3>Appointment Details</h3>
				<div class="row">
					<div class="row">
						<div class="field">
							<input class="has-data" type="text" name="fName" value="" required />
							<label for="fName">First Name <span class="required">*</span></label>
							<div role="alert">
								<div rel="fName" class="message hide" message="required">Your first name is required</div>
							</div>
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
					
					<h3>Payment Details</h3>
					<div id="charge-cards" class="row">
						<ul id="card-logos">
							<li id="visa"></li>
							<li id="mastercard"></li>
							<li id="discover"></li>
							<li id="amex"></li>
							<!-- <li id="paypal"></li> -->
						</ul>
					</div>
					
					<div class="field">
						<input type="text" id="cc-amountpaid" name="cc-amountpaid" value="<?=$data->info->paymentAmount?>" readonly="readonly" />
						<label for="ccNumber">Payment Amount<span class="required">*</span></label>
					</div>
								
					<div class="field">
						<input type="text" class="inspectletIgnore" id="cc-number" data-stripe="number" name="number" value="" />
						<label for="ccNumber">Credit Card Number <span class="required">*</span></label>
						<div role="alert">
							<div class="message hide" rel="cc-number">You must enter a valid credit card number</div>
						</div>
					</div>


					<div class="row">
						<div class="split">
							<div class="field">
								<input class="inspectletIgnore" id="exp-month" type="text" size="2" data-stripe="exp-month" value="" />
								<label for="ccExpiration">Month (MM) <span class="required">*</span></label>
								<div role="alert">
									<div class="message hide" rel="exp-month">Invalid month</div>
								</div>
							</div>
						</div>
						<div class="split">
							<div class="field">
								<input class="inspectletIgnore" id="exp-year" type="text" size="4" data-stripe="exp-year" value="" />
								<label for="ccExpiration">Year (YYYY) <span class="required">*</span></label>
								<div role="alert">
									<div class="message hide" rel="exp-year">Invalid year</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="split">
							<div class="field">
								<input class="inspectletIgnore" type="text" id="cc-cvv" value="" name="cvv" data-stripe="cvv" />
								<label for="ccCVV">CVV <span class="required">*</span></label>
								<div role="alert">
									<div class="message hide" rel="cc-cvv">Enter a valid CVV number. Look at the back of your card.</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="button">
						<button id="sTSubmit" type="submit" name="submit">Submit</button>
					</div>
				</div>
			</div>
	
			<div id="processor">
				<img src="<?=APP?>/assets/img/payments/logo-stripe.png" alt="Stripe Logo" />
			</div>
	
		</div>
	</div>
</div>

<script type="text/javascript">
		
		$('#cc-number').payment('formatCardNumber');
		$('#cc-cvc').payment('formatCardCVC');
		
		Stripe.setPublishableKey('<?=$data->info->paymentConfig->publishableKey?>');
		
		var stripeResponseHandler = function(status, response) {
			
			var $form = $('#processInvoicePaymentForm');
			var loader = $(".loading");
			var firstName = $("[name='fName']").val();
			var lastName = $("[name='lName']").val();
			var email = $("[name='email']").val();
			var telephone = $("[name='telephone']").val();
			var errorDiv = $("#errorMessage");
			var totalAmount = $("input[name='cc-amountpaid']").val() * 100;
			

			if (response.error) {
				// Show the errors on the form
				$(errorDiv).html("<div class='alert error'><p>We unfortunately could not approve your payment. <br />Please check the card, expiration and CVV to ensure they are correct.</p></div>");
				$form.find('button').prop('disabled', false);
				
				return; 
				
			} else {
				// token contains id, last4, and card type
				var token = response.id;

				$.ajax({
					url: '<?=API?>/StripeProcessor/oneTimePayment',
					beforeSend: function(){
						$(loader).removeClass("none").addClass("active");
					},
					dataType: 'json',
					method: 'POST',
					data: {
						currency: '<?=$data->portal-> currency?>',
						amountPaid: totalAmount,
						description: '<?=urlencode($data->portal->company." - ".$data->info->title)?>',
						photographerId: <?=$data->portal->photographerId?>,
						environment: '<?=ENVIRONMENT?>',
						source: token,
						stripeToken: token,
					},
					error: function(response){

						$(loader).removeClass("active").addClass("none");
						$(errorDiv).html("<div class='alert error'><p>Unfortunately there was a technical error in processing your transaction. Please try again at a later time.</p></div>");
						$('#sTSubmit').prop('disabled', false);
						
						return;

					},
					success: function(response){

						<? include("snippets/payment/process-appointment.php"); ?>

						$(loader).removeClass("active").addClass("none");
						
						return;

					},
					timeout: 5000
				});

			}
		};

		$("form[name='scheduleAppointment']").on("submit", function(e){

			// e.preventDefault();
			
			var errors = 0;

			var $form = $(this);
			$(".message").addClass("hide");
			var amountPaid = $("#cc-amountpaid").val();
			var ccNumber = $("#cc-number").val();
			var ccMonth = $("#exp-month").val();
			var ccYear = $("#exp-year").val();
			var ccCVV = $("#cc-cvv").val();
			var firstName = $("[name='fName']").val();
			var lastName = $("[name='lName']").val();
			var email = $("[name='email']").val();
			var telephone = $("[name='telephone']").val();

			if (_.isUndefined(email) || _.isNull(email) || email.length < 5) {
				$("[rel='email']").removeClass("hide");
				errors++;
			} else {
				$("[rel='email']").addClass("hide");
			}
	
			if (_.isUndefined(lastName) || _.isNull(lastName) || lastName.length < 1) {
				$("[rel='lName']").removeClass("hide");
				errors++;
			} else {
				$("[rel='lName']").addClass("hide");
			}
	
			if (_.isUndefined(firstName) || _.isNull(firstName) || firstName.length < 1) {
				$("[rel='fName']").removeClass("hide");
				errors++;
			} else {
				$("[rel='fName']").addClass("hide");
			}

			if(_.isNull(amountPaid) || _.isUndefined(amountPaid) || amountPaid.length === 0 || amountPaid == 0){
				$("[rel='cc-amountpaid']").removeClass("hide");
				errors++;
			} else {
				$("[rel='cc-amountpaid']").addClass("hide");
			}

			if(_.isNull(ccNumber) || _.isUndefined(ccNumber) || ccNumber.length === 0 || ccNumber.length > 20){
				$("[rel='cc-number']").removeClass("hide");
				errors++;
			} else {
				$("[rel='cc-number']").addClass("hide");
			}

			if(_.isNull(ccMonth) || _.isUndefined(ccMonth) || ccMonth < 1 || ccMonth > 12){
				$("[rel='exp-month']").removeClass("hide");
				errors++;
			} else {
				$("[rel='exp-month']").addClass("hide");
			}

			if(_.isNull(ccYear) || _.isUndefined(ccYear)){
				$("[rel='exp-year']").removeClass("hide");
				errors++;
			} else {
				$("[rel='exp-year']").addClass("hide");
			}

			if(_.isNull(ccCVV) || _.isUndefined(ccCVV)){
				$("[rel='cc-cvv']").removeClass("hide");
				errors++;
			} else {
				$("[rel='cc-cvv']").addClass("hide");
			}
			
			if(errors === 0){
				$('#sTSubmit').prop('disabled', true);
				
				Stripe.card.createToken($form, stripeResponseHandler);
			}
			
			return false;
		});
		

</script>
