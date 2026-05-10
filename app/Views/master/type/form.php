<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Type/Model</h6>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <form action="<?= isset($record) ? base_url('/master/type/' . $record['ty_id']) : base_url('/master/type') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if (isset($record)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="merk_id" class="form-label">Merk</label>
                            <select name="merk_id" id="merk_id" class="form-select">
                                <option value="">-- Tanpa Merk --</option>
                                <?php if (!empty($merk)): ?>
                                    <?php foreach ($merk as $m): ?>
                                        <option value="<?= esc($m['mr_id']) ?>" <?= (!empty($record['merk_id']) && $record['merk_id'] == $m['mr_id']) ? 'selected' : '' ?>>
                                            <?= esc($m['nama_merk']) ?> (<?= esc($m['kode_merk']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kode_type" class="form-label">Kode Type *</label>
                            <input type="text" name="kode_type" id="kode_type" class="form-control"
                                   value="<?= old('kode_type', $record['kode_type'] ?? '') ?>" required maxlength="30">
                        </div>

                        <div class="mb-3" style="position: relative;">
                            <label for="nama_type" class="form-label">Nama Type *</label>
                            <input type="text" name="nama_type" id="nama_type" class="form-control"
                                   value="<?= old('nama_type', $record['nama_type'] ?? '') ?>" required maxlength="100" autocomplete="off">
                            <div id="suggestions" class="suggestions-box" style="display: none;"></div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3"><?= old('keterangan', $record['keterangan'] ?? '') ?></textarea>
                        </div>

                        <?php if (isset($record)): ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                                       <?= old('is_active', $record['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label for="is_active" class="form-check-label">Aktif</label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="<?= base_url('/master/type') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var merkData = [
        <?php if (!empty($merk)): ?>
            <?php foreach ($merk as $m): ?>
                { id: '<?= esc($m['mr_id']) ?>', text: '<?= esc($m['nama_merk']) ?> (<?= esc($m['kode_merk']) ?>)' },
            <?php endforeach; ?>
        <?php endif; ?>
    ];

    var $merk = $('#merk_id');
    if ($merk.length) {
        $merk.select2({
            theme: 'bootstrap-5',
            data: merkData,
            placeholder: 'Pilih Merk atau ketik minimal 3 huruf',
            allowClear: true,
            width: '100%',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }
                if (typeof data.text === 'undefined') {
                    return null;
                }
                var term = params.term.toLowerCase();
                var text = data.text.toLowerCase();
                if (term.length < 3) {
                    return data;
                }
                if (text.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }
        });
    }

    var input = document.getElementById('nama_type');
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
            fetch('<?= base_url("/master/type/search") ?>?q=' + encodeURIComponent(keyword))
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
                        div.textContent = item.nama_type + ' (' + item.kode_type + ')';
                        div.addEventListener('click', function () {
                            input.value = item.nama_type;
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
        if (!e.target.closest('#nama_type') && !e.target.closest('#suggestions')) {
            suggestionsBox.style.display = 'none';
        }
    });
});
</script>

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
<?php $this->endSection() ?>