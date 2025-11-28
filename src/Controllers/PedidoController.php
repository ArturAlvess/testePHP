<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ErrorHandler;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Produto;
use App\Middleware\AuthMiddleware;

class PedidoController extends Controller
{
    private Pedido $pedidoModel;
    private Cliente $clienteModel;
    private Produto $produtoModel;

    public function __construct()
    {
        AuthMiddleware::check();
        $this->pedidoModel = new Pedido();
        $this->clienteModel = new Cliente();
        $this->produtoModel = new Produto();
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
            'ID_PEDIDO' => $_GET['id'] ?? '',
            'ID_CLIENTE' => $_GET['cliente'] ?? '',
            'STATUS' => $_GET['status'] ?? '',
            'DATA_PEDIDO' => $_GET['data'] ?? '',
        ];

        $orderBy = $_GET['order_by'] ?? 'p.ID_PEDIDO';
        $orderDirection = $_GET['order_dir'] ?? 'DESC';

        $pedidos = $this->pedidoModel->getAllWithCliente($filters, $orderBy, $orderDirection, $perPage, $offset);
        $total = $this->pedidoModel->countWithFilters($filters);
        $totalPages = ceil($total / $perPage);

        $clientes = $this->clienteModel->all(['ID_LOJA_USUARIO' => $lojaUsuarioId]);

