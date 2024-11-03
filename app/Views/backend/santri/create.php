<?= $this->extend('backend/template/template'); ?>
<!-- dropzonejs -->
<link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/dropzone/min/dropzone.min.css">

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Dropzone.js <small><em>jQuery File Upload</em> like look</small></h3>
            </div>
            <div class="card-body">
                <div id="actions" class="row">
                    <div class="col-lg-6">
                        <div class="btn-group w-100">
                            <span class="btn btn-success col fileinput-button">
                                <i class="fas fa-plus"></i>
                                <span>Add files</span>
                            </span>
                            <button type="button" class="btn btn-primary col start">
                                <i class="fas fa-upload"></i>
                                <span>Start upload</span>
                            </button>
                            <button type="button" class="btn btn-warning col cancel">
                                <i class="fas fa-times-circle"></i>
                                <span>Cancel upload</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center">
                        <div class="fileupload-process w-100">
                            <div id="total-progress" class="progress progress-striped active"
                                role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;"
                                    data-dz-uploadprogress></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table table-striped files" id="previews">
                    <div id="templateUpload" class="row mt-2">
                        <div class="col-auto">
                            <span class="preview"><img src="data:," alt=""
                                    data-dz-thumbnail /></span>
                        </div>
                        <div class="col d-flex align-items-center">
                            <p class="mb-0">
                                <span class="lead" data-dz-name></span>
                                (<span data-dz-size></span>)
                            </p>
                            <strong class="error text-danger" data-dz-errormessage></strong>
                        </div>
                        <div class="col-4 d-flex align-items-center">
                            <div class="progress progress-striped active w-100" role="progressbar"
                                aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;"
                                    data-dz-uploadprogress></div>
                            </div>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <div class="btn-group">
                                <button class="btn btn-primary start">
                                    <i class="fas fa-upload"></i>
                                    <span>Start</span>
                                </button>
                                <button data-dz-remove class="btn btn-warning cancel">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Cancel</span>
                                </button>
                                <button data-dz-remove class="btn btn-danger delete">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Visit <a href="https://www.dropzonejs.com">dropzone.js documentation</a> for more
                examples and information about the plugin.
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>

<!-- dropzonejs -->
<script src="<?php echo base_url('/plugins') ?>/dropzone/min/dropzone.min.js"></script>
<script>
    // DropzoneJS Demo Code Start
    Dropzone.autoDiscover = false;
    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#templateUpload")
    previewNode.id = ""
    var previewTemplate = previewNode.parentNode.innerHTML
    previewNode.parentNode.removeChild(previewNode)
    // Inisialisasi Dropzone dengan selector yang benar
    var myDropzone = new Dropzone("#previews", { // Ubah selector ke #previews
        url: "<?= base_url('santri/upload') ?>", // Tambahkan URL endpoint yang sesuai
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 1,
        previewTemplate: previewTemplate,
        autoQueue: false,
        previewsContainer: "#previews",
        clickable: ".fileinput-button",
        acceptedFiles: "image/*, application/pdf, .png", // Menerima gambar, PDF dan PNG
        maxFilesize: 2, // Tambahkan batas ukuran file (MB)
        maxFiles: 1, // Tambahkan pembatasan jumlah file
        dictDefaultMessage: "Drag file atau klik untuk upload",
        dictFallbackMessage: "Browser Anda tidak mendukung drag'n'drop file uploads.",
        dictFileTooBig: "File terlalu besar ({{filesize}}MiB). Maksimal ukuran file: {{maxFilesize}}MiB.",
        dictInvalidFileType: "Tipe file tidak diizinkan.",
        dictMaxFilesExceeded: "Anda hanya dapat mengupload 1 file." // Tambahkan pesan error
    });

    myDropzone.on("addedfile", function (file) {
            // Hookup the start button
        file.previewElement.querySelector(".start").onclick = function () { myDropzone.enqueueFile(file) }
    })

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function (progress) {
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
    })

    myDropzone.on("sending", function (file) {
        // Show the total progress bar when upload starts
        document.querySelector("#total-progress").style.opacity = "1"
        // And disable the start button
        file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
    })

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function (progress) {
        document.querySelector("#total-progress").style.opacity = "0"
    })

    // Setup the buttons for all transfers
    // The "add files" button doesn't need to be setup because the config
    // `clickable` has already been specified.
    document.querySelector("#actions .start").addEventListener("click", function() {
        myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
    });
    document.querySelector("#actions .cancel").onclick = function () {
        myDropzone.removeAllFiles(true)
    }

    // Tambahkan event handler untuk success dan error
    myDropzone.on("success", function(file, response) {
        //console.log("File berhasil diupload:", file.name);
        console.log("Response dari server:", response);
        // Dapatkan nama file dari response
        const uploadedFilename = response.filename;
        alert("File berhasil diupload dengan nama: " + uploadedFilename);
    });

    myDropzone.on("error", function(file, errorMessage) {
        console.log("Error mengupload file:", file.name);
        console.log("Pesan error:", errorMessage);
        // Tambahkan notifikasi error jika diperlukan
        alert("Gagal mengupload file " + file.name + ": " + errorMessage);
    });

    // Tambahkan event handler untuk mencegah file tambahan
    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeAllFiles();
        this.addFile(file);
    });

    // DropzoneJS Demo Code End
    </script>
<?= $this->endSection(); ?>
