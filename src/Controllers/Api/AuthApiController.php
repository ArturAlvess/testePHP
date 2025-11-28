<?php

namespace App\Controllers\Api;

use App\Core\JsonResponse;
use App\Models\LojaUsuario;

class AuthApiController
{
    private LojaUsuario $lojaUsuarioModel;

    public function __construct()
    {
        $this->lojaUsuarioModel = new LojaUsuario();
    }

    /**
     * POST /api/auth/login
     * Body: { "email": "user@example.com", "senha": "password" }
     */
    public function login(): void
    {
        $data = JsonResponse::getRequestData();

        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            JsonResponse::validationError([
                'email' => empty($email) ? 'Email é obrigatório' : null,
                'senha' => empty($senha) ? 'Senha é obrigatória' : null
            ]);
        }

        $lojaUsuario = $this->lojaUsuarioModel->authenticate($email, $senha);

        if (!$lojaUsuario) {
            JsonResponse::error('Credenciais inválidas', null, 401);
        }

        // Gerar token
        session_start();
        $token = base64_encode($lojaUsuario['ID_LOJA_USUARIO'] . '-' . time() . '-' . uniqid());
        $_SESSION['api_token'] = $token;
        $_SESSION['loja_usuario_id'] = $lojaUsuario['ID_LOJA_USUARIO'];

        JsonResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $lojaUsuario['ID_LOJA_USUARIO'],
                'nome' => $lojaUsuario['NOME_USUARIO'],
                'email' => $lojaUsuario['EMAIL'],
                'loja' => $lojaUsuario['NOME_LOJA']
            ]
        ], 'Login realizado com sucesso');
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(): void
    {
        session_start();
        session_destroy();
        JsonResponse::success(null, 'Logout realizado com sucesso');
    }

    /**
     * GET /api/auth/me
     */
    public function me(): void
    {
        $lojaUsuarioId = \App\Middleware\ApiAuthMiddleware::check();
        $lojaUsuario = $this->lojaUsuarioModel->find($lojaUsuarioId);

        if (!$lojaUsuario) {
            JsonResponse::notFound('Usuário não encontrado');
        }

        JsonResponse::success([
            'id' => $lojaUsuario['ID_LOJA_USUARIO'],
            'nome' => $lojaUsuario['NOME_USUARIO'],
            'email' => $lojaUsuario['EMAIL'],
            'loja' => $lojaUsuario['NOME_LOJA'],
            'cnpj' => $lojaUsuario['CNPJ'] ?? null
        ]);
    }
}
