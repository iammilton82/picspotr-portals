<?

use Underscore\Underscore as _;

class Core {

	public function calculateDateDiff($date1, $date2){
		$time = new stdClass();

		$diff = abs(strtotime($date2) - strtotime($date1));  
  
		// To get the year divide the resultant date into 
		// total seconds in a year (365*60*60*24) 
		$time->years = floor($diff / (365*60*60*24));  
		
		
		// To get the month, subtract it with years and 
		// divide the resultant date into 
		// total seconds in a month (30*60*60*24) 
		$time->months = floor(($diff - $time->years * 365*60*60*24) 
									/ (30*60*60*24));  
		
		
		// To get the day, subtract it with years and  
		// months and divide the resultant date into 
		// total seconds in a days (60*60*24) 
		$time->days = floor(($diff - $time->years * 365*60*60*24 -  
					$time->months*30*60*60*24)/ (60*60*24)); 
		
		
		// To get the hour, subtract it with years,  
		// months & seconds and divide the resultant 
		// date into total seconds in a hours (60*60) 
		$time->hours = floor(($diff - $time->years * 365*60*60*24  
			- $time->months*30*60*60*24 - $time->days*60*60*24) 
										/ (60*60));  
		
		
		// To get the minutes, subtract it with years, 
		// months, seconds and hours and divide the  
		// resultant date into total seconds i.e. 60 
		$time->minutes = floor(($diff - $time->years * 365*60*60*24  
				- $time->months*30*60*60*24 - $time->days*60*60*24  
								- $time->hours*60*60)/ 60);  
		
		
		// To get the minutes, subtract it with years, 
		// months, seconds, hours and minutes  
		$time->seconds = floor(($diff - $time->years * 365*60*60*24  
				- $time->months*30*60*60*24 - $time->days*60*60*24 
						- $time->hours*60*60 - $time->minutes*60));  

		$time->totalHours = floor((strtotime($date2) - strtotime($date1)) / (60*60));

		$time->date1 = $date1;
		$time->date2 = $date2;

		return $time;

	}

	public function determinePortalURL(){
		$host = $_SERVER['HTTP_HOST'];
		$url = explode(".", $host);
		$profile = $url[0];

		if($url[0] === 'www' || !isset($url[0]) || $host === 'clientstud.io'){
			$user = false;
		} else {
			$user = $profile;
		}

		return $user;
	}

    public function console($data){
		$data = json_encode($data);
		echo "<script type='text/javascript'>console.log($data)</script>";
	}

	public function currencies($abbr){
		switch($abbr){
			case 'chf':
				return 'CHF';
			break;
			case 'dkk':
			case 'sek':
				return 'kr';
			break;
			case 'zar':
				return "R";
			break;
			case 'gbp':
				return '£';
			break;
			case 'eur':
				return '€';
			break;
			default:
				return '$';
			break;
		}
	}

	public function invoiceStatus($invoice){
		if(($invoice->paidToDate >= $invoice->total) || $invoice->paid === 1){
			return 'paid';
		} elseif($invoice->balance > 0){
			if($invoice->dueDate < time()){
				if(sizeof($invoice->paymentHistory) > 0 && $invoice->balance > 0){
					return 'past-due';
				} else {
					return 'past-due';
				}
			} else {
				if(sizeof($invoice->paymentHistory) > 0 && $invoice->balance > 0){
					return 'unpaid';
				} else {
					return 'unpaid';
				}
			}
		} else {
			return 'paid';
		}
	}

