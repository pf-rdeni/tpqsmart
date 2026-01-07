<!DOCTYPE html>
<html lang="id">
<!-- Include Meta -->
<?= $this->include('frontend/template/meta'); ?>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        
        <!-- Include Navbar -->
        <?= $this->include('frontend/template/navbar'); ?>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
             <!-- Optional: Add breadcrumbs or page title here if needed -->
            
            <!-- Main content -->
            <div class="content">
                <div class="container">
                    <?= $this->renderSection('content'); ?>
                </div>
            </div>
        </div>

        <!-- Include Footer -->
        <?= $this->include('frontend/template/footer'); ?>
    </div>

    <!-- Include Scripts -->
    <?= $this->include('frontend/template/scripts'); ?>
    
    <!-- Custom Scripts Section -->
    <?= $this->renderSection('scripts'); ?>
</body>
</html>