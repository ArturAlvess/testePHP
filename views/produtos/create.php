<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Novo Produto</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/produtos/store" id="produtoForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="IMAGEM" class="form-label">Imagem do Produto</label>
                        <input type="file" class="form-control" id="IMAGEM" name="IMAGEM" accept="image/*">
                        <small class="text-muted">Formatos aceitos: JPG, PNG, GIF (máx. 5MB)</small>
                    </div>

                    <div class="mb-3">
                        <label for="COD_BARRAS" class="form-label">Código de Barras *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['errors']['COD_BARRAS']) ? 'is-invalid' : ''; ?>" 
                               id="COD_BARRAS" name="COD_BARRAS" required 
                               value="<?php echo htmlspecialchars($_SESSION['old']['COD_BARRAS'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['COD_BARRAS'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['errors']['COD_BARRAS']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="NOME_PRODUTO" class="form-label">Nome *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['errors']['NOME_PRODUTO']) ? 'is-invalid' : ''; ?>" 
                               id="NOME_PRODUTO" name="NOME_PRODUTO" required 
                               value="<?php echo htmlspecialchars($_SESSION['old']['NOME_PRODUTO'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['NOME_PRODUTO'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['errors']['NOME_PRODUTO']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="DESCRICAO" class="form-label">Descrição</label>
                        <textarea class="form-control" id="DESCRICAO" name="DESCRICAO" rows="3"><?php echo htmlspecialchars($_SESSION['old']['DESCRICAO'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="VALOR_UNITARIO" class="form-label">Valor Unitário *</label>
                        <input type="number" step="0.01" class="form-control <?php echo isset($_SESSION['errors']['VALOR_UNITARIO']) ? 'is-invalid' : ''; ?>" 
                               id="VALOR_UNITARIO" name="VALOR_UNITARIO" required 
                               value="<?php echo htmlspecialchars($_SESSION['old']['VALOR_UNITARIO'] ?? ''); ?>">
                        <?php if (isset($_SESSION['errors']['VALOR_UNITARIO'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['errors']['VALOR_UNITARIO']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/produtos" class="btn btn-secondary">
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
