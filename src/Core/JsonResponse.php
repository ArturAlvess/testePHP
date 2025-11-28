<?php

namespace App\Core;

class JsonResponse
{

    public static function success($data = null, string $message = 'Success', int $statusCode = 200): void
    {
        self::send([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function error(string $message = 'Error', $errors = null, int $statusCode = 400): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error($message, null, 401);
    }

    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, null, 404);
    }

    public static function validationError(array $errors, string $message = 'Validation failed'): void
    {
        self::error($message, $errors, 422);
    }

    private static function send(array $data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function getRequestData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            return $data ?? [];
        }
        
        return $_POST;
    }
}
