# UK_NumberValidator
Author: Philip Brennan

The index file has as simple form used to post the number to uk_mobileNum_validation.php file.

A basic character and length check are done.
The function 'validateFormat' takes the input and replaces all non digit characters except the prefix character '+'
The preg_match function takes in my own regex pattern. Allows for option +44 or 44, option leading 0, mandatory start of 07x, where x is between 1-9 excluding 070, 072 and 076, as these are not valid mobile numbers.
If the pattern is matched, a result of true is passed back to the calling function.

If it fails to match the valid pattern, the 'getErrors' function is called. This function goes through the number from start to finish and validates each part until the problem is identified at which point the appropriate error message is assigned and returned.
The error message is returned to 'validateFormat' caller.


If the 'validateFormat' function returns true the number is prepared for use with the API, that is the international format and all non-digits are removed, via the 'prepNumber' function

The 'numVerifyAPI_validation' function is called.
You can uncomment the echo $verificationLink statement if you want to view a link to see JSON response

The API URL is passed to the 'file_get_contents' function and JSON response is received and then decoded via the 'json_decode' function.
Looping through the response array, the value for 'carrier' is checked, the API suggests using this field to test if the number is active.
The 'isActive' flag is set to true or false if carrier value set or not, and returned to the function caller.
From there appropriate Number active/inactive message is shown.
and the script is complete
