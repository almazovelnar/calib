<?php

namespace App\Http\Middleware;

use App\Interfaces\BaseInterface;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class APIMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return mixed
     */
    public function handle(Request $request, Closure $next)  {
//        if(!$request->hasHeader('X-Caliber-Token')) {
//            throw new AuthorizationException('Unauthorized.', 401);
//        }

        $limit = false;
        $page  = false;

        $trace         = NULL;
        $error_message = NULL;
        $body          = NULL;
        $time          = microtime(TRUE) - LARAVEL_START;
        $isProd        = app()->environment('production');
        $errors        = NULL;
        $message       = NULL;

        /** @var $response \Illuminate\Http\Response */
        $response = $next($request);

        $originalContent = json_decode($response->getContent(), TRUE);

        if(!$isProd) {
            if(!empty($originalContent['trace'])) {
                $trace = $originalContent['trace'];
            }
            if(!empty($originalContent['error_message'])) {
                $error_message = $originalContent['error_message'];
            }
        }

        if(!empty($originalContent['errors'])) {
            $errors = $originalContent['errors'];
        }

        if(!empty($originalContent['message'])) {
            $message = $originalContent['message'];
        }

        unset($originalContent['trace']);
        unset($originalContent['error_message']);
        unset($originalContent['errors']);
        unset($originalContent['message']);

        if(empty($trace)) {
            $body = $originalContent;
        }

        if(!empty($errors)) {
            $body = $errors;
        }

        $total = $current = 0;

        if(is_countable($body)) {
            if($count = count($body) == 0) {
                if($request->missing('limit') && $request->missing('page')) {
                    $body = [];
                }
            }else {
                if(is_numeric($limit) && is_numeric($page)) {
                    if(!$body instanceof Collection) {
                        $body = collect($body);
                    }

                    $total = $body->count();

                    $body    = $body->forPage($page, $limit)->values();
                    $current = $body->count();
                }
            }
        }

        $responseBody = [
            'body'    => $body,
            'code'    => $response->status(),
            'message' => $message ?? $error_message ?? $this->getMessage($response->status()),
            'status'  => in_array($response->status(), [200, 201]) ? 'success' : 'danger',
            'time'    => round($time * 1000, 2) . "ms",
            'ref'     => $uuid = Str::uuid(),
            'trace'   => $trace,
        ];

        if($limit && $page && $total > 0) {
            $responseBody['pagination'] = [
                'total'   => [
                    'item' => $total,
                    'page' => ceil($total / $limit),
                ],
                'current' => [
                    'item' => $current,
                    'page' => (int) $page,
                ],
            ];
        }

        if(app()->environment() === 'production') {
            unset($responseBody['trace']);
            unset($responseBody['time']);
            unset($responseBody['ref']);
        }

//        if($request->hasHeader('X-Caliber-Token')) {
//            $this->convertFieldsToString($responseBody);
//        }

        $response->setContent(json_encode($responseBody, JSON_OBJECT_AS_ARRAY));

        $response->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        $response->setStatusCode($response->status());
        http_response_code($response->status());

        return $response;
    }

    private function getMessage(int $code) : string {
        switch ($code) {
            case 100:
                return 'Continue.';
            case 101:
                return 'Switching Protocols.';
            case 200:
                return 'OK.';
            case 201:
                return 'Created.';
            case 202:
                return 'Accepted.';
            case 203:
                return 'Non-Authoritative Information.';
            case 204:
                return 'No Content.';
            case 205:
                return 'Reset Content.';
            case 206:
                return 'Partial Content.';
            case 300:
                return 'Multiple Choices.';
            case 301:
                return 'Moved Permanently.';
            case 302:
                return 'Moved Temporarily.';
            case 303:
                return 'See Other.';
            case 304:
                return 'Not Modified.';
            case 305:
                return 'Use Proxy.';
            case 400:
                return 'Bad Request.';
            case 401:
                return 'Unauthorized.';
            case 402:
                return 'Payment Required.';
            case 403:
                return 'Forbidden.';
            case 404:
                return 'Not Found.';
            case 405:
                return 'Method Not Allowed.';
            case 406:
                return 'Not Acceptable.';
            case 407:
                return 'Proxy Authentication Required.';
            case 408:
                return 'Request Time-out.';
            case 409:
                return 'Conflict.';
            case 410:
                return 'Gone.';
            case 411:
                return 'Length Required.';
            case 412:
                return 'Precondition Failed.';
            case 413:
                return 'Request Entity Too Large.';
            case 414:
                return 'Request-URI Too Large.';
            case 415:
                return 'Unsupported Media Type.';
            case 422:
                return 'Unprocessable Entity.';
            case 500:
                return 'Internal Server Error.';
            case 501:
                return 'Not Implemented.';
            case 502:
                return 'Bad Gateway.';
            case 503:
                return 'Service Unavailable.';
            case 504:
                return 'Gateway Time-out.';
            case 505:
                return 'HTTP Version not supported.';
            default:
                return "Unknown HTTP status code.";
        }
    }

    /**
     * Convert all response fields to string
     * for specific Client requirements (for
     * example IOS Applications)
     *
     * @param $array
     */
    public function convertFieldsToString(&$array) {
        foreach($array as $index => &$field) {
            if(is_array($field)) {
                $this->convertFieldsToString($field);
                continue;
            }

            if(is_int($field)) {
                $field = (string) $field;
                continue;
            }

            if(is_float($field)) {
                $field = (string) $field;
                continue;
            }

            if(is_bool($field)) {
                $field = (string) (int) $field;
                continue;
            }
        }
    }

}
