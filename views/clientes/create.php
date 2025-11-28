<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Novo Cliente</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/clientes/store" id="clienteForm">
                    <div class="mb-3">
                        <label for="NOME_CLIENTE" class="form-label">Nome *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['errors']['NOME_CLIENTE']) ? 'is-invalid' : ''; ?>" 
                               id="NOME_CLIENTE" name="NOME_CLIENTE" required 
                               value="<?php echo htmlspecialchars($_SESSION['old']['NOME_CLIENTE'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['NOME_CLIENTE'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['errors']['NOME_CLIENTE']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="CPF" class="form-label">CPF *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['errors']['CPF']) ? 'is-invalid' : ''; ?>" 
                               id="CPF" name="CPF" required maxlength="14" 
                               placeholder="000.000.000-00"
                               value="<?php echo htmlspecialchars($_SESSION['old']['CPF'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['CPF'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['errors']['CPF']; ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Digite apenas números</small>
                    </div>

                    <div class="mb-3">
                        <label for="EMAIL" class="form-label">Email</label>
                        <input type="email" class="form-control <?php echo isset($_SESSION['errors']['EMAIL']) ? 'is-invalid' : ''; ?>" 
                               id="EMAIL" name="EMAIL" 
                               value="<?php echo htmlspecialchars($_SESSION['old']['EMAIL'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['EMAIL'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['errors']['EMAIL']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="TELEFONE" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="TELEFONE" name="TELEFONE" 
                               value="<?php echo htmlspecialchars($_SESSION['old']['TELEFONE'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="CEP" class="form-label">CEP</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="CEP" name="CEP" 
                                   placeholder="00000-000" maxlength="9"
                                   value="<?php echo htmlspecialchars($_SESSION['old']['CEP'] ?? ''); ?>">
                            <button class="btn btn-outline-secondary" type="button" id="buscarCep">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <small class="text-muted">Digite o CEP e clique na lupa</small>
                    </div>

                    <div class="mb-3">
                        <label for="ENDERECO" class="form-label">Endereço</label>
                        <textarea class="form-control" id="ENDERECO" name="ENDERECO" rows="3"><?php echo htmlspecialchars($_SESSION['old']['ENDERECO'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/clientes" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Buscar endereço por CEP
document.getElementById('buscarCep')?.addEventListener('click', function() {
    const cep = document.getElementById('CEP').value.replace(/\D/g, '');
    
    if (cep.length !== 8) {
        alert('Por favor, digite um CEP válido com 8 dígitos');
        return;
    }
    
    const btn = this;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    btn.disabled = true;
    
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert('CEP não encontrado');
                return;
            }
            
            const endereco = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
            document.getElementById('ENDERECO').value = endereco;
        })
        .catch(error => {
            console.error('Erro ao buscar CEP:', error);
            alert('Erro ao buscar CEP. Tente novamente.');
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
});

// Máscara de CEP
document.getElementById('CEP')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 5) {
        value = value.slice(0, 5) + '-' + value.slice(5, 8);
    }
    e.target.value = value;
});
</script>
