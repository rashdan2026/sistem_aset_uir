<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= isset($kategori) ? 'Edit' : 'Tambah' ?> Kategori</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Kategori</h6>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <?= form_open(isset($kategori) ? base_url('/master/kategori/' . $kategori['kt_id']) : base_url('/master/kategori')) ?>
                        <?php if (isset($kategori)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>

                        <?php if (isset($kategori)): ?>
                            <input type="hidden" name="kode_kategori" value="<?= esc($kategori['kode_kategori'] ?? '') ?>">
                        <?php else: ?>
                            <input type="hidden" name="kode_kategori" value="">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="gl_id" class="form-label">Golongan *</label>
                            <select name="gl_id" id="gl_id" class="form-select" required>
                                <option value="">-- Pilih Golongan --</option>
                                <?php if (!empty($golonganOptions)): ?>
                                    <?php foreach ($golonganOptions as $g): ?>
                                        <option value="<?= $g['gl_id'] ?>" <?= old('gl_id', $kategori['gl_id'] ?? '') == $g['gl_id'] ? 'selected' : '' ?>><?= esc($g['nama_golongan']) ?> (<?= esc($g['kode_golongan']) ?>)</option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">Pilih golongan terlebih dahulu. Golongan induk harus sudah ada.</small>
                        </div>

                        <div class="mb-3" style="position: relative;">
                            <label for="nama_kategori" class="form-label">Nama Kategori *</label>
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control"
                                   value="<?= old('nama_kategori', $kategori['nama_kategori'] ?? '') ?>" required maxlength="150" autocomplete="off">
                            <div id="suggestions" class="suggestions-box" style="display: none;"></div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3"><?= old('keterangan', $kategori['keterangan'] ?? '') ?></textarea>
                        </div>

                        <?php if (isset($kategori)): ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" <?= old('is_active', $kategori['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="<?= base_url('/master/kategori') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    <?= form_close() ?>
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
    var input = document.getElementById('nama_kategori');
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
            var golonganId = document.getElementById('gl_id').value;
            var url = '<?= base_url("/master/kategori/search") ?>?q=' + encodeURIComponent(keyword);
            if (golonganId) {
                url += '&gl_id=' + encodeURIComponent(golonganId);
            }

            fetch(url)
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
                        div.textContent = item.nama_kategori + ' (' + item.kode_kategori + ')';
                        div.addEventListener('click', function () {
                            input.value = item.nama_kategori;
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
        if (!e.target.closest('#nama_kategori') && !e.target.closest('#suggestions')) {
            suggestionsBox.style.display = 'none';
        }
    });
});
</script>
<?php $this->endSection() ?>