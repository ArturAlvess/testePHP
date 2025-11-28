<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-lightning-charge-fill" style="font-size: 4rem; color: var(--primary-color);"></i>
                        <h2 class="mt-3">AlphaOrders</h2>
                        <p class="text-muted">Faça login para acessar</p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/login">
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope-fill"></i> Email
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="email" 
                                   name="email" 
                                   placeholder="seu@email.com"
                                   required 
                                   autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="senha" class="form-label">
                                <i class="bi bi-lock-fill"></i> Senha
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg" 
                                   id="senha" 
                                   name="senha" 
                                   placeholder="••••••••"
                                   required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-box-arrow-in-right"></i> 
                                <span>Entrar</span>
                            </button>
                        </div>

                        <hr>

                        <div class="text-center mt-3">
                            <p class="text-muted mb-2">Ainda não tem uma conta?</p>
                            <a href="/registro" class="btn btn-outline-primary">
                                <i class="bi bi-person-plus"></i> Criar Conta
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> Acesso seguro e protegido
                        </small>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <p class="text-muted">
                    <small>© <?php echo date('Y'); ?> AlphaCode Sistema - Teste Técnico</small>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    .card {
        border-radius: 20px;
        border: none;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(128, 64, 237, 0.25);
    }
</style>
