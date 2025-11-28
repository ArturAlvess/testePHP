<?php

namespace App\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\ErrorHandler;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Produto;
use App\Middleware\ApiAuthMiddleware;

class PedidoApiController
{
    private Pedido $pedidoModel;
    private Cliente $clienteModel;
    private Produto $produtoModel;

    public function __construct()
    {
        $this->pedidoModel = new Pedido();
        $this->clienteModel = new Cliente();
        $this->produtoModel = new Produto();
    }

    /**
     * GET /api/pedidos
     */
    public function index(): void
    {
        $lojaUsuarioId = ApiAuthMiddleware::check();

        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 20);
        $offset = ($page - 1) * $perPage;

        $filters = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId,
            'ID_PEDIDO' => $_GET['id'] ?? '',
            'ID_CLIENTE' => $_GET['cliente'] ?? '',
            'STATUS' => $_GET['status'] ?? '',
            'DATA_PEDIDO' => $_GET['data'] ?? '',
        ];

        $orderBy = $_GET['order_by'] ?? 'p.ID_PEDIDO';
        $orderDirection = $_GET['order_dir'] ?? 'DESC';

        $pedidos = $this->pedidoModel->getAllWithCliente($filters, $orderBy, $orderDirection, $perPage, $offset);
        $total = $this->pedidoModel->countWithFilters($filters);

        JsonResponse::success([
            'pedidos' => $pedidos,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * GET /api/pedidos/{id}
     */
    public function show(int $id): void
    {
        ApiAuthMiddleware::check();
        
        $pedido = $this->pedidoModel->getWithCliente($id);

        if (!$pedido) {
            JsonResponse::notFound('Pedido não encontrado');
        }

        $itens = $this->pedidoModel->getItens($id);
        $total = $this->pedidoModel->getTotal($id);

        $pedido['itens'] = $itens;
        $pedido['subtotal'] = $total;
        $pedido['total_final'] = $pedido['VALOR_TOTAL'] ?? $total;

        JsonResponse::success($pedido);
    }

    /**
     * POST /api/pedidos
     */
    public function store(): void
    {
        $lojaUsuarioId = ApiAuthMiddleware::check();
        $data = JsonResponse::getRequestData();

        $pedidoData = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId,
            'ID_CLIENTE' => $data['id_cliente'] ?? $data['ID_CLIENTE'] ?? '',
            'STATUS' => $data['status'] ?? $data['STATUS'] ?? 'EM_ABERTO',
            'OBSERVACAO' => $data['observacao'] ?? $data['OBSERVACAO'] ?? ''
        ];

        $errors = $this->pedidoModel->validate($pedidoData);

        if (!empty($errors)) {
            JsonResponse::validationError($errors);
        }

        try {
            $pedidoId = $this->pedidoModel->create($pedidoData);
            $pedido = $this->pedidoModel->getWithCliente($pedidoId);
            
            JsonResponse::success($pedido, 'Pedido criado com sucesso', 201);
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'criar pedido'), null, 500);
        }
    }

    /**
     * PUT /api/pedidos/{id}
     */
    public function update(int $id): void
    {
        ApiAuthMiddleware::check();
        $data = JsonResponse::getRequestData();

        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            JsonResponse::notFound('Pedido não encontrado');
        }

        $pedidoData = [
            'ID_CLIENTE' => $data['id_cliente'] ?? $data['ID_CLIENTE'] ?? '',
            'STATUS' => $data['status'] ?? $data['STATUS'] ?? 'EM_ABERTO',
            'OBSERVACAO' => $data['observacao'] ?? $data['OBSERVACAO'] ?? ''
        ];

        $errors = $this->pedidoModel->validate($pedidoData);

        if (!empty($errors)) {
            JsonResponse::validationError($errors);
        }

        try {
            $this->pedidoModel->update($id, $pedidoData);
            $pedido = $this->pedidoModel->getWithCliente($id);
            
            JsonResponse::success($pedido, 'Pedido atualizado com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'atualizar pedido'), null, 500);
        }
    }

    /**
     * DELETE /api/pedidos/{id}
     */
    public function delete(int $id): void
    {
        ApiAuthMiddleware::check();

        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            JsonResponse::notFound('Pedido não encontrado');
        }

        try {
            $this->pedidoModel->delete($id);
            JsonResponse::success(null, 'Pedido excluído com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'excluir pedido'), null, 500);
        }
    }

    /**
     * POST /api/pedidos/{id}/itens
     */
    public function addItem(int $id): void
    {
        ApiAuthMiddleware::check();
        $data = JsonResponse::getRequestData();

        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            JsonResponse::notFound('Pedido não encontrado');
        }

        $produtoId = (int) ($data['id_produto'] ?? $data['ID_PRODUTO'] ?? 0);
        $quantidade = (int) ($data['quantidade'] ?? $data['QUANTIDADE'] ?? 0);

        if ($produtoId <= 0 || $quantidade <= 0) {
            JsonResponse::validationError([
                'id_produto' => $produtoId <= 0 ? 'Produto é obrigatório' : null,
                'quantidade' => $quantidade <= 0 ? 'Quantidade deve ser maior que zero' : null
            ]);
        }

        $produto = $this->produtoModel->find($produtoId);
        if (!$produto) {
            JsonResponse::notFound('Produto não encontrado');
        }

        try {
            $this->pedidoModel->addItem($id, $produtoId, $quantidade, $produto['VALOR_UNITARIO']);
            $pedido = $this->pedidoModel->getWithCliente($id);
            $pedido['itens'] = $this->pedidoModel->getItens($id);
            
            JsonResponse::success($pedido, 'Item adicionado com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'adicionar item'), null, 500);
        }
    }

    /**
     * DELETE /api/pedidos/{id}/itens/{itemId}
     */
    public function removeItem(int $id, int $itemId): void
    {
        ApiAuthMiddleware::check();

        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            JsonResponse::notFound('Pedido não encontrado');
        }

        try {
            $this->pedidoModel->removeItem($itemId);
            JsonResponse::success(null, 'Item removido com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'remover item'), null, 500);
        }
    }

    /**
     * POST /api/pedidos/{id}/desconto
     */
    public function applyDiscount(int $id): void
    {
        ApiAuthMiddleware::check();
        $data = JsonResponse::getRequestData();

        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            JsonResponse::notFound('Pedido não encontrado');
        }

        $tipo = $data['tipo'] ?? 'percentual';
        $valor = (float) ($data['valor'] ?? 0);

        if ($valor <= 0) {
            JsonResponse::validationError(['valor' => 'Valor de desconto deve ser maior que zero']);
        }

        $total = $this->pedidoModel->getTotal($id);

        if ($tipo === 'percentual') {
            if ($valor > 100) {
                JsonResponse::error('Desconto percentual não pode ser maior que 100%', null, 400);
            }
            $descontoValor = ($total * $valor) / 100;
            $descontoPercentual = $valor;
        } else {
            if ($valor > $total) {
                JsonResponse::error('Desconto em valor não pode ser maior que o total do pedido', null, 400);
            }
            $descontoValor = $valor;
            $descontoPercentual = ($valor / $total) * 100;
        }

        $valorTotal = $total - $descontoValor;

        try {
            $this->pedidoModel->updateDiscount($id, $descontoPercentual, $descontoValor, $valorTotal);
            $pedido = $this->pedidoModel->getWithCliente($id);
            
            JsonResponse::success($pedido, 'Desconto aplicado com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'aplicar desconto'), null, 500);
        }
    }

    /**
     * DELETE /api/pedidos/{id}/desconto
     */
    public function removeDiscount(int $id): void
    {
        ApiAuthMiddleware::check();

        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            JsonResponse::notFound('Pedido não encontrado');
        }

        try {
            $total = $this->pedidoModel->getTotal($id);
            $this->pedidoModel->updateDiscount($id, 0, 0, $total);
            
            JsonResponse::success(null, 'Desconto removido com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'remover desconto'), null, 500);
        }
    }
}
