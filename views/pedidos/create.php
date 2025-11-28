<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Novo Pedido</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/pedidos/store" id="pedidoForm">
                    <div class="mb-3">
                        <label for="ID_CLIENTE" class="form-label">Cliente *</label>
                        <select class="form-select <?php echo isset($_SESSION['errors']['ID_CLIENTE']) ? 'is-invalid' : ''; ?>" 
                                id="ID_CLIENTE" name="ID_CLIENTE" required>
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['ID_CLIENTE']; ?>" 
                                    <?php echo ($_SESSION['old']['ID_CLIENTE'] ?? '') == $cliente['ID_CLIENTE'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cliente['NOME_CLIENTE']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errors']['ID_CLIENTE'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['errors']['ID_CLIENTE']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="STATUS" class="form-label">Status *</label>
                        <select class="form-select <?php echo isset($_SESSION['errors']['STATUS']) ? 'is-invalid' : ''; ?>" 
                                id="STATUS" name="STATUS" required>
                            <option value="EM_ABERTO" <?php echo ($_SESSION['old']['STATUS'] ?? 'EM_ABERTO') === 'EM_ABERTO' ? 'selected' : ''; ?>>EM ABERTO</option>
                            <option value="PAGO" <?php echo ($_SESSION['old']['STATUS'] ?? '') === 'PAGO' ? 'selected' : ''; ?>>PAGO</option>
                            <option value="CANCELADO" <?php echo ($_SESSION['old']['STATUS'] ?? '') === 'CANCELADO' ? 'selected' : ''; ?>>CANCELADO</option>
                        </select>
                        <?php if (isset($_SESSION['errors']['STATUS'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['errors']['STATUS']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="OBSERVACAO" class="form-label">Observações</label>
                        <textarea class="form-control" id="OBSERVACAO" name="OBSERVACAO" rows="3"><?php echo htmlspecialchars($_SESSION['old']['OBSERVACAO'] ?? ''); ?></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Após criar o pedido, você poderá adicionar produtos.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/pedidos" class="btn btn-secondary">
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
