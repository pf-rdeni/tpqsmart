<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Print QR Label</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="label_size">Ukuran Label</label>
                                <select class="form-control" id="label_size" name="label_size">
                                    <option value="small">Kecil (50x30mm)</option>
                                    <option value="medium">Sedang (100x50mm)</option>
                                    <option value="large">Besar (150x75mm)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantity">Jumlah Label</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="printLabels()">Print Label</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printLabels() {
        // Implementasi fungsi print akan ditambahkan di sini
        window.print();
    }
</script>
<?= $this->endSection() ?>