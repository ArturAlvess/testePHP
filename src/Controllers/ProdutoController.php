<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ErrorHandler;
use App\Models\Produto;
use App\Middleware\AuthMiddleware;

class ProdutoController extends Controller
{
    private Produto $produtoModel;

    public function __construct()
    {
        AuthMiddleware::check();
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
            'ID_PRODUTO' => $_GET['id'] ?? '',
            'COD_BARRAS' => $_GET['cod_barras'] ?? '',
            'NOME_PRODUTO' => $_GET['nome'] ?? '',
            'VALOR_UNITARIO' => $_GET['valor'] ?? '',
        ];

        $orderBy = $_GET['order_by'] ?? 'ID_PRODUTO';
        $orderDirection = $_GET['order_dir'] ?? 'ASC';

        $produtos = $this->produtoModel->all($filters, $orderBy, $orderDirection, $perPage, $offset);
        $total = $this->produtoModel->count($filters);
        $totalPages = ceil($total / $perPage);

        $this->view('produtos.index', [
            'produtos' => $produtos,
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
        $this->view('produtos.create');
    }

    public function store(): void
    {
        $lojaUsuarioId = AuthMiddleware::lojaUsuarioId();
        
        $data = [
            'ID_LOJA_USUARIO' => $lojaUsuarioId,
            'COD_BARRAS' => $_POST['COD_BARRAS'] ?? '',
            'NOME_PRODUTO' => $_POST['NOME_PRODUTO'] ?? '',
            'DESCRICAO' => $_POST['DESCRICAO'] ?? '',
            'VALOR_UNITARIO' => $_POST['VALOR_UNITARIO'] ?? ''
        ];

        if (isset($_FILES['IMAGEM']) && $_FILES['IMAGEM']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadImagem($_FILES['IMAGEM']);
            if ($uploadResult['success']) {
                $data['IMAGEM_URL'] = $uploadResult['path'];
            } else {
                $_SESSION['error'] = $uploadResult['error'];
                $_SESSION['old'] = $data;
                $this->redirect('/produtos/create');
                return;
            }
        }

        $errors = $this->produtoModel->validate($data);

        if (!empty($errors)) {
            if (!empty($data['IMAGEM_URL'])) {
                $this->deleteImagem($data['IMAGEM_URL']);
            }
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect('/produtos/create');
        }

        try {
            $this->produtoModel->create($data);
            $_SESSION['success'] = 'Produto criado com sucesso!';
            $this->redirect('/produtos');
        } catch (\Exception $e) {
            if (!empty($data['IMAGEM_URL'])) {
                $this->deleteImagem($data['IMAGEM_URL']);
            }
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'criar produto');
            $_SESSION['old'] = $data;
            $this->redirect('/produtos/create');
        }
    }

    public function edit(int $id): void
    {
        $produto = $this->produtoModel->find($id);

        if (!$produto) {
            $_SESSION['error'] = 'Produto não encontrado';
            $this->redirect('/produtos');
        }

        $this->view('produtos.edit', ['produto' => $produto]);
    }

