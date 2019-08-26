<?php

namespace App\Services\Response;

use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    protected $statusCode = 200;

    protected $message;

    protected $pagination;

    protected $error;

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function setPagination(LengthAwarePaginator $pagination)
    {
        $this->pagination = [
            'total' => $pagination->total(),
            'per_page' => min($pagination->total(), $pagination->perPage()),
            'current_page' => $pagination->currentPage(),
            'last_page' => $pagination->lastPage(),
            'first_page_url' => $pagination->url(1),
            'last_page_url' => $pagination->url($pagination->lastPage()),
            'next_page_url' => $pagination->nextPageUrl(),
            'prev_page_url' => $pagination->previousPageUrl(),
            'path' => url('v1'),
            'from' => $pagination->firstItem(),
            'to' => $pagination->lastItem(),
        ];

        return $this;
    }

    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function respondUnauthorizedError($message = 'Unauthorized!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->respondWithError($message);
    }

    public function respondForbiddenError($message = 'Forbidden!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_FORBIDDEN)->respondWithError($message);
    }

    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    public function respondServiceUnavailable($message = 'Service Unavailable!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_SERVICE_UNAVAILABLE)->respondWithError($message);
    }

    public function respondWithError($message)
    {
        return $this->setMessage($message)->respond();
    }

    public function respondCreated($data = [])
    {
        $this->message = 'successfully created';

        return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)
            ->respond($data);
    }

    public function respondUpdated($data = [])
    {
        $this->message = 'Updated successfully';

        return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)
            ->respond($data);
    }

    public function respondDeleted($data = [])
    {
        $this->message = 'Deleted successfully';

        return $this->setStatusCode(IlluminateResponse::HTTP_OK)
            ->respond($data);
    }

    public function respondUnprocessableEntity($message)
    {
        return $this->setMessage($message)->setStatusCode(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->respond([
                'message' => $message,
            ]);
    }

    public function respondSuccess($message)
    {
        return $this->setMessage($message)->setStatusCode(IlluminateResponse::HTTP_OK)
            ->respond([
                'message' => $message,
            ]);
    }

    public function respondInvalidQuery(\Exception $exception)
    {
        \Log::info('Invalid query!', [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'request' => request()->all(),
        ]);

        return $this->respondBadRequest('Error while processing requested query. Please check your filter, sort by, includes, etc and try again.');
    }

    public function respondBadRequest($message = 'Bad Request')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_BAD_REQUEST)->respondWithError($message);
    }

    public function respondInternalError(\Exception $exception)
    {
        \Log::warning('Internal Error!', [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'request' => request()->all(),
        ]);
        lad(['secret_error_msg' => $exception->getMessage()]);

        return $this->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)
            ->respondWithError('Something went wrong! Please contact our support at support@chs.com');
    }

    public function respondPaymentFailed($errorMessage, $parameters = [])
    {
        \Log::warning('Payment Failed!', [
            'error' => $errorMessage,
            'parameters' => $parameters,
        ]);

        return $this->setStatusCode(IlluminateResponse::HTTP_PAYMENT_REQUIRED)
            ->respondWithError($errorMessage);
    }

    public function respond($data = [], $headers = [])
    {
        $finalData = [];
        if (!empty($data)) {
            $finalData['data'] = $data;
        }
        if (!empty($this->pagination)) {
            $finalData['pagination'] = $this->pagination;
        }
        if (!empty($this->message)) {
            $finalData['message'] = $this->message;
        }
        if (!empty($this->error)) {
            $finalData['error'] = $this->error;
        }

        return response()->json($finalData, $this->getStatusCode(), $headers);
    }

    public function respondWithExcception($response)
    { //already gets parsed response from Exception Handler
        return $response;
    }
}
