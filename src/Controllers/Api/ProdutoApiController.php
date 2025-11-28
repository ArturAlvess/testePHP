<?php

namespace App\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\ErrorHandler;
use App\Models\Produto;
use App\Middleware\ApiAuthMiddleware;

class ProdutoApiController
{
    private Produto $produtoModel;

    public function __construct()
    {
        $this->produtoModel = new Produto();
    }

    /**
     * GET /api/produtos
     */
    public function index(): void
    {
        $lojaUsuarioId = ApiAuthMiddleware::check();

        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 20);
        $offset = ($page - 1) * $perPage;

        $filters = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId,
            'ID_PRODUTO' => $_GET['id'] ?? '',
            'COD_BARRAS' => $_GET['cod_barras'] ?? '',
            'NOME_PRODUTO' => $_GET['nome'] ?? '',
            'VALOR_UNITARIO' => $_GET['valor'] ?? '',
        ];

        $orderBy = $_GET['order_by'] ?? 'ID_PRODUTO';
        $orderDirection = $_GET['order_dir'] ?? 'ASC';

        $produtos = $this->produtoModel->all($filters, $orderBy, $orderDirection, $perPage, $offset);
        $total = $this->produtoModel->count($filters);

        JsonResponse::success([
            'produtos' => $produtos,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * GET /api/produtos/{id}
     */
    public function show(int $id): void
    {
        ApiAuthMiddleware::check();
        
        $produto = $this->produtoModel->find($id);

        if (!$produto) {
            JsonResponse::notFound('Produto não encontrado');
        }

        JsonResponse::success($produto);
    }

    /**
     * POST /api/produtos
     */
    public function store(): void
    {
        $lojaUsuarioId = ApiAuthMiddleware::check();
        $data = JsonResponse::getRequestData();

        $produtoData = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId,
            'COD_BARRAS' => $data['cod_barras'] ?? $data['COD_BARRAS'] ?? '',
            'NOME_PRODUTO' => $data['nome'] ?? $data['NOME_PRODUTO'] ?? '',
            'DESCRICAO' => $data['descricao'] ?? $data['DESCRICAO'] ?? '',
            'VALOR_UNITARIO' => $data['valor'] ?? $data['VALOR_UNITARIO'] ?? ''
        ];

        $errors = $this->produtoModel->validate($produtoData);

        if (!empty($errors)) {
            JsonResponse::validationError($errors);
        }

        try {
            $produtoId = $this->produtoModel->create($produtoData);
            $produto = $this->produtoModel->find($produtoId);
            
            JsonResponse::success($produto, 'Produto criado com sucesso', 201);
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'criar produto'), null, 500);
        }
    }

    /**
     * PUT /api/produtos/{id}
     */
    public function update(int $id): void
    {
        ApiAuthMiddleware::check();
        $data = JsonResponse::getRequestData();

        $produto = $this->produtoModel->find($id);
        if (!$produto) {
            JsonResponse::notFound('Produto não encontrado');
        }

        $produtoData = [
            'COD_BARRAS' => $data['cod_barras'] ?? $data['COD_BARRAS'] ?? '',
            'NOME_PRODUTO' => $data['nome'] ?? $data['NOME_PRODUTO'] ?? '',
            'DESCRICAO' => $data['descricao'] ?? $data['DESCRICAO'] ?? '',
            'VALOR_UNITARIO' => $data['valor'] ?? $data['VALOR_UNITARIO'] ?? ''
        ];

        $errors = $this->produtoModel->validate($produtoData);

        if (!empty($errors)) {
            JsonResponse::validationError($errors);
        }

        try {
            $this->produtoModel->update($id, $produtoData);
            $produto = $this->produtoModel->find($id);
            
            JsonResponse::success($produto, 'Produto atualizado com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'atualizar produto'), null, 500);
        }
    }

    /**
     * DELETE /api/produtos/{id}
     */
    public function delete(int $id): void
    {
        ApiAuthMiddleware::check();

        $produto = $this->produtoModel->find($id);
        if (!$produto) {
            JsonResponse::notFound('Produto não encontrado');
        }

        try {
            $this->produtoModel->delete($id);
            JsonResponse::success(null, 'Produto excluído com sucesso');
        } catch (\Exception $e) {
            JsonResponse::error(ErrorHandler::handleGenericError($e, 'excluir produto'), null, 500);
        }
    }
}
