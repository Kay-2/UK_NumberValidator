<?php
if(isset($_POST) and $_SERVER['REQUEST_METHOD'] === "POST")
{
	$rawText_input = $_POST['phone_num'];
	echo "Original Number: ".$rawText_input."<br/><br/>";

	$charactersOK = checkCharacters($rawText_input); // Check for illegal characters
	if($charactersOK === false)
	{
		echo '<h4 style="color:RED">Invalid characters used</h4>';
		return;
	}
	elseif($charactersOK =="badLen")
	{
		echo "Bad Length";
		return;
	}
	
	$formatValid = validateFormat($rawText_input); // Validates the format of the number against a regular expression	
	
	if($formatValid === true) // if validateFormat returns true, call numverify.com verification API and handle response
	{
		$number = prepNumber($rawText_input); // Prep Number for API, removes spaces, prefixes etc
		$isActive = numVerifyAPI_validation($number); // Call API function
		
		if($isActive === false) // The nubmer is active.
		{
			echo '<h4 style="color:RED;">Inactive UK Mobile Number</h4>';
		}
		else
		{
			echo '<h4 style="color:GREEN;">Active UK Mobile Number</h4>';
		}	
	} // End if formatValid
	else // Invalid format. '$formatValid' contains error message, display it
	{
		echo $formatValid; 
	}
} // End if $_POST isset
//---------------------------------------------------------------------------------------------------------------------->>

function validateFormat($var)
{
	$input = preg_replace('/[^\d\+]/', "", $var); // Replaces all characters but digits and '+'	
	$valid_uk_mobile = '/^(^\+(44)|(44))?(0)?(7[1|3|4|5|7|8|9][0-9]{8})$/'; // RegEx -Opt. '+44' or '44', opt. '0', mandatory 7 followed by 1 digit from 1-9 exlcuding 0,2 and 6, followed by the final 8 digits in range of 0-9
	
	if(preg_match($valid_uk_mobile, $input))
	{
		echo '<h4 style="color:GREEN">Valid UK Mobile Format</h4>';
		return true;	
	}
	else // not a valid UK number, trace the error in the number and return
	{
		echo '<h4 style="color:RED">Invalid UK Mobile Format</h4>';	
		$errorMessage = getErrors($input); // Get exact reason for failure
		return $errorMessage; // return error message to function caller
	}
}// End Function regex_validate
//---------------------------------------------------------------------------------------------------------------------->>

function numVerifyAPI_validation($var)
{
	$number = $var;
	$isActive = false;
	
	$numverify_API_key = '7d389d45d1974f447414fef4db72facb';
	$numverify_API_URL = 'http://apilayer.net/api/validate?access_key='.$numverify_API_key.'&number='.$number.'&country_code=GB&format=1';
	$verificationLink = '<a href="'.$numverify_API_URL.'">Check JSON</a>';
	//echo $verificationLink; // Remove comment tag if you want a link displayed to view the JSON response
	
	$numverify_JSON_response = file_get_contents($numverify_API_URL); // Call to API
	$numverify_content = json_decode($numverify_JSON_response, true); // prep JSON in array
	
	foreach($numverify_content as $key => $value)
	{
		if($key == "carrier") // Check activity by carrier as recommended by API docs
		{
			if(($value != "") || ($value != NULL))
			{
				$isActive = true;	
			}
		}	
	} // End for each

	return $isActive;
}// End function numVerify
//------------------------------------------------------------------------------------------------->>

function checkCharacters($var)
{
	$rawText_input =$var;
	if(preg_match('/[^\+\d\s\-\(\)]/', $rawText_input)) // if match anything but valid characters from start to finish
	{
		return false;
	}
	elseif(preg_match('/^[^\d\+]/', $rawText_input)) // if anything but a digit or a plus is at the start
	{
		return false;
	}
	elseif(strlen($rawText_input) < 10) // If the string is empty or less than 10
	{
		return "badLen";
	}
}
//------------------------------------------------------------------------------------------------->>

function getErrors($var) // traces the error in the number and displays appropriate message
{
	$input = $var;

	if(preg_match('/^(^(\+)|(44))/', $input, $matches))//Check for international dialling prefix +/44
	{
		if(preg_match('/^(^\+44)|(^44)/', $input, $matches)) // Only accept '+44' or '44' else it's a bad format
		{
			$input = preg_replace('/^(^\+44)|(^44)/', "", $input); //Remove international prefix	
		}
		else
		{
			$errorMessage = '<b style="color:red;">Error: Invalid International Code</b>';
			return $errorMessage;
		}
	} // End if internation + used
	
	if(!preg_match('/^(^07)|(^7)/', $input, $matches)) // Check the first digit of number without int. prefix should be either 07 or 7
	{
		$errorMessage = '<b style="color:red;">Error: Invalid Mobile Prefix</b> - Mobile Number should start with 07 or 7';
		return $errorMessage;	
	}
	else // Check if mobile prefix 72 or 76 were used, both are invalid according the numverify
	{
		$input = preg_replace('/^(^07)/', "7", $input); //we know it exists and is valid, Remove start of mobile prefix	'0'

		if(preg_match('/^(^70)|(^72)|(^76)/', $input, $matches)) // Check if invalid 07X numbers exist i.e. 070, 072 and 076
		{
			$errorMessage = '<b style="color:red;">Error: Invalid Mobile Prefix</b> - Mobile Number should not start with 0/70, 0/72 or 0/76';
			return $errorMessage;	
		}	
	} // End Else 076/072 used
	
	if(preg_match('/[\+]/', $input)) // Check if the user added extra + characters
	{
		$errorMessage = '<b style="color:red;">Error: Invalid Use of +<br/>';
		return $errorMessage;
	}

	if(strlen($input) != 10) // Check if the length is 10 digits
	{
		$errorMessage = '<b style="color:red;">Error: Invalid Legth ('.strlen($input).')</b>';
		return $errorMessage;
	}
} // End function getErrors();
//---------------------------------------------------------	

function prepNumber($var)
{
	$rawText_input = $var;
	if(preg_match('/^(^\+44)|(^44)/', $rawText_input)) // If international prefix +/44 is found strip it as the api will fail if used
	{
		$rawText_input = preg_replace('/^(^\+44)|(^44)/', "", $rawText_input); 
	}
	$number = preg_replace('/[^\d]/', "", $rawText_input); // Replaces all characters but digits
	return $number;
}

?>