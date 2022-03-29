<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Traits;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;

trait HandleExceptionTrait
{
    use LoggingTrait;

    /**
     * Handle error threw in catch block
     * Simple call this function: $this->handleException($e);
     * @param Exception $e
     * @param object|null $request
     * @param array $logs
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleException(
        Exception $e,
        object $request = null,
        array $logs = []
    ) {
        $logChannel = 'errorlog';
        // Collect basic information of request like uri, ip, session, user info,...
        $this->collectRequestInformation($request, $logs);
        $this->addErrorLogInformation($e, $logs);
        $this->writeLogToLoggingChannel($logChannel, $logs);
        $code = $e->getCode();
        if ($code >= 500 || $code == 0) {
            $this->alertChatWork($logs['error']);
        }
    }

    protected function collectRequestInformation($request, array &$logs)
    {
        if (!isset($logs['request'])) {
            $logs['request'] = $this->collectBaseInformation($request);
        }
    }

    protected function addErrorLogInformation(Exception $e, array &$logs)
    {
        $logs['error'] = [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $this->traceFewLastAction($e, 4),
        ];
    }

    protected function traceFewLastAction($e, $stepNum) : array
    {
        try {
            return $this->minimizeTraces($e->getTrace(), $stepNum);
        } catch (Exception $error) {
            return ["Trace error"];
        }
    }

    protected function minimizeTraces(array $traces, $step = -1) : array
    {
        $result = [];
        foreach ($traces as $trace) {
            if (count($result) == $step) {
                break;
            }
            $fileOrClassName = '';
            $isSystemCall    = true;
            if (isset($trace['class']) && $this->isNotSystemName($trace['class'])) {
                $fileOrClassName = $this->getClassName($trace['class']);
                $isSystemCall = false;
            }
            if (isset($trace['file']) && $this->isNotSystemName($trace['file'])) {
                $fileOrClassName = $this->getFileName($trace['file']);
                $isSystemCall = false;
            }
            if (isset($trace['function']) && $this->isNotAnonymousFunction($trace['function']) && !$isSystemCall) {
                $result[] = $fileOrClassName . "." . $trace['function'] . "()";
            }
            // Example of one item: 'AuthController.login()'
        }
        return $result;
    }

    protected function isNotSystemName($string) : bool
    {
        return str_contains($string, 'App\\') || str_contains($string, 'App\/') || str_contains($string, 'App/');
    }

    protected function isNotAnonymousFunction($string) : bool
    {
        return !str_contains($string, '{closure}');
    }

    protected function detectSeparator($string) : string
    {
        if (str_contains($string, '\\')) {
            return '\\';
        }
        if (str_contains($string, '\/')) {
            return '\/';
        }
        if (str_contains($string, '/')) {
            return '/';
        }
        return '';
    }

    protected function getClassName(string $string)
    {
        $arrayDir = explode($this->detectSeparator($string), $string);
        return end($arrayDir);
    }

    protected function getFileName(string $string)
    {
        $arrayDir = explode($this->detectSeparator($string), $string);
        $file     = end($arrayDir);
        $arrFileAndExtension = explode('.', $file);
        return reset($arrFileAndExtension);
    }

    /**
     * Zip log and write to logging channel
     * 1: Zip log with json_encode function
     * 2: Write log to logging channel
     *
     * @param string $channel
     * @param array $logs
     */
    protected function writeLogToLoggingChannel(string $channel, array $logs)
    {
        Log::channel($channel)->error(json_encode($logs));
    }

    protected function generateStringWrapWithInfoTag(string $string) : string
    {
        return "[info]{$string}[/info]";
    }

    /**
     * @param $params
     * @return bool|Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function alertChatWork($params)
    {
        $baseUrl = config('settings.chatwork.url');
        if (empty($baseUrl)) {
            return false;
        }
        $url = $baseUrl . '/' . config('settings.chatwork.room') . '/messages';
        $env = strtoupper(config('app.env'));
        $content = "ENVIRONMENT: {$env}\n\n";
        foreach ($params as $key => $value) {
            $errorInfo = is_array($value) ? json_encode($value) : $value;
            $content .= "{$key}: {$errorInfo}\n";
        }

        $msg = "[toall]\n[info][title](gogo)-- API ERROR --(gogo)[/title]{$content}[/info]";
        try {
            $client = new Client(
                ['headers' => ['X-ChatWorkToken' => config('settings.chatwork.token')]]
            );
            $client->request('POST', $url, [
                'form_params' => [
                    'body' => $msg
                ]
            ]);
        } catch (\Exception $e) {
            return $e;
        }
        return true;
    }
}
