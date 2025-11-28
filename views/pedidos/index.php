<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="bi bi-receipt"></i> Pedidos</h1>
            <a href="/pedidos/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Pedido
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-funnel"></i> Filtros
            </div>
            <div class="card-body">
                <form method="GET" action="/pedidos" class="row g-3">
                    <div class="col-md-2">
                        <label for="id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($filters['ID_PEDIDO']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cliente" class="form-label">Cliente</label>
                        <select class="form-select" id="cliente" name="cliente">
                            <option value="">Todos</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['ID_CLIENTE']; ?>" <?php echo $filters['ID_CLIENTE'] == $cliente['ID_CLIENTE'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cliente['NOME_CLIENTE']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="EM_ABERTO" <?php echo $filters['STATUS'] === 'EM_ABERTO' ? 'selected' : ''; ?>>EM ABERTO</option>
                            <option value="PAGO" <?php echo $filters['STATUS'] === 'PAGO' ? 'selected' : ''; ?>>PAGO</option>
                            <option value="CANCELADO" <?php echo $filters['STATUS'] === 'CANCELADO' ? 'selected' : ''; ?>>CANCELADO</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" name="data" value="<?php echo htmlspecialchars($filters['DATA_PEDIDO']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <a href="/pedidos" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <form method="GET" action="/pedidos" class="d-inline-flex align-items-center">
                            <!-- Manter filtros e ordenação existentes -->
                            <?php foreach ($_GET as $key => $value): ?>
                                <?php if ($key !== 'per_page' && $key !== 'page'): ?>
                                    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <label for="perPage" class="me-2 mb-0">Itens por página:</label>
                            <select name="per_page" id="perPage" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="5" <?php echo ($perPage ?? 20) == 5 ? 'selected' : ''; ?>>5</option>
                                <option value="10" <?php echo ($perPage ?? 20) == 10 ? 'selected' : ''; ?>>10</option>
                                <option value="20" <?php echo ($perPage ?? 20) == 20 ? 'selected' : ''; ?>>20</option>
                                <option value="50" <?php echo ($perPage ?? 20) == 50 ? 'selected' : ''; ?>>50</option>
                                <option value="100" <?php echo ($perPage ?? 20) == 100 ? 'selected' : ''; ?>>100</option>
                            </select>
                        </form>
                    </div>
                </div>
                <form id="bulkDeleteForm" method="POST" action="/pedidos/delete-multiple">
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;" onclick="return confirm('Tem certeza que deseja excluir os pedidos selecionados?');">
                            <i class="bi bi-trash"></i> Excluir Selecionados
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'p.ID_PEDIDO', 'order_dir' => $orderBy === 'p.ID_PEDIDO' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            ID <?php if($orderBy === 'p.ID_PEDIDO') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'c.NOME_CLIENTE', 'order_dir' => $orderBy === 'c.NOME_CLIENTE' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            Cliente <?php if($orderBy === 'c.NOME_CLIENTE') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'p.DATA_PEDIDO', 'order_dir' => $orderBy === 'p.DATA_PEDIDO' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            Data <?php if($orderBy === 'p.DATA_PEDIDO') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'p.STATUS', 'order_dir' => $orderBy === 'p.STATUS' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            Status <?php if($orderBy === 'p.STATUS') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>Ações</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pedidos)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum pedido encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pedidos as $pedido): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ids[]" value="<?php echo $pedido['ID_PEDIDO']; ?>" class="form-check-input item-checkbox">
                                            </td>
                                            <td><?php echo htmlspecialchars($pedido['ID_PEDIDO']); ?></td>
                                        <td>
                                            <a href="/clientes/view/<?php echo $pedido['ID_CLIENTE']; ?>">
                                                <?php echo htmlspecialchars($pedido['NOME_CLIENTE']); ?>
                                            </a>
                                        </td>
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
                                        <td class="table-actions">
                                            <a href="/pedidos/view/<?php echo $pedido['ID_PEDIDO']; ?>" class="btn btn-sm btn-info" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <?php if ($pedido['STATUS'] !== 'PAGO' && $pedido['STATUS'] !== 'CANCELADO'): ?>
                                                <form action="/pedidos/mark-as-paid/<?php echo $pedido['ID_PEDIDO']; ?>" method="POST" style="display: inline;" onsubmit="return confirm('Marcar este pedido como PAGO?');">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Marcar como Pago">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <a href="/pedidos/edit/<?php echo $pedido['ID_PEDIDO']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="/pedidos/delete/<?php echo $pedido['ID_PEDIDO']; ?>" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este pedido?');">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td style="width: 50px; text-align: center;">
                                            <?php if ($pedido['STATUS'] !== 'CANCELADO'): ?>
                                                <form action="/pedidos/cancel/<?php echo $pedido['ID_PEDIDO']; ?>" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja CANCELAR este pedido?');">
                                                    <button type="submit" class="btn btn-link text-danger p-0" title="Cancelar Pedido" style="font-size: 1.5rem; text-decoration: none;">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>

            <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage - 1])); ?>">Anterior</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
                                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php elseif (abs($i - $currentPage) == 3): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage + 1])); ?>">Próximo</a>
                            </li>
                        </ul>
                    </nav>
                    <p class="text-center text-muted">Mostrando <?php echo count($pedidos); ?> de <?php echo $total; ?> pedidos</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const selectAllCheckbox = document.getElementById('selectAll');
const itemCheckboxes = document.querySelectorAll('.item-checkbox');
const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkDeleteBtn();
    });
}

itemCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', toggleBulkDeleteBtn);
});

function toggleBulkDeleteBtn() {
    const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
    if (checkedCount > 0) {
        bulkDeleteBtn.style.display = 'inline-block';
        bulkDeleteBtn.textContent = `Excluir ${checkedCount} Selecionado(s)`;
    } else {
        bulkDeleteBtn.style.display = 'none';
    }
}
</script>
