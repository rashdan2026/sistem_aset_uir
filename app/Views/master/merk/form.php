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
                    <h6 class="m-0 font-weight-bold text-primary">Form Merk</h6>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <form action="<?= isset($record) ? base_url('/master/merk/' . $record['mr_id']) : base_url('/master/merk') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if (isset($record)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="kode_merk" class="form-label">Kode Merk *</label>
                            <input type="text" name="kode_merk" id="kode_merk" class="form-control"
                                   value="<?= old('kode_merk', $record['kode_merk'] ?? '') ?>" required maxlength="30">
                        </div>

                        <div class="mb-3" style="position: relative;">
                            <label for="nama_merk" class="form-label">Nama Merk *</label>
                            <input type="text" name="nama_merk" id="nama_merk" class="form-control"
                                   value="<?= old('nama_merk', $record['nama_merk'] ?? '') ?>" required maxlength="100" autocomplete="off">
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
                            <a href="<?= base_url('/master/merk') ?>" class="btn btn-secondary">Batal</a>
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
    var input = document.getElementById('nama_merk');
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
            fetch('<?= base_url("/master/merk/search") ?>?q=' + encodeURIComponent(keyword))
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
                        div.textContent = item.nama_merk + ' (' + item.kode_merk + ')';
                        div.addEventListener('click', function () {
                            input.value = item.nama_merk;
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
        if (!e.target.closest('#nama_merk') && !e.target.closest('#suggestions')) {
            suggestionsBox.style.display = 'none';
        }
    });
});
</script>
<?php $this->endSection() ?>