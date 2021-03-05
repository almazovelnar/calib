<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ReCaptcha implements Rule {

    private $token;

    /**
     * Create a new rule instance.
     *
     * @param $_token
     */
    public function __construct($_token) {
        $this->token = $_token;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() : string {
        return __('auth.recaptcha');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value) : bool {
//        if(app()->environment('testing') || app()->environment('local')) {
//            return TRUE;
//        }

        $post_data = [
            'secret'   => config('auth.recaptcha.secret'),
            'response' => $value,
            'remoteip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ];

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($post_data),
            ],
        ];

        $context  = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', FALSE, $context);
        $result   = json_decode($response);

        if($result->success && ( $result->action === $this->token )) {
            if($result->score < 0.5) {
                return FALSE;
            }

            return TRUE;
        }

        return FALSE;
    }

}
