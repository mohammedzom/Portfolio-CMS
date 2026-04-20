<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiException extends Exception
{
    protected $httpCode;

    protected $details;

    protected $internalCode;

    public function __construct($message, $details = null, $httpCode = 400, $internalCode = null)
    {
        parent::__construct($message);

        $this->details = $details;
        $this->httpCode = $httpCode;
        $this->internalCode = $internalCode;
    }

    public function render(Request $request): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
            'data' => $this->getResponseData(),
        ];

        if ($this->internalCode) {
            $response['error_code'] = $this->internalCode;
        }

        return response()->json($response, $this->httpCode);
    }

    private function getResponseData()
    {
        if (empty($this->details)) {
            return null;
        }

        if ($this->internalCode === 'VALIDATION_ERROR') {
            return $this->details;
        }

        if (! config('app.debug')) {
            return is_array($this->details) && isset($this->details['errorInfo'])
                ? $this->details['errorInfo']
                : null;
        }

        return $this->details;
    }
}
