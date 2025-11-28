<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="bi bi-box-seam"></i> Produtos</h1>
            <a href="/produtos/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Produto
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-funnel"></i> Filtros
            </div>
            <div class="card-body">
                <form method="GET" action="/produtos" class="row g-3">
                    <div class="col-md-2">
                        <label for="id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($filters['ID_PRODUTO']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cod_barras" class="form-label">Código de Barras</label>
                        <input type="text" class="form-control" id="cod_barras" name="cod_barras" value="<?php echo htmlspecialchars($filters['COD_BARRAS']); ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($filters['NOME_PRODUTO']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="valor" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="valor" name="valor" value="<?php echo htmlspecialchars($filters['VALOR_UNITARIO']); ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="/produtos" class="btn btn-secondary">
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
                        <form method="GET" action="/produtos" class="d-inline-flex align-items-center">
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
                <form id="bulkDeleteForm" method="POST" action="/produtos/delete-multiple">
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;" onclick="return confirm('Tem certeza que deseja excluir os produtos selecionados?');">
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
                                    <th>Imagem</th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'ID_PRODUTO', 'order_dir' => $orderBy === 'ID_PRODUTO' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            ID <?php if($orderBy === 'ID_PRODUTO') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'COD_BARRAS', 'order_dir' => $orderBy === 'COD_BARRAS' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            Código de Barras <?php if($orderBy === 'COD_BARRAS') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'NOME_PRODUTO', 'order_dir' => $orderBy === 'NOME_PRODUTO' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            Nome <?php if($orderBy === 'NOME_PRODUTO') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['order_by' => 'VALOR_UNITARIO', 'order_dir' => $orderBy === 'VALOR_UNITARIO' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'])); ?>" class="sort-link">
                                            Valor <?php if($orderBy === 'VALOR_UNITARIO') echo $orderDirection === 'ASC' ? '▲' : '▼'; ?>
                                        </a>
                                    </th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($produtos)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum produto encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ids[]" value="<?php echo $produto['ID_PRODUTO']; ?>" class="form-check-input item-checkbox">
                                            </td>
                                        <td>
                                            <?php if (!empty($produto['IMAGEM_URL'])): ?>
                                                <img src="<?php echo htmlspecialchars($produto['IMAGEM_URL']); ?>" alt="<?php echo htmlspecialchars($produto['NOME_PRODUTO']); ?>" class="img-thumbnail" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="text-muted" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 4px;">
                                                    <i class="bi bi-image" style="font-size: 24px;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($produto['ID_PRODUTO']); ?></td>
                                        <td><?php echo htmlspecialchars($produto['COD_BARRAS']); ?></td>
                                        <td><?php echo htmlspecialchars($produto['NOME_PRODUTO']); ?></td>
                                        <td>R$ <?php echo number_format($produto['VALOR_UNITARIO'], 2, ',', '.'); ?></td>
                                        <td class="table-actions">
                                            <a href="/produtos/view/<?php echo $produto['ID_PRODUTO']; ?>" class="btn btn-sm btn-info" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/produtos/edit/<?php echo $produto['ID_PRODUTO']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-single-btn" 
                                                    data-id="<?php echo $produto['ID_PRODUTO']; ?>" 
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
                    <p class="text-center text-muted">Mostrando <?php echo count($produtos); ?> de <?php echo $total; ?> produtos</p>
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

// Exclusão individual
document.querySelectorAll('.delete-single-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const produtoId = this.getAttribute('data-id');
        
        if (confirm('Tem certeza que deseja excluir este produto?')) {
            // Criar formulário dinamicamente e submeter
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/produtos/delete/${produtoId}`;
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>
