<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-receipt"></i> Pedido #<?php echo $pedido['ID_PEDIDO']; ?></h4>
                <?php
                $statusClass = [
                    'EM_ABERTO' => 'warning',
                    'PAGO' => 'success',
                    'CANCELADO' => 'danger'
                ];
                ?>
                <span class="badge bg-<?php echo $statusClass[$pedido['STATUS']]; ?> fs-6">
                    <?php echo $pedido['STATUS']; ?>
                </span>
            </div>
            <div class="card-body">
                <dl class="row mb-4">
                    <dt class="col-sm-3">Cliente:</dt>
                    <dd class="col-sm-9">
                        <a href="/clientes/view/<?php echo $pedido['ID_CLIENTE']; ?>">
                            <?php echo htmlspecialchars($pedido['NOME_CLIENTE']); ?>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Data:</dt>
                    <dd class="col-sm-9"><?php echo date('d/m/Y H:i', strtotime($pedido['DATA_PEDIDO'])); ?></dd>

                    <dt class="col-sm-3">Observações:</dt>
                    <dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($pedido['OBSERVACAO'])); ?></dd>
                </dl>

                <hr>

                <h5 class="mb-3"><i class="bi bi-list-ul"></i> Itens do Pedido</h5>

                <form method="POST" action="/pedidos/<?php echo $pedido['ID_PEDIDO']; ?>/add-item" class="row g-3 mb-3">
                    <div class="col-md-6">
                        <select class="form-select" name="ID_PRODUTO" required>
                            <option value="">Selecione um produto</option>
                            <?php foreach ($produtos as $produto): ?>
                                <option value="<?php echo $produto['ID_PRODUTO']; ?>">
                                    <?php echo htmlspecialchars($produto['NOME_PRODUTO']); ?> - 
                                    R$ <?php echo number_format($produto['VALOR_UNITARIO'], 2, ',', '.'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="QUANTIDADE" placeholder="Quantidade" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle"></i> Adicionar
                        </button>
                    </div>
                </form>

                <?php if (empty($itens)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Nenhum item adicionado a este pedido ainda.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Código</th>
                                    <th class="text-end">Quantidade</th>
                                    <th class="text-end">Valor Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($itens as $item): ?>
                                    <tr>
                                        <td>
                                            <a href="/produtos/view/<?php echo $item['ID_PRODUTO']; ?>">
                                                <?php echo htmlspecialchars($item['NOME_PRODUTO']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['COD_BARRAS']); ?></td>
                                        <td class="text-end"><?php echo $item['QUANTIDADE']; ?></td>
                                        <td class="text-end">R$ <?php echo number_format($item['VALOR_UNITARIO'], 2, ',', '.'); ?></td>
                                        <td class="text-end">R$ <?php echo number_format($item['QUANTIDADE'] * $item['VALOR_UNITARIO'], 2, ',', '.'); ?></td>
                                        <td>
                                            <form action="/pedidos/<?php echo $pedido['ID_PEDIDO']; ?>/remove-item/<?php echo $item['ID_ITEM']; ?>" 
                                                  method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Tem certeza que deseja remover este item?');">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-info fw-bold">
                                    <td colspan="4" class="text-end">TOTAL:</td>
                                    <td class="text-end">R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-2 mt-4">
                    <a href="/pedidos" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <a href="/pedidos/edit/<?php echo $pedido['ID_PEDIDO']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar Pedido
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calculator"></i> Resumo</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-6">Itens:</dt>
                    <dd class="col-sm-6 text-end"><?php echo count($itens); ?></dd>

                    <dt class="col-sm-6">Subtotal:</dt>
                    <dd class="col-sm-6 text-end">R$ <?php echo number_format($total, 2, ',', '.'); ?></dd>

                    <?php if (!empty($pedido['DESCONTO_PERCENTUAL']) && $pedido['DESCONTO_PERCENTUAL'] > 0): ?>
                        <dt class="col-sm-6 text-success">Desconto (<?php echo number_format($pedido['DESCONTO_PERCENTUAL'], 2, ',', '.'); ?>%):</dt>
                        <dd class="col-sm-6 text-end text-success">- R$ <?php echo number_format($pedido['DESCONTO_VALOR'], 2, ',', '.'); ?></dd>

                        <dt class="col-sm-6 fw-bold">Total:</dt>
                        <dd class="col-sm-6 text-end fs-5 fw-bold text-primary">
                            R$ <?php echo number_format($pedido['VALOR_TOTAL'], 2, ',', '.'); ?>
                        </dd>
                    <?php else: ?>
                        <dt class="col-sm-6 fw-bold">Total:</dt>
                        <dd class="col-sm-6 text-end fs-5 fw-bold text-primary">
                            R$ <?php echo number_format($total, 2, ',', '.'); ?>
                        </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <?php if ($pedido['STATUS'] !== 'CANCELADO'): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-percent"></i> Desconto</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($pedido['DESCONTO_PERCENTUAL']) && $pedido['DESCONTO_PERCENTUAL'] > 0): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Desconto aplicado!
                        </div>
                        <form method="POST" action="/pedidos/<?php echo $pedido['ID_PEDIDO']; ?>/remove-discount" onsubmit="return confirm('Tem certeza que deseja remover o desconto?');">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle"></i> Remover Desconto
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/pedidos/<?php echo $pedido['ID_PEDIDO']; ?>/apply-discount">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Desconto</label>
                                <select class="form-select" name="tipo_desconto" id="tipoDesconto" required>
                                    <option value="percentual">Percentual (%)</option>
                                    <option value="valor">Valor (R$)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valor do Desconto</label>
                                <input type="number" class="form-control" name="valor_desconto" step="0.01" min="0" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Aplicar Desconto
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
