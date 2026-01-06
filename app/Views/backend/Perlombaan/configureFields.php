<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog"></i> Konfigurasi Field Sertifikat
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Lomba:</strong> <?= esc($template['NamaLomba']) ?></p>
                        <p><strong>Cabang:</strong> <?= esc($template['NamaCabang']) ?></p>
                        <p><strong>Template:</strong> <?= esc($template['NamaTemplate']) ?> 
                           (<?= $template['Width'] ?>x<?= $template['Height'] ?>px)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Panel: Field List & Configuration -->
            <div class="col-md-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Field Configuration</h3>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <div class="form-group">
                            <label>Tambah Field</label>
                            <select class="form-control" id="selectField">
                                <option value="">-- Pilih Field --</option>
                                <?php foreach ($available_fields as $af): ?>
                                    <option value="<?= $af['name'] ?>" 
                                            data-label="<?= $af['label'] ?>" 
                                            data-sample="<?= $af['sample'] ?>">
                                        <?= $af['label'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-sm btn-success mt-2" id="btnAddField">
                                <i class="fas fa-plus"></i> Tambah Field
                            </button>
                        </div>

                        <hr>

                        <div id="fieldsList">
                            <!-- Fields will be added here dynamically -->
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-block mb-2" id="btnSaveConfig">
                            <i class="fas fa-save"></i> Simpan Konfigurasi
                        </button>
                        <a href="<?= base_url('backend/perlombaan/preview-sertifikat/' . $template['id']) ?>" target="_blank" class="btn btn-info btn-block mb-2">
                             <i class="fas fa-eye"></i> Preview PDF (Dummy Data)
                        </a>
                        <a href="<?= base_url('backend/perlombaan/template-sertifikat/' . $template['cabang_id']) ?>" 
                           class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Canvas Preview -->
            <div class="col-md-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> Preview Template</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="btnTogglePreview">
                                <i class="fas fa-sync"></i> Refresh Preview
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="background: #f4f4f4; overflow: auto;">
                        <div style="position: relative; display: inline-block;">
                            <canvas id="templateCanvas" 
                                    width="<?= $template['Width'] ?>" 
                                    height="<?= $template['Height'] ?>"
                                    style="border: 1px solid #ddd; cursor: crosshair; max-width: 100%; height: auto;">
                            </canvas>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Klik pada canvas untuk menempatkan field. Drag untuk memindahkan posisi.
                                <br><i class="fas fa-keyboard"></i> Gunakan tombol panah keyboard atau klik tombol arrow untuk geser field (Shift+Arrow = 10px).
                                <br><i class="fas fa-expand-arrows-alt"></i> Drag kotak biru di sudut kanan bawah untuk mengubah ukuran font.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<input type="hidden" id="templateId" value="<?= $template['id'] ?>">
<input type="hidden" id="templatePath" value="<?= base_url('uploads/' . $template['FileTemplate']) ?>">

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    let canvas, ctx;
    let templateImage = new Image();
    let fields = [];
    let selectedFieldIndex = -1;
    let isDragging = false;
    let isResizing = false;
    let resizeStartY = 0;
    let resizeStartFontSize = 0;
    let dragOffset = {x: 0, y: 0};
    let hasUnsavedChanges = false;
    let autoSaveKey = 'certificate_config_backup_' + $('#templateId').val();

    $(document).ready(function() {
        canvas = document.getElementById('templateCanvas');
        ctx = canvas.getContext('2d');
        
        // Initial button state
        updateSaveButtonState();

        // Load template image
        templateImage.src = $('#templatePath').val();
        templateImage.onload = function() {
            drawCanvas();
        };

        // Load existing fields
        <?php if (!empty($fields)): ?>
        var dbFields = <?= json_encode($fields) ?>;
        fields = dbFields.map(function(f) {
            return {
                name: f.FieldName,
                label: f.FieldLabel,
                sample: f.FieldLabel, 
                x: parseFloat(f.PosX),
                y: parseFloat(f.PosY),
                font_family: f.FontFamily,
                font_size: parseInt(f.FontSize),
                font_style: f.FontStyle,
                text_align: f.TextAlign,
                text_color: f.TextColor,
                max_width: parseInt(f.MaxWidth)
            };
        });
        <?php endif; ?>

    renderFieldsList();
    
    // Add field button
    $('#btnAddField').click(function() {
        var selectedOption = $('#selectField option:selected');
        var fieldName = selectedOption.val();
        
        if (!fieldName) {
            Swal.fire('Perhatian', 'Pilih field terlebih dahulu', 'warning');
            return;
        }

        // Check if field already exists
        if (fields.find(f => f.name === fieldName)) {
            Swal.fire('Perhatian', 'Field sudah ditambahkan', 'warning');
            return;
        }

        var field = {
            name: fieldName,
            label: selectedOption.data('label'),
            sample: selectedOption.data('sample'),
            x: 100,
            y: 100,
            font_family: 'Arial',
            font_size: 24,
            font_style: 'B',
            text_align: 'C',
            text_color: '#000000',
            max_width: 0
        };

        fields.push(field);
        renderFieldsList();
        drawCanvas();
        markAsDirty(); // Mark as dirty
        
        $('#selectField').val('');
    });

    // Canvas click to place field
    canvas.addEventListener('click', function(e) {
        if (selectedFieldIndex >= 0) {
            var rect = canvas.getBoundingClientRect();
            var scaleX = canvas.width / rect.width;
            var scaleY = canvas.height / rect.height;
            
            fields[selectedFieldIndex].x = (e.clientX - rect.left) * scaleX;
            fields[selectedFieldIndex].y = (e.clientY - rect.top) * scaleY;
            
            drawCanvas();
            updateFieldForm(selectedFieldIndex);
            markAsDirty(); // Mark as dirty
        }
    });

    // Canvas drag and resize
    canvas.addEventListener('mousedown', function(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var mouseX = (e.clientX - rect.left) * scaleX;
        var mouseY = (e.clientY - rect.top) * scaleY;

        // First, check if clicking on resize handle of selected field
        if (selectedFieldIndex >= 0) {
            var field = fields[selectedFieldIndex];
            if (field._handleBounds) {
                var h = field._handleBounds;
                if (mouseX >= h.x && mouseX <= h.x + h.width &&
                    mouseY >= h.y && mouseY <= h.y + h.height) {
                    isResizing = true;
                    resizeStartY = mouseY;
                    resizeStartFontSize = field.font_size;
                    return;
                }
            }
        }

        // Check if clicking on a field (for selection and drag)
        for (let i = fields.length - 1; i >= 0; i--) {
            var field = fields[i];
            ctx.font = `${field.font_style === 'B' ? 'bold' : 'normal'} ${field.font_size}px ${field.font_family}`;
            var textWidth = ctx.measureText(field.sample).width;
            
            // Calculate bounding box based on alignment
            var boxX = field.x;
            if (field.text_align === 'C') {
                boxX = field.x - textWidth / 2;
            } else if (field.text_align === 'R') {
                boxX = field.x - textWidth;
            }
            
            if (mouseX >= boxX - 10 && mouseX <= boxX + textWidth + 10 &&
                mouseY >= field.y - 10 && mouseY <= field.y + field.font_size + 10) {
                selectedFieldIndex = i;
                isDragging = true;
                dragOffset.x = mouseX - field.x;
                dragOffset.y = mouseY - field.y;
                drawCanvas(); // Redraw to show selection
                renderFieldsList();
                break;
            }
        }
    });

    canvas.addEventListener('mousemove', function(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var mouseX = (e.clientX - rect.left) * scaleX;
        var mouseY = (e.clientY - rect.top) * scaleY;
        
        // Handle resizing
        if (isResizing && selectedFieldIndex >= 0) {
            var deltaY = mouseY - resizeStartY;
            var newFontSize = Math.max(8, Math.min(200, resizeStartFontSize + Math.round(deltaY / 2)));
            fields[selectedFieldIndex].font_size = newFontSize;
            
            drawCanvas();
            renderFieldsList();
            markAsDirty();
            return;
        }
        
        // Handle dragging
        if (isDragging && selectedFieldIndex >= 0) {
            fields[selectedFieldIndex].x = mouseX - dragOffset.x;
            fields[selectedFieldIndex].y = mouseY - dragOffset.y;
            
            drawCanvas();
            updateFieldForm(selectedFieldIndex);
            markAsDirty();
            return;
        }
        
        // Update cursor based on what we're hovering over
        if (selectedFieldIndex >= 0) {
            var field = fields[selectedFieldIndex];
            if (field._handleBounds) {
                var h = field._handleBounds;
                if (mouseX >= h.x && mouseX <= h.x + h.width &&
                    mouseY >= h.y && mouseY <= h.y + h.height) {
                    canvas.style.cursor = 'nwse-resize';
                    return;
                }
            }
        }
        canvas.style.cursor = 'crosshair';
    });

    canvas.addEventListener('mouseup', function() {
        isDragging = false;
        isResizing = false;
    });
    
    canvas.addEventListener('mouseleave', function() {
        isDragging = false;
        isResizing = false;
    });

    // Save configuration
    $('#btnSaveConfig').click(function() {
        if (fields.length === 0) {
            Swal.fire('Perhatian', 'Tambahkan minimal 1 field', 'warning');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/perlombaan/save-field-config') ?>',
            type: 'POST',
            data: {
                template_id: $('#templateId').val(),
                fields: fields
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success');
                    hasUnsavedChanges = false;
                    localStorage.removeItem(autoSaveKey); // Clear backup on success
                    updateSaveButtonState();
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Konfigurasi');
                updateSaveButtonState(); // Re-check button state
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Konfigurasi');
            }
        });
    });

    $('#btnTogglePreview').click(function() {
        drawCanvas();
    });

    // Prevent navigation if unsaved
    window.addEventListener('beforeunload', function (e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Intercept back button specifically
    $('a.btn-secondary').click(function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            var link = $(this).attr('href');
            Swal.fire({
                title: 'Perubahan Belum Disimpan',
                text: 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Tinggalkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    hasUnsavedChanges = false; // Disable flag so beforeunload doesn't trigger
                    window.location.href = link;
                }
            });
        }
    });

    // Keyboard arrow keys handler
    $(document).keydown(function(e) {
        if (selectedFieldIndex < 0) return;
        
        // Don't capture if typing in input field
        if ($(e.target).is('input, select, textarea')) return;
        
        var step = e.shiftKey ? 10 : 1; // Hold Shift for larger steps
        var moved = false;
        
        switch(e.keyCode) {
            case 37: // Left arrow
                fields[selectedFieldIndex].x -= step;
                moved = true;
                break;
            case 38: // Up arrow
                fields[selectedFieldIndex].y -= step;
                moved = true;
                break;
            case 39: // Right arrow
                fields[selectedFieldIndex].x += step;
                moved = true;
                break;
            case 40: // Down arrow
                fields[selectedFieldIndex].y += step;
                moved = true;
                break;
        }
        
        if (moved) {
            e.preventDefault(); // Prevent page scrolling
            drawCanvas();
            renderFieldsList();
            markAsDirty();
        }
    });
});

