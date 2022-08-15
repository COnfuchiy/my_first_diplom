<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SiteUrlsTextAreaRule implements Rule, DataAwareRule
{

    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected array $data = [];

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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->data['page_select_type'] === 'manual') {
            $textAreaArray = explode("\n", str_replace("\r", '', $value));
            if (!is_array($textAreaArray)) {
                return false;
            }
            foreach ($textAreaArray as $url) {
                $url = trim($url);
                $validator = Validator::make(
                    compact('url'),
                    [
                        'url' => 'required|url',
                    ]
                );

                if ($validator->fails()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.manual_input_text_area');
    }

    public function setData($data): SiteUrlsTextAreaRule|static
    {
        $this->data = $data;

        return $this;
    }
}
