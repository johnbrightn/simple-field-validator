<?php

namespace Jbn\Validate;

 class ValidateFields {

    function __construct(){}


    private static function filterEmail($input)
    {
        $input = filter_var(trim($input), FILTER_SANITIZE_EMAIL);

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return $input;
        } else {
            return false;
        }
    }


        /**
    * @param array $requiredFields array of fields that has to be checked. eg ["username"=>20, "email"=>60] Interger values are the fields' max length
    * @param array $requestObject array of fields submitted by the user. eg  ["username"=>"foo", "password"=>"bar"]
    * @param array $customMessage OPTIONAL speficies custom message to return for each field when validation fails
    * @return array returns array of error true with error message if a field fails the validation or false and the values if all the fields pass the validation
    */
     static function validate(array $requiredFields, array $requestObject, array $customMessage = null) : array
    {
        if (!$requestObject == null) {
            $requestObjectKeys = array_keys($requestObject); //extract all the keys from the array submited by user
            $requiredFieldsKeys = array_keys($requiredFields);
            $newarray = []; //create new array
            foreach ($requiredFields as $requiredFieldsKey => $fieldLength) { //loop through the fields to check
                foreach ($requestObject as $requestObjectKey => $values) { //loop through the fields submitted by the user
                    if (!in_array($requiredFieldsKey, $requestObjectKeys)) { //if a default field set is not in the fields submitted by the user, throw an error
                        if($customMessage != null) //if custom message is not null, return custom message instead
                            return ValidateFields::customMessage($customMessage, $requiredFieldsKey, $requiredFieldsKeys);

                        return ["error" => true, "message" => " " . str_replace('_', " ", $requiredFieldsKey) . " is required"];
                    } elseif (in_array($requiredFieldsKey, $requestObjectKeys) && $requiredFieldsKey == $requestObjectKey) { 
                        
                        //check the field length
                        if(strlen($values) > $fieldLength){
                            if($customMessage != null) //if custom message is not null, return custom message instead
                             return ValidateFields::customMessage($customMessage, $requiredFieldsKey, $requiredFieldsKeys);

                            return ["error" => true, "message" => "" . str_replace('_', " ", $requiredFieldsKey) . " should be ".$fieldLength." characters or less"];
                        }
                        
                        if (empty(trim($values))) {//if the field submitted by the user is empty, prompt the user
                            if($customMessage != null) //if custom message is not null, return custom message instead
                             return ValidateFields::customMessage($customMessage, $requiredFieldsKey, $requiredFieldsKeys);

                            return ["error" => true, "message" => "" . str_replace('_', " ", $requiredFieldsKey) . " should not be empty"];
                        }else{
                            if($requiredFieldsKey == "email"){ //validate email address
                                if(!ValidateFields::filterEmail($values)){
                                    return ["error" => true, "message" => "Invalid email address"];
                                }
                            }
                           // $values = filter_var($values, FILTER_SANITIZE_SPECIAL_CHARS);

                            //create new array with values and their keys
                            $newarray = array_merge($newarray, ["error"=> false, $requiredFieldsKey => $values]);//create new array obj with specified fields and corresponding vaues
                        }
                    }
                }
            }
            return $newarray;
        } else {
            return ["error" => true, "message" => "No field is set"];
        }
    }

    private static function customMessage($customMessage, $requiredFieldsKey, $requiredFieldsKeys){
            $index = array_search($requiredFieldsKey, $requiredFieldsKeys); //get the index of the field
            return ["error" => true, "message" => $customMessage[$index]];
    }
}