function drawCanvas() {
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Draw template image
    ctx.drawImage(templateImage, 0, 0, canvas.width, canvas.height);
    
    // Draw fields
    fields.forEach((field, index) => {
        ctx.font = `${field.font_style === 'B' ? 'bold' : 'normal'} ${field.font_size}px ${field.font_family}`;
        ctx.fillStyle = field.text_color;
        
        ctx.textAlign = field.text_align === 'C' ? 'center' : (field.text_align === 'R' ? 'right' : 'left');
        ctx.textBaseline = 'top'; // Match CSS top positioning
        
        ctx.fillText(field.sample, field.x, field.y);
        
        // Draw selection box and resize handles for selected field
        if (index === selectedFieldIndex) {
            var textWidth = ctx.measureText(field.sample).width;
            var textHeight = field.font_size;
            
            // Calculate bounding box based on alignment
            var boxX = field.x;
            if (field.text_align === 'C') {
                boxX = field.x - textWidth / 2;
            } else if (field.text_align === 'R') {
                boxX = field.x - textWidth;
            }
            var boxY = field.y;
            
            // Draw bounding box
            ctx.strokeStyle = '#0066ff';
            ctx.lineWidth = 2;
            ctx.setLineDash([5, 3]);
            ctx.strokeRect(boxX - 5, boxY - 5, textWidth + 10, textHeight + 10);
            ctx.setLineDash([]);
            
            // Draw resize handle (bottom-right corner)
            var handleSize = 12;
            var handleX = boxX + textWidth + 5 - handleSize/2;
            var handleY = boxY + textHeight + 5 - handleSize/2;
            
            ctx.fillStyle = '#0066ff';
            ctx.fillRect(handleX, handleY, handleSize, handleSize);
            
            // Draw resize icon in handle
            ctx.fillStyle = '#ffffff';
            ctx.font = '10px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('↘', handleX + handleSize/2, handleY + handleSize/2);
            
            // Store handle bounds for click detection
            field._handleBounds = {
                x: handleX,
                y: handleY,
                width: handleSize,
                height: handleSize
            };
            
            // Store text bounds for reference
            field._textBounds = {
                x: boxX - 5,
                y: boxY - 5,
                width: textWidth + 10,
                height: textHeight + 10
            };
        }
    });
}

