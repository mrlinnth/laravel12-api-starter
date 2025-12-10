<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Return a success JSON response
     */
    protected function successResponse(
        mixed $data = null,
        ?string $message = null,
        int $status = 200
    ): JsonResponse {
        $response = ['success' => true];

        if ($data !== null) {
            // Handle Laravel Resources
            if ($data instanceof JsonResource || $data instanceof ResourceCollection) {
                return $data->additional(['success' => true])
                    ->response()
                    ->setStatusCode($status);
            }

            $response['data'] = $data;
        }

        if ($message !== null) {
            $response['message'] = $message;
        }

        return response()->json($response, $status);
    }

    /**
     * Return an error JSON response
     */
    protected function errorResponse(
        string $message,
        int $status = 400,
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Return a created response (201)
     */
    protected function createdResponse(
        mixed $data,
        ?string $message = 'Resource created successfully'
    ): JsonResponse {
        if ($data instanceof JsonResource) {
            return $data->additional([
                'success' => true,
                'message' => $message,
            ])->response()->setStatusCode(201);
        }

        return $this->successResponse($data, $message, 201);
    }

    /**
     * Return a deleted response (200)
     */
    protected function deletedResponse(
        ?string $message = 'Resource deleted successfully'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    }

    /**
     * Return a no content response (204)
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json([], 204);
    }

    /**
     * Return a validation error response (422)
     */
    protected function validationErrorResponse(
        mixed $errors,
        ?string $message = 'The given data was invalid'
    ): JsonResponse {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Return a not found response (404)
     */
    protected function notFoundResponse(
        ?string $message = 'Resource not found'
    ): JsonResponse {
        return $this->errorResponse($message, 404);
    }

    /**
     * Return an unauthorized response (401)
     */
    protected function unauthorizedResponse(
        ?string $message = 'Unauthenticated'
    ): JsonResponse {
        return $this->errorResponse($message, 401);
    }

    /**
     * Return a forbidden response (403)
     */
    protected function forbiddenResponse(
        ?string $message = 'This action is unauthorized'
    ): JsonResponse {
        return $this->errorResponse($message, 403);
    }

    /**
     * Return a bad request response (400)
     */
    protected function badRequestResponse(
        ?string $message = 'Bad request'
    ): JsonResponse {
        return $this->errorResponse($message, 400);
    }

    /**
     * Return a conflict response (409)
     */
    protected function conflictResponse(
        ?string $message = 'Resource already exists'
    ): JsonResponse {
        return $this->errorResponse($message, 409);
    }

    /**
     * Return an internal server error response (500)
     */
    protected function serverErrorResponse(
        ?string $message = 'Internal server error'
    ): JsonResponse {
        return $this->errorResponse($message, 500);
    }
}
