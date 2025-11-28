<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Carregar .env se existir
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

session_start();

$router = new App\Core\Router();

// Rotas de Autenticação
$router->get('/login', App\Controllers\AuthController::class, 'login');
$router->post('/login', App\Controllers\AuthController::class, 'authenticate');
$router->get('/logout', App\Controllers\AuthController::class, 'logout');

// Rotas de Registro
$router->get('/registro', App\Controllers\RegistroController::class, 'index');
$router->post('/registro', App\Controllers\RegistroController::class, 'store');

$router->get('/', App\Controllers\HomeController::class, 'index');

// Rotas de Clientes
$router->get('/clientes', App\Controllers\ClienteController::class, 'index');
$router->get('/clientes/create', App\Controllers\ClienteController::class, 'create');
$router->post('/clientes/store', App\Controllers\ClienteController::class, 'store');
$router->get('/clientes/view/{id}', App\Controllers\ClienteController::class, 'show');
$router->get('/clientes/edit/{id}', App\Controllers\ClienteController::class, 'edit');
$router->post('/clientes/update/{id}', App\Controllers\ClienteController::class, 'update');
$router->post('/clientes/delete/{id}', App\Controllers\ClienteController::class, 'delete');
$router->post('/clientes/delete-multiple', App\Controllers\ClienteController::class, 'deleteMultiple');

// Rotas de Produtos
$router->get('/produtos', App\Controllers\ProdutoController::class, 'index');
$router->get('/produtos/create', App\Controllers\ProdutoController::class, 'create');
$router->post('/produtos/store', App\Controllers\ProdutoController::class, 'store');
$router->get('/produtos/view/{id}', App\Controllers\ProdutoController::class, 'show');
$router->get('/produtos/edit/{id}', App\Controllers\ProdutoController::class, 'edit');
$router->post('/produtos/update/{id}', App\Controllers\ProdutoController::class, 'update');
$router->post('/produtos/delete/{id}', App\Controllers\ProdutoController::class, 'delete');
$router->post('/produtos/delete-multiple', App\Controllers\ProdutoController::class, 'deleteMultiple');

// Rotas de Pedidos
$router->get('/pedidos', App\Controllers\PedidoController::class, 'index');
$router->get('/pedidos/create', App\Controllers\PedidoController::class, 'create');
$router->post('/pedidos/store', App\Controllers\PedidoController::class, 'store');
$router->get('/pedidos/view/{id}', App\Controllers\PedidoController::class, 'show');
$router->get('/pedidos/edit/{id}', App\Controllers\PedidoController::class, 'edit');
$router->post('/pedidos/update/{id}', App\Controllers\PedidoController::class, 'update');
$router->post('/pedidos/delete/{id}', App\Controllers\PedidoController::class, 'delete');
$router->post('/pedidos/delete-multiple', App\Controllers\PedidoController::class, 'deleteMultiple');
$router->post('/pedidos/mark-as-paid/{id}', App\Controllers\PedidoController::class, 'markAsPaid');
$router->post('/pedidos/cancel/{id}', App\Controllers\PedidoController::class, 'cancel');
$router->post('/pedidos/{id}/add-item', App\Controllers\PedidoController::class, 'addItem');
$router->post('/pedidos/{id}/remove-item/{itemId}', App\Controllers\PedidoController::class, 'removeItem');
$router->post('/pedidos/{id}/apply-discount', App\Controllers\PedidoController::class, 'applyDiscount');
$router->post('/pedidos/{id}/remove-discount', App\Controllers\PedidoController::class, 'removeDiscount');

// ============================================
// API REST JSON ROUTES
// ============================================

// API Auth
$router->post('/api/auth/login', App\Controllers\Api\AuthApiController::class, 'login');
$router->post('/api/auth/logout', App\Controllers\Api\AuthApiController::class, 'logout');
$router->get('/api/auth/me', App\Controllers\Api\AuthApiController::class, 'me');

// API Clientes
$router->get('/api/clientes', App\Controllers\Api\ClienteApiController::class, 'index');
$router->get('/api/clientes/{id}', App\Controllers\Api\ClienteApiController::class, 'show');
$router->post('/api/clientes', App\Controllers\Api\ClienteApiController::class, 'store');
$router->put('/api/clientes/{id}', App\Controllers\Api\ClienteApiController::class, 'update');
$router->delete('/api/clientes/{id}', App\Controllers\Api\ClienteApiController::class, 'delete');

// API Produtos
$router->get('/api/produtos', App\Controllers\Api\ProdutoApiController::class, 'index');
$router->get('/api/produtos/{id}', App\Controllers\Api\ProdutoApiController::class, 'show');
$router->post('/api/produtos', App\Controllers\Api\ProdutoApiController::class, 'store');
$router->put('/api/produtos/{id}', App\Controllers\Api\ProdutoApiController::class, 'update');
$router->delete('/api/produtos/{id}', App\Controllers\Api\ProdutoApiController::class, 'delete');

// API Pedidos
$router->get('/api/pedidos', App\Controllers\Api\PedidoApiController::class, 'index');
$router->get('/api/pedidos/{id}', App\Controllers\Api\PedidoApiController::class, 'show');
$router->post('/api/pedidos', App\Controllers\Api\PedidoApiController::class, 'store');
$router->put('/api/pedidos/{id}', App\Controllers\Api\PedidoApiController::class, 'update');
$router->delete('/api/pedidos/{id}', App\Controllers\Api\PedidoApiController::class, 'delete');

// API Pedidos - Itens
$router->post('/api/pedidos/{id}/itens', App\Controllers\Api\PedidoApiController::class, 'addItem');
$router->delete('/api/pedidos/{id}/itens/{itemId}', App\Controllers\Api\PedidoApiController::class, 'removeItem');

// API Pedidos - Desconto
$router->post('/api/pedidos/{id}/desconto', App\Controllers\Api\PedidoApiController::class, 'applyDiscount');
$router->delete('/api/pedidos/{id}/desconto', App\Controllers\Api\PedidoApiController::class, 'removeDiscount');

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
