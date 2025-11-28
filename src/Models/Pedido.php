<?php

namespace App\Models;

use App\Core\Model;

class Pedido extends Model
{
    protected string $table = 'PEDIDO';
    protected string $primaryKey = 'ID_PEDIDO';

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['ID_CLIENTE'])) {
            $errors['ID_CLIENTE'] = 'Cliente é obrigatório';
        }

        if (empty($data['STATUS'])) {
            $errors['STATUS'] = 'Status é obrigatório';
        } elseif (!in_array($data['STATUS'], ['EM_ABERTO', 'PAGO', 'CANCELADO'])) {
            $errors['STATUS'] = 'Status inválido';
        }

        return $errors;
    }

    public function getWithCliente(int $id): ?array
    {
        $sql = "SELECT p.*, c.NOME_CLIENTE 
                FROM PEDIDO p 
                INNER JOIN CLIENTE c ON p.ID_CLIENTE = c.ID_CLIENTE 
                WHERE p.ID_PEDIDO = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAllWithCliente(array $filters = [], string $orderBy = '', string $orderDirection = 'ASC', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT p.*, c.NOME_CLIENTE 
                FROM PEDIDO p 
                INNER JOIN CLIENTE c ON p.ID_CLIENTE = c.ID_CLIENTE";
        
        $params = [];

        // Filtros
        if (!empty($filters)) {
            $conditions = [];
            
            // Filtro obrigatório por ID_LOJA_USUARIO
            if (!empty($filters['ID_LOJA_USUARIO'])) {
                $conditions[] = "p.ID_LOJA_USUARIO = :filter_loja_usuario";
                $params['filter_loja_usuario'] = $filters['ID_LOJA_USUARIO'];
            }
            
            if (!empty($filters['ID_PEDIDO'])) {
                $conditions[] = "p.ID_PEDIDO LIKE :filter_id";
                $params['filter_id'] = "%{$filters['ID_PEDIDO']}%";
            }
            
            if (!empty($filters['ID_CLIENTE'])) {
                $conditions[] = "p.ID_CLIENTE = :filter_cliente";
                $params['filter_cliente'] = $filters['ID_CLIENTE'];
            }
            
            if (!empty($filters['STATUS'])) {
                $conditions[] = "p.STATUS = :filter_status";
                $params['filter_status'] = $filters['STATUS'];
            }
            
            if (!empty($filters['DATA_PEDIDO'])) {
                $conditions[] = "DATE(p.DATA_PEDIDO) = :filter_data";
                $params['filter_data'] = $filters['DATA_PEDIDO'];
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
        }

        // Ordenação
        if ($orderBy) {
            $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$orderBy} {$orderDirection}";
        }

        // Paginação
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM PEDIDO p 
                INNER JOIN CLIENTE c ON p.ID_CLIENTE = c.ID_CLIENTE";
        
        $params = [];

        if (!empty($filters)) {
            $conditions = [];
            
            // Filtro obrigatório por ID_LOJA_USUARIO
            if (!empty($filters['ID_LOJA_USUARIO'])) {
                $conditions[] = "p.ID_LOJA_USUARIO = :filter_loja_usuario";
                $params['filter_loja_usuario'] = $filters['ID_LOJA_USUARIO'];
            }
            
            if (!empty($filters['ID_PEDIDO'])) {
                $conditions[] = "p.ID_PEDIDO LIKE :filter_id";
                $params['filter_id'] = "%{$filters['ID_PEDIDO']}%";
            }
            
            if (!empty($filters['ID_CLIENTE'])) {
                $conditions[] = "p.ID_CLIENTE = :filter_cliente";
                $params['filter_cliente'] = $filters['ID_CLIENTE'];
            }
            
            if (!empty($filters['STATUS'])) {
                $conditions[] = "p.STATUS = :filter_status";
                $params['filter_status'] = $filters['STATUS'];
            }
            
            if (!empty($filters['DATA_PEDIDO'])) {
                $conditions[] = "DATE(p.DATA_PEDIDO) = :filter_data";
                $params['filter_data'] = $filters['DATA_PEDIDO'];
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        $stmt->execute();
        return (int) $stmt->fetch()['total'];
    }

    public function getItens(int $pedidoId): array
    {
        $sql = "SELECT ip.*, p.NOME_PRODUTO, p.COD_BARRAS
                FROM ITEM_PEDIDO ip
                INNER JOIN PRODUTO p ON ip.ID_PRODUTO = p.ID_PRODUTO
                WHERE ip.ID_PEDIDO = :id
                ORDER BY ip.ID_ITEM";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $pedidoId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function addItem(int $pedidoId, int $produtoId, int $quantidade, float $valorUnitario): int
    {
        $sql = "INSERT INTO ITEM_PEDIDO (ID_PEDIDO, ID_PRODUTO, QUANTIDADE, VALOR_UNITARIO) 
                VALUES (:pedido, :produto, :quantidade, :valor)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pedido', $pedidoId);
        $stmt->bindValue(':produto', $produtoId);
        $stmt->bindValue(':quantidade', $quantidade);
        $stmt->bindValue(':valor', $valorUnitario);
        $stmt->execute();
        
        return (int) $this->db->lastInsertId();
    }

    public function removeItem(int $itemId): bool
    {
        $sql = "DELETE FROM ITEM_PEDIDO WHERE ID_ITEM = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $itemId);
        return $stmt->execute();
    }

    public function getTotal(int $pedidoId): float
    {
        $sql = "SELECT SUM(QUANTIDADE * VALOR_UNITARIO) as total 
                FROM ITEM_PEDIDO 
                WHERE ID_PEDIDO = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $pedidoId);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return (float) ($result['total'] ?? 0);
    }

    public function updateDiscount(int $pedidoId, float $descontoPercentual, float $descontoValor, float $valorTotal): bool
    {
        $sql = "UPDATE PEDIDO 
                SET DESCONTO_PERCENTUAL = :percentual,
                    DESCONTO_VALOR = :valor,
                    VALOR_TOTAL = :total
                WHERE ID_PEDIDO = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':percentual', $descontoPercentual);
        $stmt->bindValue(':valor', $descontoValor);
        $stmt->bindValue(':total', $valorTotal);
        $stmt->bindValue(':id', $pedidoId);
        
        return $stmt->execute();
    }
}
