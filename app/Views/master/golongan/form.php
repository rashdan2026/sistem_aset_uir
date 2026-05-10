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
                    <h6 class="m-0 font-weight-bold text-primary">Form Golongan</h6>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <form action="<?= isset($record) ? base_url('/master/golongan/' . $record['gl_id']) : base_url('/master/golongan') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if (isset($record)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="kode_golongan" class="form-label">Kode Golongan *</label>
                            <input type="text" name="kode_golongan" id="kode_golongan" class="form-control"
                                   value="<?= old('kode_golongan', $record['kode_golongan'] ?? '') ?>" required maxlength="30">
                        </div>

                        <div class="mb-3" style="position: relative;">
                            <label for="nama_golongan" class="form-label">Nama Golongan *</label>
                            <input type="text" name="nama_golongan" id="nama_golongan" class="form-control"
                                   value="<?= old('nama_golongan', $record['nama_golongan'] ?? '') ?>" required maxlength="150" autocomplete="off">
                            <div id="suggestions" class="suggestions-box" style="display: none;"></div>
                        </div>

                        <div class="mb-3">
                            <label for="kelompok" class="form-label">Kelompok</label>
                            <select name="kelompok" id="kelompok" class="form-select">
                                <option value="">Pilih Kelompok</option>
                                <option value="bangunan" <?= old('kelompok', $record['kelompok'] ?? '') == 'bangunan' ? 'selected' : '' ?>>Bangunan</option>
                                <option value="non_bangunan" <?= old('kelompok', $record['kelompok'] ?? '') == 'non_bangunan' ? 'selected' : '' ?>>Non Bangunan</option>
                                <option value="lainnya" <?= old('kelompok', $record['kelompok'] ?? '') == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                            </select>
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
                            <a href="<?= base_url('/master/golongan') ?>" class="btn btn-secondary">Batal</a>
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
    var input = document.getElementById('nama_golongan');
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
            fetch('<?= base_url("/master/golongan/search") ?>?q=' + encodeURIComponent(keyword))
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
                        div.textContent = item.nama_golongan + ' (' + item.kode_golongan + ')';
                        div.addEventListener('click', function () {
                            input.value = item.nama_golongan;
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
        if (!e.target.closest('#nama_golongan') && !e.target.closest('#suggestions')) {
            suggestionsBox.style.display = 'none';
        }
    });
});
</script>
<?php $this->endSection() ?>