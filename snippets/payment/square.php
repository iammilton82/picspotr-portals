<?
if($data->info->paymentConfig->version == 2){
	$appId = SQUAREUP_APP;
} else {
	$appId = $data->info->paymentConfig->publishableKey;
}
?>

<script>

	var applicationId = '<?=$appId?>'; // <-- Add your application's ID here

	// You can delete this 'if' statement. It's here to notify you that you need
	// to provide your application ID.
	if (applicationId == '') {
	  alert('You need to provide a value for the applicationId variable.');
	}

	// Initializes the payment form. See the documentation for descriptions of
	// each of these parameters.
	var paymentForm = new SqPaymentForm({
	  applicationId: applicationId,
	  inputClass: 'sq-input',
	  inputStyles: [
		{
		  fontSize: '15px'
		}
	  ],
	  cardNumber: {
		elementId: 'sq-card-number',
		placeholder: 'Card Number'
	  },
	  cvv: {
		elementId: 'sq-cvv',
		placeholder: 'CVV'
	  },
	  expirationDate: {
		elementId: 'sq-expiration-date',
		placeholder: 'Expiration Date (MM/YY)'
	  },
	  postalCode: {
		elementId: 'sq-postal-code',
		placeholder: 'Zip/Postal Code'
	  },
	  callbacks: {

		// Called when the SqPaymentForm completes a request to generate a card
		// nonce, even if the request failed because of an error.
		cardNonceResponseReceived: function(errors, nonce, cardData) {
		  if (errors) {
			console.log("Encountered errors:");

			// This logs all errors encountered during nonce generation to the
			// Javascript console.
			errors.forEach(function(error) {
			  console.log('  ' + error.message);
			});

			(function($){

				var errorDiv = $("#errorMessage");
				$(errorDiv).html("<div class='alert error'><h3>Transaction Error!</h3><p>We unfortunately could not approve your payment. <br />Please check the card, expiration and CVV to ensure they are correct.</p></div>");
				// $form.find('button').prop('disabled', false);

			})(jQuery);

		  // No errors occurred. Extract the card nonce.
		  } else {

			// Delete this line and uncomment the lines below when you're ready
			// to start submitting nonces to your server.
			// var nonce = alert('Nonce received: ' + nonce);

			document.getElementById('card-nonce').value = nonce;
			(function($){

				var loader = $(".loading");
				var totalId = $("#invoiceTotal");
				var payForm = $("#payForm");
				var totalAmount = $("input[name='cc-amountpaid']").val();
				var errorDiv = $("#errors");
				var token = nonce;
				var firstName = $("[name='fName']").val();
				var lastName = $("[name='lName']").val();
				var email = $("[name='email']").val();
				var telephone = $("[name='telephone']").val();
							
				$.ajax({
					url: '<?=API?>/squareProcessor/oneTimePayment',
					beforeSend: function(){
						$(loader).removeClass("none").addClass("active");
						$("#submit-button").prop('disabled', true);
					},
					dataType: 'json',
					method: 'POST',
					data: {
						currency: '<?=$data->portal->currency?>',
						amountPaid: totalAmount,
						description: '<?=urlencode($data->portal->company."-".$data->info->title)?>',
						photographerId: <?=$data->portal->photographerId?>,
						environment: '<?=ENVIRONMENT?>',
						source: nonce,
						squareNonce: nonce,
					},
					error: function(response){

						$(loader).removeClass("active").addClass("none");
						$(errorDiv).html("<div class='alert error'><p>Unfortunately there was a technical error in processing your transaction. Please try again at a later time.</p></div>");
						$("#submit-button").prop('disabled', false);

					},
					success: function(response){
						// record the booked session
						
						<? include("snippets/payment/process-appointment.php"); ?>
						
						// record in the activity log

					},
					timeout: 5000
				});


			})(jQuery);
			// document.getElementById('nonce-form').submit();

		  }
		},

		unsupportedBrowserDetected: function() {
		  // Fill in this callback to alert buyers when their browser is not supported.
		},

		// Fill in these cases to respond to various events that can occur while a
		// buyer is using the payment form.
		inputEventReceived: function(inputEvent) {
		  switch (inputEvent.eventType) {
			case 'focusClassAdded':
			  // Handle as desired
			  break;
			case 'focusClassRemoved':
			  // Handle as desired
			  break;
			case 'errorClassAdded':
			  // Handle as desired
			  break;
			case 'errorClassRemoved':
			  // Handle as desired
			  break;
			case 'cardBrandChanged':
			  // Handle as desired
			  break;
			case 'postalCodeChanged':
			  // Handle as desired
			  break;
		  }
		},

		paymentFormLoaded: function() {
		  // Fill in this callback to perform actions after the payment form is
		  // done loading (such as setting the postal code field programmatically).
		  // paymentForm.setPostalCode('94103');
		}
	  }
	});



// This function is called when a buyer clicks the Submit button on the webpage
// to charge their card.
function requestCardNonce(event) {

	// This prevents the Submit button from submitting its associated form.
	// Instead, clicking the Submit button should tell the SqPaymentForm to generate
	// a card nonce, which the next line does.
	event.preventDefault();
		
	var errors = 0;
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

	if(errors === 0){
		paymentForm.requestCardNonce();
	}
}


</script>


<style type="text/css">
	.sq-input {
	border-bottom: 1px solid rgb(223, 223, 223);
	outline-offset: -2px;
	margin-bottom: 5px;
	padding: 5px 2px 4px;
	height: 40px;
	}
	.sq-input--focus {
	/* Indicates how form inputs should appear when they have focus */
	outline: 5px auto rgb(59, 153, 252);
	}
	.sq-input--error {
	/* Indicates how form inputs should appear when they contain invalid values */
	outline: 5px auto rgb(255, 97, 97);
	}
</style>
<div id="billing">
	
	<div id="payment" class="row">
		<div>
			<div id="payForm">
				
				<div id="errorMessage"></div>
				<div class="loading none"></div>
				<input type="hidden" name="photographerId" value="<?=$data->portal->photographerId?>" />
				<input type="hidden" name="environment" value="<?=ENVIRONMENT?>" />
				<input type="hidden" id="card-nonce" name="nonce">
				
				<div id="errors"></div>

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
		
					<div class="row">
						<div class="inspectletIgnore" id="sq-card-number"></div>
					</div>
					<div class="row">
						<div class="inspectletIgnore" id="sq-expiration-date"></div>
					</div>
		
					<div class="row">
						<div class="inspectletIgnore" id="sq-cvv"></div>
					</div>
					<div class="row">
						<div id="sq-postal-code"></div>
					</div>
		
		
	
	
					<div class="row">
						<div class="actions">
							<div class="action-button big">
								<input type="submit" class="big" id="submit-button" onclick="requestCardNonce(event)" value="Submit" />
							</div>
						</div>
					</div>
		
				</div>
			</div>
	
			<div id="processor">
				<img src="<?=APP?>/assets/img/pay-square.png" alt="Square Logo" />
			</div>
	
		</div>
	</div>

</div>