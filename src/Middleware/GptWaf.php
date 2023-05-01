<?php

namespace Motikan2010\GptWaf\Middleware;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

define('GPT3_COMPLETIONS_ENDPOINT', 'https://api.openai.com/v1/chat/completions');
define('GPT3_MODEL', 'gpt-4');
define('MAX_TOKENS', 3000);
define('GENERATE_COMPLETIONS_COUNT', 1);
define('STOP', null);
define('TEMPERATURE', 0.5);

class GptWaf
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( !env('GPT_WAF_ENABLED', false) ) {
            return $next($request);
        }

        $questionText = env('GPT_WAF_QUESTION', "Begin your answer with 'Yes' or 'No'.\nIs the following HTTP request a cyber attack?(Beware of false positives.)\n-----\n");
        $systemRole = env('GPT_WAF_SYSTEM_ROLE', 'You are Security engineer.');

        $rawHttpRequest = $this->getRawRequest();
        $openAiKey = env('GPT_WAF_OPEN_AI_API_KEY');
        $debugFlag = env('GPT_WAF_DEBUG_MODE', false);
        if ( $this->isAttack($questionText, $systemRole, $rawHttpRequest, $openAiKey, $debugFlag) ) {
            return $this->genBlockResponse(env('GPT_WAF_BLOCK_STATUS_CODE', 403));
        }

        return $next($request);
    }

    private function getRawRequest(): string
    {
        $http_request = new http_request(false);
        return $http_request->raw();
    }

    private function isAttack(string $questionText, string $systemRole, string $rawHttpRequest, string $apiKey, bool $debug): bool
    {
        $questionText .= $rawHttpRequest;

        $client = new Client();
        $response = $client->request('POST', GPT3_COMPLETIONS_ENDPOINT, [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '. $apiKey,
            ],
            'json' => [
                'model'         => GPT3_MODEL,
                'messages'         => [
                    ['role' => 'system', 'content' => $systemRole],
                    ['role' => 'user', 'content' => $questionText]
                ],
                'max_tokens'    => MAX_TOKENS,
                'n'             => GENERATE_COMPLETIONS_COUNT,
                'stop'          => STOP,
                'temperature'   => TEMPERATURE,
            ],
            'debug' => false,
        ]);

        // Get answer from GPT
        $gptResultMessage = json_decode($response->getBody()->getContents(), true)['choices'][0]['message']['content'];

        // Output log
        if ( $debug ) {
            Log::info($questionText);
            Log::info($gptResultMessage);
        }

        // If the answer begins with "Yes", it is judged to be an attack.
        if ( str_starts_with(str_replace(array("\r\n", "\r", "\n"), '', $gptResultMessage), 'Yes') ) {
            return true;
        } else {
            return false;
        }
    }

    public function genBlockResponse(int $statusCode)
    {
        return  response('Forbidden.', $statusCode);
    }
}

/**
 * Raw HTTP Request Class
 *
 * Reference: https://stackoverflow.com/questions/23446989/get-the-raw-request-using-php
 */
class http_request {

    public $add_headers = [];

    function __construct($add_headers = false) {

        $this->retrieve_headers($add_headers);
        $this->body = @file_get_contents('php://input');
    }

    function retrieve_headers($add_headers = false) {

        if ($add_headers) {
            $this->add_headers = array_merge($this->add_headers, $add_headers);
        }

        if (isset($_SERVER['HTTP_METHOD'])) {
            $this->method = $_SERVER['HTTP_METHOD'];
            unset($_SERVER['HTTP_METHOD']);
        } else {
            $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;
        }
        $this->protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : false;
        $this->request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;

        $this->headers = array();
        foreach($_SERVER as $i=>$val) {
            if (strpos($i, 'HTTP_') === 0 || in_array($i, $this->add_headers)) {
                $name = str_replace(array('HTTP_', '_'), array('', '-'), $i);
                if ( $name === 'COOKIE' ) {
                    continue;
                }
                $this->headers[$name] = $val;
            }
        }
    }

    function method() {
        return $this->method;
    }

    function body() {
        return $this->body;
    }

    function header($name) {
        $name = strtoupper($name);
        return isset($this->headers[$name]) ? $this->headers[$name] : false;
    }

    function headers() {
        return $this->headers;
    }

    function raw($refresh = false) {
        if (isset($this->raw) && !$refresh) {
            return $this->raw; // return cached
        }
        $headers = $this->headers();
        $this->raw = "{$this->method} {$_SERVER['REQUEST_URI']} {$this->protocol}\r\n";

        foreach($headers as $i=>$header) {
            $this->raw .= "$i: $header\r\n";
        }
        $this->raw .= "\r\n{$this->body}";
        return $this->raw;
    }

}
