<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-box-seam"></i> Detalhes do Produto</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($produto['IMAGEM_URL'])): ?>
                    <div class="text-center mb-4">
                        <img src="<?php echo htmlspecialchars($produto['IMAGEM_URL']); ?>" alt="<?php echo htmlspecialchars($produto['NOME_PRODUTO']); ?>" class="img-fluid rounded" style="max-height: 400px;">
                    </div>
                <?php endif; ?>

                <dl class="row">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($produto['ID_PRODUTO']); ?></dd>

                    <dt class="col-sm-4">Código de Barras:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($produto['COD_BARRAS']); ?></dd>

                    <dt class="col-sm-4">Nome:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($produto['NOME_PRODUTO']); ?></dd>

                    <dt class="col-sm-4">Descrição:</dt>
                    <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($produto['DESCRICAO'])); ?></dd>

                    <dt class="col-sm-4">Valor Unitário:</dt>
                    <dd class="col-sm-8">R$ <?php echo number_format($produto['VALOR_UNITARIO'], 2, ',', '.'); ?></dd>
                </dl>

                <div class="d-flex gap-2">
                    <a href="/produtos" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <a href="/produtos/edit/<?php echo $produto['ID_PRODUTO']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
