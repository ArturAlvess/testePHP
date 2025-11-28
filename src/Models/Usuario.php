<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Usuario extends Model
{
    protected static $table = 'USUARIO';
    protected static $primaryKey = 'ID_USUARIO';
    public static function findByEmail(string $email): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM " . self::$table . " WHERE EMAIL = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function emailExists(string $email, ?int $excludeId = null): bool
    {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM " . self::$table . " WHERE EMAIL = :email";
        
        if ($excludeId) {
            $sql .= " AND " . self::$primaryKey . " != :id";
        }
        
        $stmt = $db->prepare($sql);
        $params = ['email' => $email];
        
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
        
        return self::create($data);
    }

    public static function updateWithPassword(int $id, array $data): bool
    {
        if (isset($data['SENHA']) && !empty($data['SENHA'])) {
            $data['SENHA'] = password_hash($data['SENHA'], PASSWORD_DEFAULT);
        } else {
            unset($data['SENHA']);
        }
        
        return self::update($id, $data);
    }

    public static function authenticate(string $email, string $senha): ?array
    {
        $user = self::findByEmail($email);
        
        if (!$user || !password_verify($senha, $user['SENHA'])) {
            return null;
        }

        if (!$user['ATIVO']) {
            return null;
        }

        unset($user['SENHA']);
        return $user;
    }

    public static function findWithLoja(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, l.NOME_LOJA, l.CNPJ, l.ATIVA as LOJA_ATIVA
            FROM " . self::$table . " u
            LEFT JOIN LOJA l ON u.ID_LOJA = l.ID_LOJA
            WHERE u." . self::$primaryKey . " = :id
        ");
        $stmt->execute(['id' => $id]);
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}
