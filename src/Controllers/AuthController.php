<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\LojaUsuario;

class AuthController extends Controller
{
    public function login(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/');
        }

        $this->view('auth/login', [
            'title' => 'Login - Sistema AlphaCode'
        ]);
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $_SESSION['error'] = 'Email e senha são obrigatórios';
            $this->redirect('/login');
            return;
        }
        $user = LojaUsuario::authenticate($email, $senha);

        if (!$user) {
            $_SESSION['error'] = 'Email ou senha inválidos';
            $this->redirect('/login');
            return;
        }

        $_SESSION['user'] = $user;
        $_SESSION['loja_usuario_id'] = $user['ID_LOJA_USUARIO'];
        $_SESSION['user_name'] = $user['NOME_USUARIO'];
        $_SESSION['nome_loja'] = $user['NOME_LOJA'];
        $_SESSION['success'] = 'Login realizado com sucesso!';

        $this->redirect('/');
    }


    public function logout(): void
    {
        $wasLoggedIn = isset($_SESSION['loja_usuario_id']);
        
        session_unset();
        session_destroy();
        
        session_start();
        
        if ($wasLoggedIn) {
            $_SESSION['success'] = 'Logout realizado com sucesso!';
        }
        
        $this->redirect('/login');
    }

    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }
}
