<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ErrorHandler;
use App\Models\Cliente;
use App\Middleware\AuthMiddleware;

class ClienteController extends Controller
{
    private Cliente $clienteModel;

    public function __construct()
    {
        AuthMiddleware::check();
        $this->clienteModel = new Cliente();
    }

    public function index(): void
    {
        $lojaUsuarioId = AuthMiddleware::lojaUsuarioId();
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 20);

        $perPage = max(5, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $filters = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId,
            'ID_CLIENTE' => $_GET['id'] ?? '',
            'NOME_CLIENTE' => $_GET['nome'] ?? '',
            'CPF' => $_GET['cpf'] ?? '',
            'EMAIL' => $_GET['email'] ?? '',
            'TELEFONE' => $_GET['telefone'] ?? '',
        ];

        $orderBy = $_GET['order_by'] ?? 'ID_CLIENTE';
        $orderDirection = $_GET['order_dir'] ?? 'ASC';

        $clientes = $this->clienteModel->all($filters, $orderBy, $orderDirection, $perPage, $offset);
        $total = $this->clienteModel->count($filters);
        $totalPages = ceil($total / $perPage);

        $this->view('clientes.index', [
            'clientes' => $clientes,
            'filters' => $filters,
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'perPage' => $perPage
        ]);
    }

    public function create(): void
    {
        $this->view('clientes.create');
    }

    public function store(): void
    {
        $lojaUsuarioId = AuthMiddleware::lojaUsuarioId();
        
        $data = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId, 
            'NOME_CLIENTE' => $_POST['NOME_CLIENTE'] ?? '',
            'CPF' => preg_replace('/[^0-9]/', '', $_POST['CPF'] ?? ''),
            'EMAIL' => $_POST['EMAIL'] ?? '',
            'TELEFONE' => $_POST['TELEFONE'] ?? '',
            'ENDERECO' => $_POST['ENDERECO'] ?? ''
        ];

        $errors = $this->clienteModel->validate($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect('/clientes/create');
        }

        try {
            $this->clienteModel->create($data);
            $_SESSION['success'] = 'Cliente criado com sucesso!';
            $this->redirect('/clientes');
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'criar cliente');
            $_SESSION['old'] = $data;
            $this->redirect('/clientes/create');
        }
    }

    public function edit(int $id): void
    {
        $cliente = $this->clienteModel->find($id);

        if (!$cliente) {
            $_SESSION['error'] = 'Cliente não encontrado';
            $this->redirect('/clientes');
        }

        $this->view('clientes.edit', ['cliente' => $cliente]);
    }

    public function update(int $id): void
    {
        $data = [
            'NOME_CLIENTE' => $_POST['NOME_CLIENTE'] ?? '',
            'CPF' => preg_replace('/[^0-9]/', '', $_POST['CPF'] ?? ''),
            'EMAIL' => $_POST['EMAIL'] ?? '',
            'TELEFONE' => $_POST['TELEFONE'] ?? '',
            'ENDERECO' => $_POST['ENDERECO'] ?? ''
        ];

        $errors = $this->clienteModel->validate($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect("/clientes/edit/{$id}");
        }

        try {
            $this->clienteModel->update($id, $data);
            $_SESSION['success'] = 'Cliente atualizado com sucesso!';
            $this->redirect('/clientes');
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'atualizar cliente');
            $_SESSION['old'] = $data;
            $this->redirect("/clientes/edit/{$id}");
        }
    }

    public function delete(int $id): void
    {
        $nomeCliente = $this->clienteModel->getNome($id);
        $validacao = $this->clienteModel->canDelete($id);

        if (!$validacao['can_delete']) {
            $_SESSION['error'] = "O cliente '{$nomeCliente}' não pode ser excluído. " . $validacao['message'];
            $this->redirect('/clientes');
            return;
        }

        try {
            $this->clienteModel->delete($id);
            $_SESSION['success'] = "Cliente '{$nomeCliente}' excluído com sucesso!";
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, "excluir cliente '{$nomeCliente}'");
        }
        $this->redirect('/clientes');
    }

    public function deleteMultiple(): void
    {
        $ids = $_POST['ids'] ?? [];
        
        if (empty($ids)) {
            $_SESSION['error'] = 'Nenhum cliente selecionado para exclusão';
            $this->redirect('/clientes');
            return;
        }

        $deleted = 0;
        $skipped = [];
        $errors = [];

        foreach ($ids as $id) {
            $nomeCliente = $this->clienteModel->getNome((int)$id);
            $validacao = $this->clienteModel->canDelete((int)$id);

            if (!$validacao['can_delete']) {
                $skipped[] = "'{$nomeCliente}': " . $validacao['message'];
                continue;
            }

            try {
                $this->clienteModel->delete((int)$id);
                $deleted++;
            } catch (\Exception $e) {
                $errors[] = "'{$nomeCliente}': " . ErrorHandler::handleGenericError($e, 'excluir');
            }
        }

        $messages = [];
        
        if ($deleted > 0) {
            $messages[] = "{$deleted} cliente(s) excluído(s) com sucesso!";
        }
        
        if (!empty($skipped)) {
            $messages[] = "Clientes não excluídos: " . implode('; ', $skipped);
        }
        
        if (!empty($errors)) {
            $messages[] = "Erros: " . implode('; ', $errors);
        }

        if ($deleted > 0) {
            $_SESSION['success'] = implode(' | ', $messages);
        } else {
            $_SESSION['error'] = implode(' | ', $messages);
        }

        $this->redirect('/clientes');
    }

    public function show(int $id): void
    {
        $cliente = $this->clienteModel->find($id);

        if (!$cliente) {
            $_SESSION['error'] = 'Cliente não encontrado';
            $this->redirect('/clientes');
        }

        $pedidos = $this->clienteModel->getPedidos($id);

        $this->view('clientes.view', [
            'cliente' => $cliente,
            'pedidos' => $pedidos
        ]);
    }
}
