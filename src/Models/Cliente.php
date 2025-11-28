<?php

namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    protected string $table = 'CLIENTE';
    protected string $primaryKey = 'ID_CLIENTE';

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['NOME_CLIENTE'])) {
            $errors['NOME_CLIENTE'] = 'Nome é obrigatório';
        }

        if (empty($data['CPF'])) {
            $errors['CPF'] = 'CPF é obrigatório';
        } elseif (!$this->validarCPF($data['CPF'])) {
            $errors['CPF'] = 'CPF inválido';
        }

        if (!empty($data['EMAIL']) && !filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
            $errors['EMAIL'] = 'Email inválido';
        }

        return $errors;
    }

    private function validarCPF(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;
        
        if (intval($cpf[9]) !== $digito1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;
        
        if (intval($cpf[10]) !== $digito2) {
            return false;
        }

        return true;
    }

    public function getPedidos(int $clienteId): array
    {
        $sql = "SELECT * FROM PEDIDO WHERE ID_CLIENTE = :id ORDER BY DATA_PEDIDO DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $clienteId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function canDelete(int $clienteId): array
    {
        $sql = "SELECT COUNT(*) as total, 
                       SUM(CASE WHEN STATUS IN ('EM_ABERTO', 'CANCELADO') THEN 1 ELSE 0 END) as pendentes
                FROM PEDIDO 
                WHERE ID_CLIENTE = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $clienteId);
        $stmt->execute();
        $result = $stmt->fetch();

        $total = (int) $result['total'];
        $pendentes = (int) $result['pendentes'];

        if ($total === 0) {
            return ['can_delete' => true, 'message' => ''];
        }

        if ($pendentes > 0) {
            return [
                'can_delete' => false, 
                'message' => "Cliente possui {$pendentes} pedido(s) em aberto ou cancelado(s). Apenas clientes com todos os pedidos pagos podem ser excluídos."
            ];
        }

        return ['can_delete' => true, 'message' => ''];
    }

    public function getNome(int $clienteId): ?string
    {
        $cliente = $this->find($clienteId);
        return $cliente ? $cliente['NOME_CLIENTE'] : null;
    }
}
