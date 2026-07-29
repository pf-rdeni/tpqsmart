<!DOCTYPE html>
<html lang="id">
<?= $this->include('/backend/template/meta'); ?>
<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <!-- Minimal Navbar Ujian (Tanpa Sidebar Admin) -->
        <?= $this->include('/backend/template/navbar_ujian'); ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper" style="margin-left: 0 !important; background-color: #f4f6f9;">
            <?= $this->renderSection('content'); ?>
        </div>

        <!-- Minimal Footer Ujian -->
        <footer class="main-footer text-center text-muted small py-2" style="margin-left: 0 !important;">
            <strong>Copyright &copy; <?= date('Y') ?> <a href="#">TPQ Smart — Ujian MDTA</a>.</strong> All rights reserved.
        </footer>
    </div>

    <?= $this->include('/backend/template/js'); ?>
    <?= $this->include('/backend/template/scripts'); ?>
    
    <script>
        // Dark Mode Handler Khusus Template Ujian (AdminLTE Compatible)
        document.addEventListener('DOMContentLoaded', function() {
            const btnDark = document.getElementById('btnToggleDarkUjian');
            const savedTheme = localStorage.getItem('theme') || localStorage.getItem('dark-mode');
            
            if (savedTheme === 'dark' || savedTheme === 'enabled') {
                document.body.classList.add('dark-mode');
                if (btnDark) btnDark.innerHTML = '<i class="fas fa-sun text-warning"></i>';
            }

            if (btnDark) {
                btnDark.addEventListener('click', function() {
                    document.body.classList.toggle('dark-mode');
                    const isDark = document.body.classList.contains('dark-mode');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    localStorage.setItem('dark-mode', isDark ? 'enabled' : 'disabled');
                    btnDark.innerHTML = isDark ? '<i class="fas fa-sun text-warning"></i>' : '<i class="fas fa-moon"></i>';
                });
            }
        });
    </script>
    <?= $this->renderSection('scripts'); ?>
</body>
</html>
