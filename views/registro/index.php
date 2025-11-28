<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-lg-7">
            <div class="card shadow-lg">
                <div class="card-header">
                    <i class="bi bi-person-plus-fill"></i> Cadastrar Nova Conta
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        <i class="bi bi-info-circle"></i> 
                        Crie sua conta e comece a gerenciar seus clientes, produtos e pedidos.
                    </p>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/registro">
                        <!-- Dados do Usuário -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-person-badge"></i> Seus Dados
                                </h5>

                                <div class="mb-3">
                                    <label for="nome_usuario" class="form-label">
                                        <i class="bi bi-person"></i> Nome Completo *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nome_usuario" 
                                           name="nome_usuario"
                                           value="<?php echo htmlspecialchars($_SESSION['form_data']['nome_usuario'] ?? ''); ?>"
                                           placeholder="Seu nome completo"
                                           required
                                           autofocus>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            <i class="bi bi-envelope-at"></i> Email de Acesso *
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email"
                                               value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>"
                                               placeholder="seu@email.com"
                                               required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="nome_loja" class="form-label">
                                            <i class="bi bi-shop"></i> Nome da Loja *
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nome_loja" 
                                               name="nome_loja"
                                               value="<?php echo htmlspecialchars($_SESSION['form_data']['nome_loja'] ?? ''); ?>"
                                               placeholder="Nome da sua loja"
                                               required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="senha" class="form-label">
                                            <i class="bi bi-lock"></i> Senha *
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="senha" 
                                               name="senha"
                                               placeholder="Mínimo 6 caracteres"
                                               minlength="6"
                                               required>
                                        <small class="text-muted">Mínimo de 6 caracteres</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="senha_confirm" class="form-label">
                                            <i class="bi bi-lock-fill"></i> Confirmar Senha *
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="senha_confirm" 
                                               name="senha_confirm"
                                               placeholder="Digite a senha novamente"
                                               minlength="6"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados da Loja -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-building"></i> Dados da Loja <small class="text-muted">(opcionais)</small>
                                </h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cnpj" class="form-label">
                                            <i class="bi bi-file-text"></i> CNPJ
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="cnpj" 
                                               name="cnpj"
                                               value="<?php echo htmlspecialchars($_SESSION['form_data']['cnpj'] ?? ''); ?>"
                                               placeholder="00.000.000/0000-00"
                                               maxlength="18">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="cep_loja" class="form-label">
                                            <i class="bi bi-mailbox"></i> CEP
                                        </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="cep_loja" 
                                                   name="cep_loja"
                                                   value="<?php echo htmlspecialchars($_SESSION['form_data']['cep_loja'] ?? ''); ?>"
                                                   placeholder="00000-000"
                                                   maxlength="9">
                                            <button class="btn btn-outline-secondary" type="button" id="buscarCepLoja">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Digite o CEP e clique na lupa</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="endereco_loja" class="form-label">
                                        <i class="bi bi-geo-alt"></i> Endereço
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="endereco_loja" 
                                           name="endereco_loja"
                                           value="<?php echo htmlspecialchars($_SESSION['form_data']['endereco_loja'] ?? ''); ?>"
                                           placeholder="Rua, número, bairro, cidade">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-between">
                            <a href="/login" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar ao Login
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Criar Conta
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    Já tem uma conta? <a href="/login">Faça login aqui</a>
                </small>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['form_data']); ?>

<script>
// Buscar endereço por CEP usando ViaCEP
document.getElementById('buscarCepLoja')?.addEventListener('click', function() {
    const cep = document.getElementById('cep_loja').value.replace(/\D/g, '');
    
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
            document.getElementById('endereco_loja').value = endereco;
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
document.getElementById('cep_loja')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 5) {
        value = value.slice(0, 5) + '-' + value.slice(5, 8);
    }
    e.target.value = value;
});

// Máscara para CNPJ
document.getElementById('cnpj').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 14) {
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        e.target.value = value;
    }
});
</script>