    public function agreementStatus($agreement){
	    $isSignedByClient = false;
	    $isSignedByPhotographer = false;
	    $status = new stdClass();

	    if($agreement->signature === false){
		    $isSignedByPhotographer = false;
	    } else {
		    $isSignedByPhotographer = true;
	    }

		$c = 0;
		for($i=0; $i<sizeof($agreement->customers);$i++){
			$customer = $agreement->customers[$i];
			if($customer->signature === false){
				$c++;
			}
		}

		if($c === 0){
			$isSignedByClient = true;
		}

		if( ($isSignedByClient === true) && ( $isSignedByPhotographer === true ) ){
			$status->status= 'executed';
			$status->text= 'Signed';
			$status->binding= true;
		} else if ( ($isSignedByClient === true ) && ($isSignedByPhotographer === false) ){
			$status->status= 'psignneeded';
			$status->text= 'Your signature';
			$status->binding= false;
		} else if ( ($isSignedByClient === false ) && ($isSignedByPhotographer === true)) {
			$status->status= 'csignneeded';
			$status->text= 'Client signature';
			$status->binding= false;
		} else {
			$status->status= 'allsignneeded';
			$status->text= 'Not signed';
			$status->binding= false;
		}

	    return $status;

	    $this->console($agreement);
    }

}

class Portal {

    public function getPortalBySubdomain(){

	    Unirest\Request::verifyPeer(false);
	    $url = API."/user/getPortalBySubdomain/".urlencode(SUBDOMAIN);
		$request = Unirest\Request::get($url, array("Accept" => "application/json"));
		
		if($request->code == 200){
			$d = json_decode($request->raw_body);
			
			if($d->status === 1 || $d->status === true){
				$data = $d->data;
			} else {
				$data = false;
			}
		} else {
			$data = false;
		}

		return $data;

	}

	public function customerName($customer){
		if(_::isNull($customer->company)){
			return $customer->fullName;
		} else {
			return $customer->company;
		}
	}

	public function portalAssets($portal){
		$assets = new stdClass();
		if($portal === false){
			$assets->background = "background-color: #000;";
			$assets->primaryColor = "#000;";
		} else {
			if(_::isNull($portal->backgroundImage)){
				if(_::isNull($portal->primaryColor)){
					$assets->background = "background-color: #000;";
					$assets->primaryColor = "#000;";
				} else {
					$assets->background = "background-color: #".$portal->primaryColor.";";
					$assets->primaryColor = $portal->primaryColor.";";
				}
			} else {
				$assets->background = "background-image: url(".AWS."/portal/".$portal->backgroundImage.");";
				$assets->primaryColor = $portal->primaryColor.";";
			}

			if(_::isNull($portal->company)){
				$assets->studioName = $portal->firstName." ".$portal->lastName;
			} else {
				$assets->studioName = $portal->company;
			}
		}
		return $assets;
	}

	public function getAnySlotById($id){
		Unirest\Request::verifyPeer(false);
	    $url = API."/appointments/getAnySlotById/".$id;
	    $headers = array(
	    	"Accept" => "application/json",
	    	"Content-Type" => "application/json"
	    );

		$request = Unirest\Request::get($url, $headers);

		if($request->code == 200){
			$d = json_decode($request->raw_body);

			if($d->status === 1 || $d->status === true){
				$data = $d->data;
			} else {
				$data = false;
			}
		} else {
			$data = false;
		}

		return $data;
	}

	public function getSlotById($id){
		Unirest\Request::verifyPeer(false);
	    $url = API."/appointments/getSlotById/".$id;
	    $headers = array(
	    	"Accept" => "application/json",
	    	"Content-Type" => "application/json"
	    );

		$request = Unirest\Request::get($url, $headers);

		if($request->code == 200){
			$d = json_decode($request->raw_body);

			if($d->status === 1 || $d->status === true){
				$data = $d->data;
			} else {
				$data = false;
			}
		} else {
			$data = false;
		}

		return $data;
	}

	public function getAvailableTimeSlots($id){
		Unirest\Request::verifyPeer(false);
	    $url = API."/appointments/readByRecurringId/".$id;
	    $headers = array(
	    	"Accept" => "application/json",
	    	"Content-Type" => "application/json"
	    );

		$request = Unirest\Request::get($url, $headers);

		if($request->code == 200){
			$d = json_decode($request->raw_body);

			if($d->status === 1 || $d->status === true){
				$data = $d->data;
			} else {
				$data = false;
			}
		} else {
			$data = false;
		}

		return $data;
	}

