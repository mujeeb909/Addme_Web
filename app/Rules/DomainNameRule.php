<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DomainNameRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (trim($value) == '*') {
            return true;
        }

        $values = explode(',', str_replace(' ', '', trim($value)));
        foreach ($values as $value) {
            $value = trim($value);
            $flag = (preg_match("/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/", $value) //valid chars check
                && preg_match("/^.{1,253}$/", $value) //overall length check
                && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $value)); //length of each label
            if ($flag == false) {
                break;
            }
        }

        return $flag;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Please enter a valid :attribute.";
    }
}