    public function update(int $id): void
    {
        $produto = $this->produtoModel->find($id);
        if (!$produto) {
            $_SESSION['error'] = 'Produto não encontrado';
            $this->redirect('/produtos');
            return;
        }

        $data = [
            'COD_BARRAS' => $_POST['COD_BARRAS'] ?? '',
            'NOME_PRODUTO' => $_POST['NOME_PRODUTO'] ?? '',
            'DESCRICAO' => $_POST['DESCRICAO'] ?? '',
            'VALOR_UNITARIO' => $_POST['VALOR_UNITARIO'] ?? ''
        ];

        if (isset($_POST['REMOVER_IMAGEM']) && $_POST['REMOVER_IMAGEM'] == '1') {
            if (!empty($produto['IMAGEM_URL'])) {
                $this->deleteImagem($produto['IMAGEM_URL']);
            }
            $data['IMAGEM_URL'] = null;
        }

        if (isset($_FILES['IMAGEM']) && $_FILES['IMAGEM']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadImagem($_FILES['IMAGEM']);
            if ($uploadResult['success']) {
                if (!empty($produto['IMAGEM_URL'])) {
                    $this->deleteImagem($produto['IMAGEM_URL']);
                }
                $data['IMAGEM_URL'] = $uploadResult['path'];
            } else {
                $_SESSION['error'] = $uploadResult['error'];
                $_SESSION['old'] = $data;
                $this->redirect("/produtos/edit/{$id}");
                return;
            }
        }

        $errors = $this->produtoModel->validate($data);

        if (!empty($errors)) {
            if (isset($uploadResult) && $uploadResult['success']) {
                $this->deleteImagem($data['IMAGEM_URL']);
            }
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect("/produtos/edit/{$id}");
        }

        try {
            $this->produtoModel->update($id, $data);
            $_SESSION['success'] = 'Produto atualizado com sucesso!';
            $this->redirect('/produtos');
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'atualizar produto');
            $_SESSION['old'] = $data;
            $this->redirect("/produtos/edit/{$id}");
        }
    }

    public function delete(int $id): void
    {
        try {
            $produto = $this->produtoModel->find($id);
            if ($produto && !empty($produto['IMAGEM_URL'])) {
                $this->deleteImagem($produto['IMAGEM_URL']);
            }
            
            $this->produtoModel->delete($id);
            $_SESSION['success'] = 'Produto excluído com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = ErrorHandler::handleGenericError($e, 'excluir produto');
        }
        $this->redirect('/produtos');
    }

    public function deleteMultiple(): void
    {
        $ids = $_POST['ids'] ?? [];
        
        if (empty($ids)) {
            $_SESSION['error'] = 'Nenhum produto selecionado para exclusão';
            $this->redirect('/produtos');
            return;
        }

        $deleted = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $produto = $this->produtoModel->find((int)$id);
                if ($produto && !empty($produto['IMAGEM_URL'])) {
                    $this->deleteImagem($produto['IMAGEM_URL']);
                }
                $this->produtoModel->delete((int)$id);
                $deleted++;
            } catch (\Exception $e) {
                $errors[] = "Produto ID {$id}: " . ErrorHandler::handleGenericError($e, 'excluir');
            }
        }

        if ($deleted > 0) {
            $_SESSION['success'] = "{$deleted} produto(s) excluído(s) com sucesso!";
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = 'Alguns produtos não puderam ser excluídos: ' . implode('; ', $errors);
        }

        $this->redirect('/produtos');
    }

    public function show(int $id): void
    {
        $produto = $this->produtoModel->find($id);

        if (!$produto) {
            $_SESSION['error'] = 'Produto não encontrado';
            $this->redirect('/produtos');
        }

        $this->view('produtos.view', ['produto' => $produto]);
    }
    private function uploadImagem(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'error' => ErrorHandler::handleFileError($file['error'], 'imagem')];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        $extensionError = ErrorHandler::validateFileExtension($file['name'], $allowedExtensions);
        if ($extensionError) {
            return ['success' => false, 'error' => $extensionError];
        }

        $sizeError = ErrorHandler::validateFileSize($file['size'], 5 * 1024 * 1024);
        if ($sizeError) {
            return ['success' => false, 'error' => $sizeError];
        }

        $filename = uniqid('produto_') . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/produtos/';
        $uploadPath = $uploadDir . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => true, 'path' => '/uploads/produtos/' . $filename];
        }

        return ['success' => false, 'error' => 'Erro ao salvar o arquivo no servidor.'];
    }


    private function deleteImagem(string $imagemUrl): void
    {
        if (empty($imagemUrl)) return;
        
        $filepath = __DIR__ . '/../../public' . $imagemUrl;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
