<?php

namespace App\Middleware;

use App\Core\JsonResponse;

class ApiAuthMiddleware
{
    public static function check(): ?int
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (empty($authHeader)) {
            JsonResponse::unauthorized('Token de autenticação não fornecido');
        }

        // Extrair token do header "Bearer {token}"
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            JsonResponse::unauthorized('Formato de token inválido. Use: Bearer {token}');
        }

        $token = $matches[1];

        // Validar token na sessão ou banco de dados
        $lojaUsuarioId = self::validateToken($token);

        if (!$lojaUsuarioId) {
            JsonResponse::unauthorized('Token inválido ou expirado');
        }

        return $lojaUsuarioId;
    }

    private static function validateToken(string $token): ?int
    {
        // Verificar se a sessão já está ativa antes de iniciar
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Se houver sessão ativa e o token corresponde
        if (isset($_SESSION['api_token']) && $_SESSION['api_token'] === $token) {
            return $_SESSION['loja_usuario_id'] ?? null;
        }

        $decoded = base64_decode($token, true);
        if ($decoded && is_numeric($decoded)) {
            return (int) $decoded;
        }

        return null;
    }
    public static function lojaUsuarioId(): ?int
    {
        return $_SESSION['loja_usuario_id'] ?? null;
    }

    public static function generateToken(int $lojaUsuarioId): string
    {
        session_start();
        $token = base64_encode($lojaUsuarioId . '-' . time());
        $_SESSION['api_token'] = $token;
        $_SESSION['loja_usuario_id'] = $lojaUsuarioId;
        return $token;
    }
}
