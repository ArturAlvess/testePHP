<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void
    {
        $this->addRoute('GET', $path, $controller, $method);
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->addRoute('POST', $path, $controller, $method);
    }

    public function put(string $path, string $controller, string $method): void
    {
        $this->addRoute('PUT', $path, $controller, $method);
    }

    public function delete(string $path, string $controller, string $method): void
    {
        $this->addRoute('DELETE', $path, $controller, $method);
    }

    public function patch(string $path, string $controller, string $method): void
    {
        $this->addRoute('PATCH', $path, $controller, $method);
    }

    private function addRoute(string $httpMethod, string $path, string $controller, string $method): void
    {
        $this->routes[] = [
            'http_method' => $httpMethod,
            'path' => $path,
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $uri = parse_url($requestUri, PHP_URL_PATH);

        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        $headers = getallheaders();
        if (isset($headers['X-HTTP-Method-Override'])) {
            $requestMethod = strtoupper($headers['X-HTTP-Method-Override']);
        }

        foreach ($this->routes as $route) {
            if ($route['http_method'] === $requestMethod && $this->matchPath($route['path'], $uri, $params)) {
                $controller = new $route['controller']();
                $method = $route['method'];
                $controller->$method(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        
        // Retornar JSON se for uma requisição API
        if (strpos($uri, '/api/') === 0) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Endpoint não encontrado',
                'errors' => null
            ]);
        } else {
            echo "Página não encontrada";
        }
    }

    private function matchPath(string $routePath, string $uri, &$params = []): bool
    {
        $params = [];
        
        // Converter rota em regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }
}
