<?php

namespace App\Core;

class ErrorHandler
{
    /** Adaptando erros não amigáveis */
    public static function handleSqlError(\PDOException $e, string $context = 'operação'): string
    {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();

        // Erro de dados muito longos
        if (strpos($errorMessage, 'Data too long for column') !== false) {
            preg_match("/column '(\w+)'/", $errorMessage, $matches);
            $column = $matches[1] ?? 'campo';
            return self::formatFieldName($column) . ' é muito longo. Por favor, use um valor menor.';
        }

        // Erro de chave duplicada
        if ($errorCode == 23000 || strpos($errorMessage, 'Duplicate entry') !== false) {
            if (strpos($errorMessage, 'CPF') !== false) {
                return 'Este CPF já está cadastrado no sistema.';
            }
            if (strpos($errorMessage, 'CNPJ') !== false) {
                return 'Este CNPJ já está cadastrado no sistema.';
            }
            if (strpos($errorMessage, 'EMAIL') !== false) {
                return 'Este email já está cadastrado no sistema.';
            }
            if (strpos($errorMessage, 'COD_BARRAS') !== false) {
                return 'Este código de barras já está cadastrado no sistema.';
            }
            return 'Este registro já existe no sistema.';
        }

        // Erro de constraint de chave estrangeira
        if ($errorCode == 23000 || strpos($errorMessage, 'foreign key constraint') !== false) {
            if (strpos($errorMessage, 'DELETE') !== false) {
                return 'Não é possível excluir este registro pois existem outros dados vinculados a ele.';
            }
            if (strpos($errorMessage, 'ID_CLIENTE') !== false) {
                return 'Cliente não encontrado ou inválido.';
            }
            if (strpos($errorMessage, 'ID_PRODUTO') !== false) {
                return 'Produto não encontrado ou inválido.';
            }
            if (strpos($errorMessage, 'ID_PEDIDO') !== false) {
                return 'Pedido não encontrado ou inválido.';
            }
            return 'Erro de relacionamento entre dados. Verifique se todos os dados vinculados existem.';
        }
        if (strpos($errorMessage, "cannot be null") !== false) {
            preg_match("/Column '(\w+)'/", $errorMessage, $matches);
            $column = $matches[1] ?? 'campo';
            return self::formatFieldName($column) . ' é obrigatório e não pode estar vazio.';
        }

        if (strpos($errorMessage, 'Out of range') !== false) {
            preg_match("/column '(\w+)'/", $errorMessage, $matches);
            $column = $matches[1] ?? 'campo';
            return self::formatFieldName($column) . ' possui um valor muito grande ou muito pequeno.';
        }

        if (strpos($errorMessage, 'Incorrect') !== false && strpos($errorMessage, 'value') !== false) {
            preg_match("/column '(\w+)'/", $errorMessage, $matches);
            $column = $matches[1] ?? 'campo';
            return self::formatFieldName($column) . ' possui um formato inválido.';
        }

        if (strpos($errorMessage, 'Incorrect datetime') !== false || strpos($errorMessage, 'Incorrect date') !== false) {
            return 'Data/hora em formato inválido.';
        }

        if (strpos($errorMessage, 'Connection refused') !== false || strpos($errorMessage, 'Connection timed out') !== false) {
            return 'Não foi possível conectar ao banco de dados. Tente novamente em instantes.';
        }
        if (strpos($errorMessage, "doesn't exist") !== false) {
            return 'Erro de estrutura do banco de dados. Contate o administrador do sistema.';
        }

        return "Erro ao realizar {$context}. Por favor, verifique os dados informados e tente novamente.";
    }

    /** Formatando campos */
    private static function formatFieldName(string $field): string
    {
        $fieldNames = [
            'NOME_CLIENTE' => 'Nome do cliente',
            'CPF' => 'CPF',
            'EMAIL' => 'Email',
            'TELEFONE' => 'Telefone',
            'ENDERECO' => 'Endereço',
            'CEP' => 'CEP',
            'COD_BARRAS' => 'Código de barras',
            'NOME_PRODUTO' => 'Nome do produto',
            'DESCRICAO' => 'Descrição',
            'VALOR_UNITARIO' => 'Valor unitário',
            'VALOR' => 'Valor',
            'QUANTIDADE' => 'Quantidade',
            'ID_CLIENTE' => 'Cliente',
            'ID_PRODUTO' => 'Produto',
            'ID_PEDIDO' => 'Pedido',
            'STATUS' => 'Status',
            'OBSERVACAO' => 'Observação',
            'DATA_PEDIDO' => 'Data do pedido',
            'NOME_USUARIO' => 'Nome do usuário',
            'NOME_LOJA' => 'Nome da loja',
            'CNPJ' => 'CNPJ',
            'ENDERECO_LOJA' => 'Endereço da loja',
            'SENHA' => 'Senha',
        ];

        return $fieldNames[$field] ?? ucfirst(strtolower(str_replace('_', ' ', $field)));
    }

    /** Traduz erros de validação de arquivo  */
    public static function handleFileError(int $errorCode, string $fieldName = 'arquivo'): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "O {$fieldName} é muito grande. Tamanho máximo permitido: 5MB.";
            
            case UPLOAD_ERR_PARTIAL:
                return "O upload do {$fieldName} foi interrompido. Tente novamente.";
            
            case UPLOAD_ERR_NO_FILE:
                return "Nenhum {$fieldName} foi selecionado.";
            
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Erro no servidor: diretório temporário não encontrado.";
            
            case UPLOAD_ERR_CANT_WRITE:
                return "Erro ao salvar o {$fieldName} no servidor.";
            
            case UPLOAD_ERR_EXTENSION:
                return "O upload foi bloqueado por uma extensão do servidor.";
            
            default:
                return "Erro desconhecido ao fazer upload do {$fieldName}.";
        }
    }

    public static function validateFileSize(int $size, int $maxSize = 5242880): ?string
    {
        if ($size > $maxSize) {
            $maxMB = $maxSize / 1048576;
            return "O arquivo é muito grande. Tamanho máximo: {$maxMB}MB.";
        }
        return null;
    }
    public static function validateFileExtension(string $filename, array $allowedExtensions): ?string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            $allowed = implode(', ', array_map('strtoupper', $allowedExtensions));
            return "Tipo de arquivo não permitido. Permitidos: {$allowed}.";
        }
        
        return null;
    }

    /** Trata erros genéricos e retorna mensagem amigavel*/
    public static function handleGenericError(\Exception $e, string $context = 'operação'): string
    {
        if ($e instanceof \PDOException) {
            return self::handleSqlError($e, $context);
        }
        error_log("Erro em {$context}: " . $e->getMessage());

        return "Erro ao realizar {$context}. Por favor, tente novamente.";
    }
}
