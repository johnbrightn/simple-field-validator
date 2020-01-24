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
    * @param array $defaultFields array of fields that has to be checked.
    * @param array $fieldsFromUser array of fields submitted by the user
    * @param array $customMessage OPTIONAL speficies custom message to return for each field when validation fails
    * @return array returns array of error true with error message if a field fails the validation or false and the values if all the fields pass the validation
    */
     static function validate(array $defaultFields, array $fieldsFromUser, array $customMessage = null) : array
    {
        if (!$fieldsFromUser == null) {
            $fieldsFromUserKeys = array_keys($fieldsFromUser); //extract all the keys from the array submited by user
            $defaultFieldsKeys = array_keys($defaultFields);
            $newarray = []; //create new array
            foreach ($defaultFields as $fieldsKey => $fieldLength) { //loop through the fields to check
                foreach ($fieldsFromUser as $firstnameKey => $values) { //loop through the fields submitted by the user
                    if (!in_array($fieldsKey, $fieldsFromUserKeys)) { //if a default field set is not in the fields submitted by the user, throw an error
                        return ["error" => true, "message" => " " . str_replace('_', " ", $fieldsKey) . " is required"];
                    } elseif (in_array($fieldsKey, $fieldsFromUserKeys) && $fieldsKey == $firstnameKey) { 
                        
                        //check the field length
                        if(strlen($values) > $fieldLength){
                            if($customMessage != null) //if custom message is not null, return custom message instead
                             return ValidateFields::customMessage($customMessage, $fieldsKey, $defaultFieldsKeys);

                            return ["error" => true, "message" => "" . str_replace('_', " ", $fieldsKey) . " should be ".$fieldLength." characters or less"];
                        }
                        
                        if (empty(trim($values))) {//if the field submitted by the user is empty, prompt the user
                            if($customMessage != null) //if custom message is not null, return custom message instead
                             return ValidateFields::customMessage($customMessage, $fieldsKey, $defaultFieldsKeys);

                            return ["error" => true, "message" => "" . str_replace('_', " ", $fieldsKey) . " should not be empty"];
                        }else{
                            if($fieldsKey == "email"){ //validate email address
                                if(!ValidateFields::filterEmail($values)){
                                    return ["error" => true, "message" => "Invalid email address"];
                                }
                            }
                            //create new array with values and their keys
                            $newarray = array_merge($newarray, ["error"=> false, $fieldsKey => $values]);//create new array obj with specified fields and corresponding vaues
                        }
                    }
                }
            }
            return $newarray;
        } else {
            return ["error" => true, "message" => "No field is set"];
        }
    }

    private static function customMessage($customMessage, $fieldsKey, $defaultFieldsKeys){

            $index = array_search($fieldsKey, $defaultFieldsKeys); //get the index of the field
            return ["error" => true, "message" => $customMessage[$index]];
    }
}
