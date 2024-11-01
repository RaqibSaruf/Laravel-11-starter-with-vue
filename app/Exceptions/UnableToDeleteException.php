<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class UnableToDeleteException extends \Exception
{
    // Custom properties for your exception
    protected $message;
    protected $errorCode;

    public function __construct($message = 'Unable to delete the record', $errorCode = Response::HTTP_CONFLICT)
    {
        // Set a custom message and error code
        $this->message = $message;
        $this->errorCode = $errorCode;

        parent::__construct($message, $errorCode);
    }

    // Customize the exception response
    public function render($request)
    {
        return response()->json([
            'message' => $this->message,
        ], $this->errorCode);
    }
}
