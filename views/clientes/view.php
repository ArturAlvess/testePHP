<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person"></i> Detalhes do Cliente</h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($cliente['ID_CLIENTE']); ?></dd>

                    <dt class="col-sm-4">Nome:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($cliente['NOME_CLIENTE']); ?></dd>

                    <dt class="col-sm-4">CPF:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($cliente['CPF']); ?></dd>

                    <dt class="col-sm-4">Email:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($cliente['EMAIL']); ?></dd>

                    <dt class="col-sm-4">Telefone:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($cliente['TELEFONE']); ?></dd>

                    <dt class="col-sm-4">Endereço:</dt>
                    <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($cliente['ENDERECO'])); ?></dd>
                </dl>

                <div class="d-flex gap-2">
                    <a href="/clientes" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <a href="/clientes/edit/<?php echo $cliente['ID_CLIENTE']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-receipt"></i> Pedidos do Cliente</h4>
            </div>
            <div class="card-body">
                <?php if (empty($pedidos)): ?>
                    <p class="text-muted">Este cliente ainda não possui pedidos.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td><?php echo $pedido['ID_PEDIDO']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['DATA_PEDIDO'])); ?></td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'EM_ABERTO' => 'warning',
                                                'PAGO' => 'success',
                                                'CANCELADO' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge bg-<?php echo $statusClass[$pedido['STATUS']]; ?>">
                                                <?php echo $pedido['STATUS']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/pedidos/view/<?php echo $pedido['ID_PEDIDO']; ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
