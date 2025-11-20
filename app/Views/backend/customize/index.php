<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-palette mr-2"></i>
                            Customize AdminLTE
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-primary" id="resetSettings">
                                <i class="fas fa-undo"></i> Reset to Default
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="customizeForm">
                            <!-- Layout Options -->
                            <div class="card card-primary card-outline collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-th-large mr-2"></i>
                                        Layout Options
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="layoutFixed" name="layoutFixed" data-class="layout-fixed">
                                            <label class="custom-control-label" for="layoutFixed">Fixed Sidebar</label>
                                        </div>
                                        <small class="form-text text-muted">Sidebar akan tetap terlihat saat scroll</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="layoutNavbarFixed" name="layoutNavbarFixed" data-class="layout-navbar-fixed">
                                            <label class="custom-control-label" for="layoutNavbarFixed">Fixed Navbar</label>
                                        </div>
                                        <small class="form-text text-muted">Navbar akan tetap di atas saat scroll</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="layoutFooterFixed" name="layoutFooterFixed" data-class="layout-footer-fixed">
                                            <label class="custom-control-label" for="layoutFooterFixed">Fixed Footer</label>
                                        </div>
                                        <small class="form-text text-muted">Footer akan tetap di bawah saat scroll</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="layoutBoxed" name="layoutBoxed" data-class="layout-boxed">
                                            <label class="custom-control-label" for="layoutBoxed">Boxed Layout</label>
                                        </div>
                                        <small class="form-text text-muted">Layout akan dibatasi maksimal 1250px</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="layoutTopNav" name="layoutTopNav" data-class="layout-top-nav">
                                            <label class="custom-control-label" for="layoutTopNav">Top Navigation</label>
                                        </div>
                                        <small class="form-text text-muted">Sidebar akan disembunyikan dan menu dipindah ke navbar</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="sidebarCollapse" name="sidebarCollapse" data-class="sidebar-collapse">
                                            <label class="custom-control-label" for="sidebarCollapse">Collapsed Sidebar</label>
                                        </div>
                                        <small class="form-text text-muted">Sidebar akan collapsed saat page load</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar Options -->
                            <div class="card card-primary card-outline collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-bars mr-2"></i>
                                        Sidebar Options
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Sidebar Color</label>
                                        <select class="form-control" id="sidebarColor" name="sidebarColor">
                                            <option value="sidebar-dark-primary">Dark Primary</option>
                                            <option value="sidebar-dark-secondary">Dark Secondary</option>
                                            <option value="sidebar-dark-info">Dark Info</option>
                                            <option value="sidebar-dark-success">Dark Success</option>
                                            <option value="sidebar-dark-warning">Dark Warning</option>
                                            <option value="sidebar-dark-danger">Dark Danger</option>
                                            <option value="sidebar-dark-indigo">Dark Indigo</option>
                                            <option value="sidebar-dark-navy">Dark Navy</option>
                                            <option value="sidebar-dark-purple">Dark Purple</option>
                                            <option value="sidebar-dark-fuchsia">Dark Fuchsia</option>
                                            <option value="sidebar-dark-pink">Dark Pink</option>
                                            <option value="sidebar-dark-maroon">Dark Maroon</option>
                                            <option value="sidebar-dark-orange">Dark Orange</option>
                                            <option value="sidebar-dark-lime">Dark Lime</option>
                                            <option value="sidebar-dark-teal">Dark Teal</option>
                                            <option value="sidebar-dark-olive">Dark Olive</option>
                                            <option value="sidebar-light-primary">Light Primary</option>
                                            <option value="sidebar-light-secondary">Light Secondary</option>
                                            <option value="sidebar-light-info">Light Info</option>
                                            <option value="sidebar-light-success">Light Success</option>
                                            <option value="sidebar-light-warning">Light Warning</option>
                                            <option value="sidebar-light-danger">Light Danger</option>
                                            <option value="sidebar-light-indigo">Light Indigo</option>
                                            <option value="sidebar-light-navy">Light Navy</option>
                                            <option value="sidebar-light-purple">Light Purple</option>
                                            <option value="sidebar-light-fuchsia">Light Fuchsia</option>
                                            <option value="sidebar-light-pink">Light Pink</option>
                                            <option value="sidebar-light-maroon">Light Maroon</option>
                                            <option value="sidebar-light-orange">Light Orange</option>
                                            <option value="sidebar-light-lime">Light Lime</option>
                                            <option value="sidebar-light-teal">Light Teal</option>
                                            <option value="sidebar-light-olive">Light Olive</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="sidebarMini" name="sidebarMini" data-class="sidebar-mini">
                                            <label class="custom-control-label" for="sidebarMini">Sidebar Mini</label>
                                        </div>
                                        <small class="form-text text-muted">Sidebar akan menampilkan hanya icon saat hover</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="sidebarMiniMd" name="sidebarMiniMd" data-class="sidebar-mini-md">
                                            <label class="custom-control-label" for="sidebarMiniMd">Sidebar Mini MD</label>
                                        </div>
                                        <small class="form-text text-muted">Sidebar mini untuk medium screen</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="sidebarMiniXs" name="sidebarMiniXs" data-class="sidebar-mini-xs">
                                            <label class="custom-control-label" for="sidebarMiniXs">Sidebar Mini XS</label>
                                        </div>
                                        <small class="form-text text-muted">Sidebar mini untuk extra small screen</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Navbar Options -->
                            <div class="card card-primary card-outline collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-window-maximize mr-2"></i>
                                        Navbar Options
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Navbar Variant</label>
                                        <select class="form-control" id="navbarVariant" name="navbarVariant">
                                            <option value="navbar-white navbar-light">White / Light</option>
                                            <option value="navbar-primary">Primary</option>
                                            <option value="navbar-secondary">Secondary</option>
                                            <option value="navbar-info">Info</option>
                                            <option value="navbar-success">Success</option>
                                            <option value="navbar-warning">Warning</option>
                                            <option value="navbar-danger">Danger</option>
                                            <option value="navbar-indigo">Indigo</option>
                                            <option value="navbar-navy">Navy</option>
                                            <option value="navbar-purple">Purple</option>
                                            <option value="navbar-fuchsia">Fuchsia</option>
                                            <option value="navbar-pink">Pink</option>
                                            <option value="navbar-maroon">Maroon</option>
                                            <option value="navbar-orange">Orange</option>
                                            <option value="navbar-lime">Lime</option>
                                            <option value="navbar-teal">Teal</option>
                                            <option value="navbar-olive">Olive</option>
                                            <option value="navbar-dark navbar-dark">Dark</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Options -->
                            <div class="card card-primary card-outline collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-cog mr-2"></i>
                                        Additional Options
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="textSm" name="textSm" data-class="text-sm">
                                            <label class="custom-control-label" for="textSm">Small Text</label>
                                        </div>
                                        <small class="form-text text-muted">Menggunakan font size lebih kecil</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="flatStyle" name="flatStyle" data-class="flat-style">
                                            <label class="custom-control-label" for="flatStyle">Flat Style</label>
                                        </div>
                                        <small class="form-text text-muted">Menghilangkan shadow dan border radius</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="legacyStyle" name="legacyStyle" data-class="legacy-style">
                                            <label class="custom-control-label" for="legacyStyle">Legacy Style</label>
                                        </div>
                                        <small class="form-text text-muted">Menggunakan style AdminLTE versi lama</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" id="applySettings">
                            <i class="fas fa-check"></i> Apply Settings
                        </button>
                        <button type="button" class="btn btn-default" id="resetSettingsFooter">
                            <i class="fas fa-undo"></i> Reset to Default
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Load saved settings from localStorage
    function loadSettings() {
        const settings = JSON.parse(localStorage.getItem('adminlte_settings') || '{}');
        
        // Load layout options
        if (settings.layoutFixed) $('#layoutFixed').prop('checked', true);
        if (settings.layoutNavbarFixed) $('#layoutNavbarFixed').prop('checked', true);
        if (settings.layoutFooterFixed) $('#layoutFooterFixed').prop('checked', true);
        if (settings.layoutBoxed) $('#layoutBoxed').prop('checked', true);
        if (settings.layoutTopNav) $('#layoutTopNav').prop('checked', true);
        if (settings.sidebarCollapse) $('#sidebarCollapse').prop('checked', true);
        
        // Load sidebar options
        if (settings.sidebarColor) $('#sidebarColor').val(settings.sidebarColor);
        if (settings.sidebarMini) $('#sidebarMini').prop('checked', true);
        if (settings.sidebarMiniMd) $('#sidebarMiniMd').prop('checked', true);
        if (settings.sidebarMiniXs) $('#sidebarMiniXs').prop('checked', true);
        
        // Load navbar options
        if (settings.navbarVariant) $('#navbarVariant').val(settings.navbarVariant);
        
        // Load additional options
        if (settings.textSm) $('#textSm').prop('checked', true);
        if (settings.flatStyle) $('#flatStyle').prop('checked', true);
        if (settings.legacyStyle) $('#legacyStyle').prop('checked', true);
    }
    
    // Apply settings to body and elements
    function applySettings() {
        const settings = JSON.parse(localStorage.getItem('adminlte_settings') || '{}');
        const body = $('body');
        const sidebar = $('.main-sidebar');
        const navbar = $('.main-header.navbar');
        const controlSidebar = $('.control-sidebar');
        
        // Remove all layout classes first
        body.removeClass('layout-fixed layout-navbar-fixed layout-footer-fixed layout-boxed layout-top-nav sidebar-collapse sidebar-mini sidebar-mini-md sidebar-mini-xs');
        
        // Apply layout classes
        if (settings.layoutFixed) body.addClass('layout-fixed');
        if (settings.layoutNavbarFixed) body.addClass('layout-navbar-fixed');
        if (settings.layoutFooterFixed) body.addClass('layout-footer-fixed');
        if (settings.layoutBoxed) body.addClass('layout-boxed');
        if (settings.layoutTopNav) body.addClass('layout-top-nav');
        if (settings.sidebarCollapse) body.addClass('sidebar-collapse');
        if (settings.sidebarMini) body.addClass('sidebar-mini');
        if (settings.sidebarMiniMd) body.addClass('sidebar-mini-md');
        if (settings.sidebarMiniXs) body.addClass('sidebar-mini-xs');
        
        // Apply sidebar color
        if (settings.sidebarColor && sidebar.length) {
            sidebar.removeClass().addClass('main-sidebar elevation-4 ' + settings.sidebarColor);
        }
        
        // Apply navbar variant
        if (settings.navbarVariant && navbar.length) {
            navbar.removeClass('navbar-white navbar-light navbar-primary navbar-secondary navbar-info navbar-success navbar-warning navbar-danger navbar-indigo navbar-navy navbar-purple navbar-fuchsia navbar-pink navbar-maroon navbar-orange navbar-lime navbar-teal navbar-olive navbar-dark').addClass('navbar navbar-expand ' + settings.navbarVariant);
        }
        
        // Apply additional options
        if (settings.textSm) body.addClass('text-sm');
        if (settings.flatStyle) body.addClass('flat-style');
        if (settings.legacyStyle) body.addClass('legacy-style');
    }
    
    // Save settings to localStorage
    function saveSettings() {
        const settings = {
            layoutFixed: $('#layoutFixed').is(':checked'),
            layoutNavbarFixed: $('#layoutNavbarFixed').is(':checked'),
            layoutFooterFixed: $('#layoutFooterFixed').is(':checked'),
            layoutBoxed: $('#layoutBoxed').is(':checked'),
            layoutTopNav: $('#layoutTopNav').is(':checked'),
            sidebarCollapse: $('#sidebarCollapse').is(':checked'),
            sidebarColor: $('#sidebarColor').val(),
            sidebarMini: $('#sidebarMini').is(':checked'),
            sidebarMiniMd: $('#sidebarMiniMd').is(':checked'),
            sidebarMiniXs: $('#sidebarMiniXs').is(':checked'),
            navbarVariant: $('#navbarVariant').val(),
            textSm: $('#textSm').is(':checked'),
            flatStyle: $('#flatStyle').is(':checked'),
            legacyStyle: $('#legacyStyle').is(':checked')
        };
        
        localStorage.setItem('adminlte_settings', JSON.stringify(settings));
        applySettings();
        
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Pengaturan telah diterapkan',
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    // Reset to default
    function resetSettings() {
        Swal.fire({
            title: 'Reset Pengaturan?',
            text: 'Semua pengaturan akan dikembalikan ke default',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.removeItem('adminlte_settings');
                location.reload();
            }
        });
    }
    
    // Initialize
    loadSettings();
    applySettings();
    
    // Event handlers
    $('#applySettings').on('click', saveSettings);
    $('#resetSettings, #resetSettingsFooter').on('click', resetSettings);
    
    // Auto-apply on change (optional - bisa diaktifkan untuk real-time preview)
    $('#customizeForm input, #customizeForm select').on('change', function() {
        saveSettings();
    });
});
</script>
<?= $this->endSection() ?>