function renderFieldsList() {
    var html = '';
    fields.forEach((field, index) => {
        html += `
            <div class="card card-outline ${index === selectedFieldIndex ? 'card-primary' : 'card-default'} mb-2">
                <div class="card-header p-2">
                    <h6 class="card-title">${field.label}</h6>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool btn-sm" onclick="selectField(${index})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-tool btn-sm" onclick="removeField(${index})">
                            <i class="fas fa-trash text-danger"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-2" id="fieldForm${index}" style="display: ${index === selectedFieldIndex ? 'block' : 'none'}">
                    <div class="row">
                        <div class="col-6">
                            <label>X:</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary" onclick="moveField(${index}, -1, 0)" title="Geser Kiri">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                </div>
                                <input type="number" class="form-control form-control-sm text-center" value="${Math.round(field.x)}" 
                                       onchange="updateFieldValue(${index}, 'x', this.value)">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="moveField(${index}, 1, 0)" title="Geser Kanan">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label>Y:</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary" onclick="moveField(${index}, 0, -1)" title="Geser Atas">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                </div>
                                <input type="number" class="form-control form-control-sm text-center" value="${Math.round(field.y)}" 
                                       onchange="updateFieldValue(${index}, 'y', this.value)">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="moveField(${index}, 0, 1)" title="Geser Bawah">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                         <div class="col-12">
                            <label>Font Family:</label>
                            <select class="form-control form-control-sm" onchange="updateFieldValue(${index}, 'font_family', this.value)">
                                <option value="Arial" ${field.font_family === 'Arial' ? 'selected' : ''}>Arial</option>
                                <option value="Times New Roman" ${field.font_family === 'Times New Roman' ? 'selected' : ''}>Times New Roman</option>
                                <option value="Courier New" ${field.font_family === 'Courier New' ? 'selected' : ''}>Courier New</option>
                                <option value="Dejavu Sans" ${field.font_family === 'Dejavu Sans' ? 'selected' : ''}>Dejavu Sans</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label>Font Size:</label>
                        <input type="number" class="form-control form-control-sm" value="${field.font_size}" 
                               onchange="updateFieldValue(${index}, 'font_size', this.value)">
                    </div>
                    <div class="form-group">
                        <label>Color:</label>
                        <input type="color" class="form-control form-control-sm" value="${field.text_color}" 
                               onchange="updateFieldValue(${index}, 'text_color', this.value)">
                    </div>
                    <div class="form-group">
                        <label>Align:</label>
                        <select class="form-control form-control-sm" onchange="updateFieldValue(${index}, 'text_align', this.value)">
                            <option value="L" ${field.text_align === 'L' ? 'selected' : ''}>Left</option>
                            <option value="C" ${field.text_align === 'C' ? 'selected' : ''}>Center</option>
                            <option value="R" ${field.text_align === 'R' ? 'selected' : ''}>Right</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
    });
    $('#fieldsList').html(html);
}

function selectField(index) {
    selectedFieldIndex = index;
    renderFieldsList();
    drawCanvas();
}

function removeField(index) {
    fields.splice(index, 1);
    selectedFieldIndex = -1;
    renderFieldsList();
    drawCanvas();
    markAsDirty();
}

function updateFieldValue(index, key, value) {
    fields[index][key] = key === 'x' || key === 'y' || key === 'font_size' ? parseInt(value) : value;
    drawCanvas();
    markAsDirty();
}

// Move field by delta X and Y (for arrow buttons)
function moveField(index, deltaX, deltaY) {
    if (index < 0 || index >= fields.length) return;
    
    fields[index].x += deltaX;
    fields[index].y += deltaY;
    
    selectedFieldIndex = index;
    drawCanvas();
    renderFieldsList();
    markAsDirty();
}

function updateFieldForm(index) {
    renderFieldsList();
}

// Helpers for Dirty State
function markAsDirty() {
    if (!hasUnsavedChanges) {
        hasUnsavedChanges = true;
        updateSaveButtonState();
    }
    // Save to local storage for crash recovery
    localStorage.setItem(autoSaveKey, JSON.stringify(fields));
}

function updateSaveButtonState() {
    var btn = $('#btnSaveConfig');
    if (hasUnsavedChanges) {
        btn.prop('disabled', false);
        btn.removeClass('btn-secondary').addClass('btn-primary');
        btn.html('<i class="fas fa-save"></i> Simpan Konfigurasi');
    } else {
        btn.prop('disabled', true);
        btn.removeClass('btn-primary').addClass('btn-secondary');
        btn.html('<i class="fas fa-check"></i> Tersimpan');
    }
}
</script>
<?= $this->endSection(); ?>
