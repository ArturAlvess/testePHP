<?php

namespace App\Models;

use App\Core\Model;

class Produto extends Model
{
    protected string $table = 'PRODUTO';
    protected string $primaryKey = 'ID_PRODUTO';

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['COD_BARRAS'])) {
            $errors['COD_BARRAS'] = 'Código de barras é obrigatório';
        }

        if (empty($data['NOME_PRODUTO'])) {
            $errors['NOME_PRODUTO'] = 'Nome do produto é obrigatório';
        }

        if (empty($data['VALOR_UNITARIO'])) {
            $errors['VALOR_UNITARIO'] = 'Valor unitário é obrigatório';
        } elseif (!is_numeric($data['VALOR_UNITARIO']) || $data['VALOR_UNITARIO'] <= 0) {
            $errors['VALOR_UNITARIO'] = 'Valor unitário deve ser maior que zero';
        }

        return $errors;
    }
}
