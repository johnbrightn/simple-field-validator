# simple-field-validator

#### Installing ? using composer
```composer
composer require johnbrightn/simple-field-validator:dev-master
```
If you get an error of no class found, run the following command to optimize autoload
```composer
composer dump

composer dump -o
```

#### Usage
Mostly used with API where request objects are received as array.
```php
require_once __FILE__ . '/vendor/autoload.php';

use Jbn\Validate\ValidateFields;


$app->post('/register', function (Request $request, Response $response) {

    $userInputs = $request->getParsedBody(); //object received from user/frontend
    
    //if there are optional fields, that do not need validation
    if(isset($request->getParsedBody()['middle_name']));
        $middle_name = $request->getParsedBody()['middle_name'];
        
    //specified required fields, key=>maxLength. value is the maximum required length of every field/key
    $requiredFields = ["first_name"=>25, "last_name"=>25, email=>60, "username"=>15, "password"=>20];
    
    $value = ValidateFields::validate($requiredFields, $userInputs);
   if ($value["error"]) {
        return $response->withJson($value);
    } else {
        //$username = $value["username"];
        $value = array_merge($value, $middle_name); //merge the optional fields to the required fields
        
        $result = $db->registerUser($value);
        
        return $response->withJson($result);
    }
});

```

If validation fails, array returned is
```php
//if all required fields are not present
["error"=>true, "message"=> "No field is set"];

//if a required field is empty
["error"=>true, "message"=> "$field_name should not be empty"];

//if a required field has length more than specified
["error"=>true, "message"=> "$field_name should be $maxLength characters or less"];

//email validation. if email address is present, field should be specified as 'email',
["error"=>true, "message"=> "Invalid email address"];

```

If validation succeeds, returns all the fields together with their values
```php
["error"=>false, $all_fields=>$all_values];

```

You can specify your custom messages when field length is longer than the specified one or is empty in the third parameter
```php
    $userInputs = $request->getParsedBody();
    
    //specified required fields, key=>maxLength. value is the maximum required length of every field/key
    $requiredFields = ["first_name"=>25, "last_name"=>25, "username"=>15, "password"=>20];
    
    $customMessage = ["Oops! First Name should not be blank and not more than 25 characters", "Enter your last name and not more than 25 characters", "Enter a username of 15 characters or less", "Password should not exceed 20 characters"];
    
    $value = ValidateFields::validate($requiredFields, $userInputs, $customMessage);

```