        $this->view('pedidos.index', [
            'pedidos' => $pedidos,
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
        $lojaUsuarioId = AuthMiddleware::lojaUsuarioId();
        
        // Verifica se existem clientes cadastrados
        $clientes = $this->clienteModel->all(['ID_LOJA_USUARIO' => $lojaUsuarioId], 'NOME_CLIENTE', 'ASC');
        if (empty($clientes)) {
            $_SESSION['warning'] = 'Você precisa cadastrar pelo menos um cliente antes de criar um pedido.';
            $this->redirect('/clientes/create');
            return;
        }
        
        // Verifica se existem produtos cadastrados
        $produtos = $this->produtoModel->all(['ID_LOJA_USUARIO' => $lojaUsuarioId]);
        if (empty($produtos)) {
            $_SESSION['warning'] = 'Você precisa cadastrar pelo menos um produto antes de criar um pedido.';
            $this->redirect('/produtos/create');
            return;
        }
        
        $this->view('pedidos.create', ['clientes' => $clientes]);
    }

    public function store(): void
    {
        $lojaUsuarioId = AuthMiddleware::lojaUsuarioId();
        
        $data = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId,
            'ID_CLIENTE' => $_POST['ID_CLIENTE'] ?? '',
            'STATUS' => $_POST['STATUS'] ?? 'EM_ABERTO',
            'OBSERVACAO' => $_POST['OBSERVACAO'] ?? ''
        ];

        $errors = $this->pedidoModel->validate($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect('/pedidos/create');
        }

        try {
            $pedidoId = $this->pedidoModel->create($data);
            $_SESSION['success'] = 'Pedido criado com sucesso!';
            $this->redirect("/pedidos/view/{$pedidoId}");
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'criar pedido');
            $_SESSION['old'] = $data;
            $this->redirect('/pedidos/create');
        }
    }

    public function edit(int $id): void
    {
        $lojaUsuarioId = AuthMiddleware::lojaUsuarioId();
        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            $_SESSION['error'] = 'Pedido não encontrado';
            $this->redirect('/pedidos');
        }

        $clientes = $this->clienteModel->all(['ID_LOJA_USUARIO' => $lojaUsuarioId], 'NOME_CLIENTE', 'ASC');

        $this->view('pedidos.edit', [
            'pedido' => $pedido,
            'clientes' => $clientes
        ]);
    }

    public function update(int $id): void
    {
        $data = [
            'ID_CLIENTE' => $_POST['ID_CLIENTE'] ?? '',
            'STATUS' => $_POST['STATUS'] ?? 'EM_ABERTO',
            'OBSERVACAO' => $_POST['OBSERVACAO'] ?? ''
        ];

        $errors = $this->pedidoModel->validate($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect("/pedidos/edit/{$id}");
        }

        try {
            $this->pedidoModel->update($id, $data);
            $_SESSION['success'] = 'Pedido atualizado com sucesso!';
            $this->redirect("/pedidos/view/{$id}");
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'atualizar pedido');
            $_SESSION['old'] = $data;
            $this->redirect("/pedidos/edit/{$id}");
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->pedidoModel->delete($id);
            $_SESSION['success'] = 'Pedido excluído com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'excluir pedido');
        }
        $this->redirect('/pedidos');
    }

    public function show(int $id): void
    {
        $lojaUsuarioId = AuthMiddleware::lojaUsuarioId();
        $pedido = $this->pedidoModel->getWithCliente($id);

        if (!$pedido) {
            $_SESSION['error'] = 'Pedido não encontrado';
            $this->redirect('/pedidos');
        }

        $itens = $this->pedidoModel->getItens($id);
        $total = $this->pedidoModel->getTotal($id);
        $produtos = $this->produtoModel->all(['ID_LOJA_USUARIO' => $lojaUsuarioId], 'NOME_PRODUTO', 'ASC');

        $this->view('pedidos.view', [
            'pedido' => $pedido,
            'itens' => $itens,
            'total' => $total,
            'produtos' => $produtos
        ]);
    }

    public function addItem(int $id): void
    {
        $produtoId = (int) ($_POST['ID_PRODUTO'] ?? 0);
        $quantidade = (int) ($_POST['QUANTIDADE'] ?? 0);

        if ($produtoId <= 0 || $quantidade <= 0) {
            $_SESSION['error'] = 'Produto e quantidade são obrigatórios';
            $this->redirect("/pedidos/view/{$id}");
        }

        $produto = $this->produtoModel->find($produtoId);

        if (!$produto) {
            $_SESSION['error'] = 'Produto não encontrado';
            $this->redirect("/pedidos/view/{$id}");
        }

        try {
            $this->pedidoModel->addItem($id, $produtoId, $quantidade, $produto['VALOR_UNITARIO']);
            $_SESSION['success'] = 'Item adicionado com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'adicionar item');
        }

        $this->redirect("/pedidos/view/{$id}");
    }

    public function removeItem(int $pedidoId, int $itemId): void
    {
        try {
            $this->pedidoModel->removeItem($itemId);
            $_SESSION['success'] = 'Item removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'remover item');
        }

        $this->redirect("/pedidos/view/{$pedidoId}");
    }

    public function deleteMultiple(): void
    {
        $ids = $_POST['ids'] ?? [];
        
        if (empty($ids)) {
            $_SESSION['error'] = 'Nenhum pedido selecionado para exclusão';
            $this->redirect('/pedidos');
            return;
        }

        $deleted = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $this->pedidoModel->delete((int)$id);
                $deleted++;
            } catch (\Exception $e) {
                $errors[] = "Pedido ID {$id}: " . ErrorHandler::handleGenericError($e, 'excluir');
            }
        }

        if ($deleted > 0) {
            $_SESSION['success'] = "{$deleted} pedido(s) excluído(s) com sucesso!";
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = 'Alguns pedidos não puderam ser excluídos: ' . implode('; ', $errors);
        }

        $this->redirect('/pedidos');
    }

    public function applyDiscount(int $id): void
    {
        $tipo = $_POST['tipo_desconto'] ?? 'percentual';
        $valor = (float) ($_POST['valor_desconto'] ?? 0);

        if ($valor <= 0) {
            $_SESSION['error'] = 'Valor de desconto inválido';
            $this->redirect("/pedidos/view/{$id}");
            return;
        }

        try {
            $pedido = $this->pedidoModel->find($id);
            if (!$pedido) {
                $_SESSION['error'] = 'Pedido não encontrado';
                $this->redirect('/pedidos');
                return;
            }

            $total = $this->pedidoModel->getTotal($id);
            
            if ($tipo === 'percentual') {
                if ($valor > 100) {
                    $_SESSION['error'] = 'Desconto percentual não pode ser maior que 100%';
                    $this->redirect("/pedidos/view/{$id}");
                    return;
                }
                $descontoValor = ($total * $valor) / 100;
                $descontoPercentual = $valor;
            } else {
                if ($valor > $total) {
                    $_SESSION['error'] = 'Desconto em valor não pode ser maior que o total do pedido';
                    $this->redirect("/pedidos/view/{$id}");
                    return;
                }
                $descontoValor = $valor;
                $descontoPercentual = ($valor / $total) * 100;
            }

            $valorTotal = $total - $descontoValor;

            $this->pedidoModel->updateDiscount($id, $descontoPercentual, $descontoValor, $valorTotal);
            $_SESSION['success'] = 'Desconto aplicado com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'aplicar desconto');
        }

        $this->redirect("/pedidos/view/{$id}");
    }

    public function removeDiscount(int $id): void
    {
        try {
            $total = $this->pedidoModel->getTotal($id);
            $this->pedidoModel->updateDiscount($id, 0, 0, $total);
            $_SESSION['success'] = 'Desconto removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'remover desconto');
        }

        $this->redirect("/pedidos/view/{$id}");
    }

    public function markAsPaid(int $id): void
    {
        try {
            $pedido = $this->pedidoModel->find($id);
            
            if (!$pedido) {
                $_SESSION['error'] = 'Pedido não encontrado';
                $this->redirect('/pedidos');
                return;
            }

            if ($pedido['STATUS'] === 'PAGO') {
                $_SESSION['warning'] = 'Pedido já está marcado como pago';
                $this->redirect('/pedidos');
                return;
            }

            $this->pedidoModel->update($id, ['STATUS' => 'PAGO']);
            $_SESSION['success'] = 'Pedido marcado como PAGO com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'marcar pedido como pago');
        }

        $this->redirect('/pedidos');
    }

    public function cancel(int $id): void
    {
        try {
            $pedido = $this->pedidoModel->find($id);
            
            if (!$pedido) {
                $_SESSION['error'] = 'Pedido não encontrado';
                $this->redirect('/pedidos');
                return;
            }

            if ($pedido['STATUS'] === 'CANCELADO') {
                $_SESSION['warning'] = 'Pedido já está cancelado';
                $this->redirect('/pedidos');
                return;
            }

            $this->pedidoModel->update($id, ['STATUS' => 'CANCELADO']);
            $_SESSION['success'] = 'Pedido cancelado com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'cancelar pedido');
        }

        $this->redirect('/pedidos');
    }
}