	public function getAvailableAppointments($userId){
		Unirest\Request::verifyPeer(false);
	    $url = API."/appointments/read/".$userId;
	    $headers = array(
	    	"Accept" => "application/json",
	    	"Content-Type" => "application/json"
	    );

		$request = Unirest\Request::get($url, $headers);

		if($request->code == 200){
			$d = json_decode($request->raw_body);

			if($d->status === 1 || $d->status === true){
				$data = $d->data;
			} else {
				$data = false;
			}
		} else {
			$data = false;
		}

		return $data;
	}

	public function getCustomerOverviewByID($customerId){
		Unirest\Request::verifyPeer(false);
	    $url = API."/customer/getCustomerOverviewWithId/".$customerId;
	    $headers = array(
	    	"Accept" => "application/json",
	    	"Content-Type" => "application/json"
	    );

		$request = Unirest\Request::get($url, $headers);

		if($request->code == 200){
			$d = json_decode($request->raw_body);

			if($d->status === 1 || $d->status === true){
				$data = $d->data;
			} else {
				$data = false;
			}
		} else {
			$data = false;
		}

		return $data;
	}

	public function getEventDetails($eventId){
		Unirest\Request::verifyPeer(false);
		$url = API."/userEvent/getUserEventWithId";

	    $headers = array(
	    	"Accept" => "application/json",
	    	"Content-Type" => "application/json"
	    );

		$query = Unirest\Request\Body::json(array("userEventId" => $eventId));
		$request = Unirest\Request::post($url, $headers, $query);

		if($request->code == 200){
			$d = json_decode($request->raw_body);

			if($d->status === 1 || $d->status === true){
				$data = $d->data;
			} else {
				$data = false;
			}
		} else {
			$data = false;
		}

		return $data;
	}

	public function eventHasMyCustomerId($event, $customerId){
		$check = 0;
		if(sizeof($event->customers[0])>0){
			foreach($event->customers[0] as $customer){
				if((int) $customer->id === (int) $customerId){
					$check++;
				}
			}
			return $check > 0 ? true : false;
		} else {
			return false;
		}
	}

	public function hasLocationAddress($event){
		if(strlen($event->location)>0){
			return true;
		} else if (strlen($event->address1)>0){
			return true;
		} else if (strlen($event->address2)>0){
			return true;
		} else if (strlen($event->city)>0){
			return true;
		} else if (strlen($event->state)>0){
			return true;
		} else if (strlen($event->zipCode)>0){
			return true;
		} else {
			return false;
		}
	}

	public function returnEventTime($event){
		$eventDate = "";
		$today = date('m/d/y', time());

		$start = $event->startDateTime;
		$end = $event->endDateTime;
		$allDay = $event->allDay;

		// check if the event is today
		if($today === date('m/d/y', $start)){
			$eventDate .= "<div class='month'>Today</div>";
		} else {
			$eventDate .= "";
		}
		if($allDay == 1 || $allDay == true){
			$eventDate .= "<div>";
			$eventDate .= date('l, F d, Y', $start);
			if(date('m/d/Y', $start) != date('m/d/Y', $end)){
				$eventDate .= " &mdash; ".date('F d, Y', $end);
			}
			$eventDate .= "<br /> All Day";
			$eventDate .= "</div>";
		} else {
			$eventDate .= "<div>";
			$eventDate .= date('l, F d, Y, g:i a', $start);
			if(date('m/d/Y', $start) != date('m/d/Y', $end)){
				$eventDate .= " &mdash; ".date('F d, Y, g:i a', $end);
			} else {
				$eventDate .= " &mdash; ".date('g:i a', $end);
			}
			$eventDate .= "</div>";
		}

		return $eventDate;

	}

}

class User {
	public function checkAuth($customer){
		if($customer == false || $_COOKIE['user'] === false || !isset($_COOKIE['user']) || _::isNull($_COOKIE['user'])){
			header('Location: index.php?notice=session_timeout');
		}
		return;
	}
}

$core = new Core();
define('SUBDOMAIN', $core->determinePortalURL());
