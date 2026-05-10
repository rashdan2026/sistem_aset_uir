<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= esc($title) ?></h6>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <form action="<?= isset($subUnit) ? base_url("/master/sub-units/{$subUnit['su_id']}") : base_url('/master/sub-units') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if (isset($subUnit)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>
                        
                        <div class="form-group mb-3">
                            <label for="unit_kerja_id">Unit Kerja *</label>
                            <select name="unit_kerja_id" id="unit_kerja_id" class="form-control select2-search" data-placeholder="Ketik nama unit (min. 3 karakter)" required>
                                <?php if (!empty($unitKerja)): ?>
                                    <?php foreach ($unitKerja as $uk): ?>
                                        <option value="<?= esc($uk['id_unit_kerja']) ?>" <?= (old('unit_kerja_id', $subUnit['unit_kerja_id'] ?? '') == $uk['id_unit_kerja'] ? 'selected' : '') ?>>
                                            <?= esc($uk['nama_unit']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="kode_sub_unit">Kode Sub Unit *</label>
                            <input type="text" name="kode_sub_unit" id="kode_sub_unit" 
                                   class="form-control" 
                                   value="<?= old('kode_sub_unit', $subUnit['kode_sub_unit'] ?? '') ?>" 
                                   required maxlength="30">
                        </div>
                        
                        <div class="form-group mb-3" style="position: relative;">
                            <label for="nama_sub_unit">Nama Sub Unit *</label>
                            <input type="text" name="nama_sub_unit" id="nama_sub_unit" 
                                   class="form-control" 
                                   value="<?= old('nama_sub_unit', $subUnit['nama_sub_unit'] ?? '') ?>" 
                                   required maxlength="150" autocomplete="off">
                            <div id="suggestions" class="suggestions-box" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="jenis_sub_unit">Jenis Sub Unit</label>
                            <input type="text" name="jenis_sub_unit" id="jenis_sub_unit" 
                                   class="form-control" 
                                   value="<?= old('jenis_sub_unit', $subUnit['jenis_sub_unit'] ?? '') ?>" 
                                   maxlength="50">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control"><?= old('keterangan', $subUnit['keterangan'] ?? '') ?></textarea>
                        </div>
                        
                        <?php if (isset($subUnit)): ?>
                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" 
                                           class="form-check-input" 
                                           value="1" <?= (old('is_active', $subUnit['is_active'] ?? 1)) ? 'checked' : '' ?>>
                                    <label for="is_active" class="form-check-label">Aktif</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="<?= base_url('/master/sub-units') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.suggestions-box {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    max-height: 200px;
    overflow-y: auto;
    z-index: 9999;
    margin-top: 2px;
}

.suggestion-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item:hover {
    background-color: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#unit_kerja_id').select2({
        theme: 'bootstrap-5',
        ajax: {
            url: '<?= base_url('/search/unit-kerja') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term,
                    limit: 20
                };
            },
            processResults: function(data) {
                return { results: data };
            },
            cache: true
        },
        minimumInputLength: 3,
        placeholder: 'Ketik nama unit (min. 3 karakter)',
        allowClear: true
    });

    var input = document.getElementById('nama_sub_unit');
    var suggestionsBox = document.getElementById('suggestions');
    var timeout = null;

    input.addEventListener('keyup', function () {
        var keyword = this.value.trim();

        clearTimeout(timeout);

        if (keyword.length < 4) {
            suggestionsBox.style.display = 'none';
            suggestionsBox.innerHTML = '';
            return;
        }

        timeout = setTimeout(function() {
            fetch('<?= base_url("/master/sub-units/search") ?>?q=' + encodeURIComponent(keyword))
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    suggestionsBox.innerHTML = '';

                    if (data.length === 0) {
                        suggestionsBox.style.display = 'none';
                        return;
                    }

                    data.forEach(function(item) {
                        var div = document.createElement('div');
                        div.className = 'suggestion-item';
                        div.textContent = item.nama_sub_unit;
                        div.addEventListener('click', function () {
                            input.value = item.nama_sub_unit;
                            suggestionsBox.style.display = 'none';
                        });
                        suggestionsBox.appendChild(div);
                    });

                    suggestionsBox.style.display = 'block';
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    suggestionsBox.style.display = 'none';
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#nama_sub_unit') && !e.target.closest('#suggestions')) {
            suggestionsBox.style.display = 'none';
        }
    });
});
</script>
<?php $this->endSection(); ?>