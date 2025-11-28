<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function check(): void
    {
        if (!isset($_SESSION['loja_usuario_id'])) {
            $_SESSION['error'] = 'Você precisa fazer login para acessar esta página';
            header('Location: /login');
            exit;
        }
    }

    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['loja_usuario_id']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function lojaUsuarioId(): ?int
    {
        return $_SESSION['loja_usuario_id'] ?? null;
    }
}
