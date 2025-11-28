<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class LojaUsuario extends Model
{
    protected string $table = 'LOJA_USUARIO';
    protected string $primaryKey = 'ID_LOJA_USUARIO';


    public static function findByEmail(string $email): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM LOJA_USUARIO WHERE EMAIL = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function emailExists(string $email, ?int $excludeId = null): bool
    {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM LOJA_USUARIO WHERE EMAIL = :email";
        
        if ($excludeId) {
            $sql .= " AND ID_LOJA_USUARIO != :id";
        }
        
        $stmt = $db->prepare($sql);
        $params = ['email' => $email];
        
        if ($excludeId) {
            $params['id'] = $excludeId;
        }
        
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public static function cnpjExists(string $cnpj, ?int $excludeId = null): bool
    {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM LOJA_USUARIO WHERE CNPJ = :cnpj";
        
        if ($excludeId) {
            $sql .= " AND ID_LOJA_USUARIO != :id";
        }
        
        $stmt = $db->prepare($sql);
        $params = ['cnpj' => $cnpj];
        
        if ($excludeId) {
            $params['id'] = $excludeId;
        }
        
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public static function createWithHashedPassword(array $data): int
    {
        if (isset($data['SENHA'])) {
            $data['SENHA'] = password_hash($data['SENHA'], PASSWORD_DEFAULT);
        }
        
        $instance = new self();
        return $instance->create($data);
    }

    public static function authenticate(string $email, string $senha): ?array
    {
        $user = self::findByEmail($email);
        
        if (!$user || !password_verify($senha, $user['SENHA'])) {
            return null;
        }
        
        unset($user['SENHA']);
        return $user;
    }

    public static function countClientes(int $lojaUsuarioId): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM CLIENTE WHERE ID_LOJA_USUARIO = :id");
        $stmt->execute(['id' => $lojaUsuarioId]);
        
        return (int) $stmt->fetchColumn();
    }

    public static function countProdutos(int $lojaUsuarioId): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM PRODUTO WHERE ID_LOJA_USUARIO = :id");
        $stmt->execute(['id' => $lojaUsuarioId]);
        
        return (int) $stmt->fetchColumn();
    }

    public static function countPedidos(int $lojaUsuarioId): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM PEDIDO WHERE ID_LOJA_USUARIO = :id");
        $stmt->execute(['id' => $lojaUsuarioId]);
        
        return (int) $stmt->fetchColumn();
    }
}
