<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ErrorHandler;
use App\Models\LojaUsuario;

class RegistroController extends Controller
{

    public function index(): void
    {
        $this->view('registro/index', [
            'title' => 'Cadastrar Loja e Usuário'
        ]);
    }


    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/registro');
            return;
        }

        // Dados do formulário
        $nomeUsuario = trim($_POST['nome_usuario'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $senhaConfirm = $_POST['senha_confirm'] ?? '';
        $nomeLoja = trim($_POST['nome_loja'] ?? '');
        $cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj'] ?? '');
        $enderecoLoja = trim($_POST['endereco_loja'] ?? '');

        // Validações
        $errors = [];

        if (empty($nomeUsuario)) {
            $errors[] = 'Nome do usuário é obrigatório';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }

        if (LojaUsuario::emailExists($email)) {
            $errors[] = 'Email já cadastrado';
        }

        if (empty($senha) || strlen($senha) < 6) {
            $errors[] = 'Senha deve ter no mínimo 6 caracteres';
        }

        if ($senha !== $senhaConfirm) {
            $errors[] = 'As senhas não coincidem';
        }

        if (empty($nomeLoja)) {
            $errors[] = 'Nome da loja é obrigatório';
        }

        if (!empty($cnpj)) {
            if (strlen($cnpj) != 14) {
                $errors[] = 'CNPJ inválido (deve ter 14 dígitos)';
            }
            
            if (LojaUsuario::cnpjExists($cnpj)) {
                $errors[] = 'CNPJ já cadastrado';
            }
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/registro');
            return;
        }

        try {
            $id = LojaUsuario::createWithHashedPassword([
                'NOME_USUARIO' => $nomeUsuario,
                'EMAIL' => $email,
                'SENHA' => $senha,
                'NOME_LOJA' => $nomeLoja,
                'CNPJ' => $cnpj ?: null,
                'ENDERECO_LOJA' => $enderecoLoja ?: null
            ]);

            unset($_SESSION['form_data']);
            $_SESSION['success'] = 'Cadastro realizado com sucesso! Faça login para continuar.';
            $this->redirect('/login');

        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'realizar cadastro');
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/registro');
        }
    }
}
