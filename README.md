A soloution to an exercise given to a candidate for the position of front end developer. 

EXERCISE: UK Mobile Number Validation.
DESC: Write a PHP script with functions that perform the 
REQUIREMENTS:

1- Accepts a single user entered free text input parameter as 'phone_num'
2- Validates using a regular expression whether it is a valid UK phone number format
3- Calls an API (https://numverify.com/) to validate if this is an active UK mobile number
4- Returns PASS/FAIL message to the function caller with appropriate error messages

-----------------------------------------------------------------------------------------------------------
Solution Explained:

The index file has as simple form used to post the number to uk_mobileNum_validation.php file.

A basic character and length check are done.
The function 'validateFormat' takes the input and replaces all non digit characters except the prefix character '+'
The preg_match function takes in my own regex pattern. Allows for option +44 or 44, optional leading 0, mandatory start of 07x, where x is between 1-9 excluding 0, 2 and 6, as these are not valid mobile numbers.
If the pattern is matched, a result of true is passed back to the calling function.

If it fails to match the valid pattern, the 'getErrors' function is called. This function goes through the number and validates each part until the problem is identified, at which point the appropriate error message is assigned and returned.
The error message is returned to 'validateFormat' caller.


If the 'validateFormat' function returns true the number is prepared for use with the API, that is the international format and all non-digits are removed, via the 'prepNumber' function

The 'numVerifyAPI_validation' function is called.
You can uncomment the echo $verificationLink statement if you want to view a link to see JSON response

The API URL is passed to the 'file_get_contents' function and JSON response is received and then decoded via the 'json_decode' function.
Looping through the response array, the value for 'carrier' is checked, the API suggests using this field to test if the number is active.
The 'isActive' flag is set to true or false if carrier value set or not, and the appropriate Number active/inactive message is shown.
End of script.
