    </main>

    <footer class="text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">
                <i class="bi bi-code-slash"></i> <?php echo date('Y'); ?> Todos direitos reservados - Artur Alves
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
<?php
// Limpar dados antigos da sessão
if (isset($_SESSION['errors'])) unset($_SESSION['errors']);
if (isset($_SESSION['old'])) unset($_SESSION['old']);
?>
