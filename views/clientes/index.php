<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="bi bi-people"></i> Clientes</h1>
            <a href="/clientes/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Cliente
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-funnel"></i> Filtros
            </div>
            <div class="card-body">
                <form method="GET" action="/clientes" class="row g-3">
                    <div class="col-md-2">
                        <label for="id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($filters['ID_CLIENTE']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($filters['NOME_CLIENTE']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo htmlspecialchars($filters['CPF']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($filters['EMAIL']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($filters['TELEFONE']); ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="/clientes" class="btn btn-secondary">
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
                        <form method="GET" action="/clientes" class="d-inline-flex align-items-center">
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
                <form id="bulkDeleteForm" method="POST" action="/clientes/delete-multiple">
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;" onclick="return confirm('Tem certeza que deseja excluir os clientes selecionados?');">
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
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'ID_CLIENTE', 'order_dir' => $orderBy === 'ID_CLIENTE' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            ID <?php if($orderBy === 'ID_CLIENTE') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'NOME_CLIENTE', 'order_dir' => $orderBy === 'NOME_CLIENTE' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            Nome <?php if($orderBy === 'NOME_CLIENTE') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'CPF', 'order_dir' => $orderBy === 'CPF' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            CPF <?php if($orderBy === 'CPF') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($clientes)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum cliente encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ids[]" value="<?php echo $cliente['ID_CLIENTE']; ?>" class="form-check-input item-checkbox">
                                            </td>
                                            <td><?php echo htmlspecialchars($cliente['ID_CLIENTE']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['NOME_CLIENTE']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['CPF']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['EMAIL']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['TELEFONE']); ?></td>
                                        <td class="table-actions">
                                            <a href="/clientes/view/<?php echo $cliente['ID_CLIENTE']; ?>" class="btn btn-sm btn-info" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/clientes/edit/<?php echo $cliente['ID_CLIENTE']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-single-btn" 
                                                    data-id="<?php echo $cliente['ID_CLIENTE']; ?>" 
                                                    title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
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
                    <p class="text-center text-muted">Mostrando <?php echo count($clientes); ?> de <?php echo $total; ?> clientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Controle de seleção em massa
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

// Exclusão individual
document.querySelectorAll('.delete-single-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const clienteId = this.getAttribute('data-id');
        
        if (confirm('Tem certeza que deseja excluir este cliente?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/clientes/delete/${clienteId}`;
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>
