<?php

namespace App\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\ErrorHandler;
use App\Models\Cliente;
use App\Middleware\ApiAuthMiddleware;

class ClienteApiController
{
    private Cliente $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    /**
     * GET /api/clientes
     */
    public function index(): void
    {
        $lojaUsuarioId = ApiAuthMiddleware::check();

        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 20);
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

        JsonResponse::success([
            'clientes' => $clientes,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * GET /api/clientes/{id}
     */
    public function show(int $id): void
    {
        ApiAuthMiddleware::check();
        
        $cliente = $this->clienteModel->find($id);

        if (!$cliente) {
            JsonResponse::notFound('Cliente não encontrado');
        }

        JsonResponse::success($cliente);
    }

    /**
     * POST /api/clientes
     */
    public function store(): void
    {
        $lojaUsuarioId = ApiAuthMiddleware::check();
        $data = JsonResponse::getRequestData();

        $clienteData = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId,
            'NOME_CLIENTE' => $data['nome'] ?? $data['NOME_CLIENTE'] ?? '',
            'CPF' => preg_replace('/[^0-9]/', '', $data['cpf'] ?? $data['CPF'] ?? ''),
            'EMAIL' => $data['email'] ?? $data['EMAIL'] ?? '',
            'TELEFONE' => $data['telefone'] ?? $data['TELEFONE'] ?? '',
            'ENDERECO' => $data['endereco'] ?? $data['ENDERECO'] ?? ''
        ];

        $errors = $this->clienteModel->validate($clienteData);

        if (!empty($errors)) {
            JsonResponse::validationError($errors);
        }

        try {
            $clienteId = $this->clienteModel->create($clienteData);
            $cliente = $this->clienteModel->find($clienteId);
            
            JsonResponse::success($cliente, 'Cliente criado com sucesso', 201);
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'criar cliente'), null, 500);
        }
    }

    /**
     * PUT /api/clientes/{id}
     */
    public function update(int $id): void
    {
        ApiAuthMiddleware::check();
        $data = JsonResponse::getRequestData();

        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            JsonResponse::notFound('Cliente não encontrado');
        }

        $clienteData = [
            'NOME_CLIENTE' => $data['nome'] ?? $data['NOME_CLIENTE'] ?? '',
            'CPF' => preg_replace('/[^0-9]/', '', $data['cpf'] ?? $data['CPF'] ?? ''),
            'EMAIL' => $data['email'] ?? $data['EMAIL'] ?? '',
            'TELEFONE' => $data['telefone'] ?? $data['TELEFONE'] ?? '',
            'ENDERECO' => $data['endereco'] ?? $data['ENDERECO'] ?? ''
        ];

        $errors = $this->clienteModel->validate($clienteData);

        if (!empty($errors)) {
            JsonResponse::validationError($errors);
        }

        try {
            $this->clienteModel->update($id, $clienteData);
            $cliente = $this->clienteModel->find($id);
            
            JsonResponse::success($cliente, 'Cliente atualizado com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'atualizar cliente'), null, 500);
        }
    }

    /**
     * DELETE /api/clientes/{id}
     */
    public function delete(int $id): void
    {
        ApiAuthMiddleware::check();

        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            JsonResponse::notFound('Cliente não encontrado');
        }

        $nomeCliente = $cliente['NOME_CLIENTE'];
        $validacao = $this->clienteModel->canDelete($id);

        if (!$validacao['can_delete']) {
            JsonResponse::error(
                "O cliente '{$nomeCliente}' não pode ser excluído. " . $validacao['message'],
                null,
                409
            );
        }

        try {
            $this->clienteModel->delete($id);
            JsonResponse::success(null, "Cliente '{$nomeCliente}' excluído com sucesso");
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'excluir cliente'), null, 500);
        }
    }
}
