<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title) ?></h1>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body" style="overflow: visible;">
            <?= csrf_field() ?>
            <?php
                $isEdit = isset($record);
                $skId = $isEdit ? $record['sk_id'] : '';
            ?>
            <form action="<?= base_url($isEdit ? '/master/sub-kategori/' . $skId : '/master/sub-kategori') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="<?= $isEdit ? 'PUT' : 'POST' ?>">

                <div class="form-group">
                    <label>Kategori <span class="text-danger">*</span></label>
                    <select name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k['kt_id'] ?>" <?= ($isEdit && $record['kategori_id'] == $k['kt_id']) ? 'selected' : '' ?>>
                                <?= esc($k['nama_kategori']) ?> (<?= esc($k['kode_kategori']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Sub Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="kode_sub_kategori" class="form-control" value="<?= old('kode_sub_kategori', $record['kode_sub_kategori'] ?? '') ?>" maxlength="30" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="position: relative;">
                            <label>Nama Sub Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_sub_kategori" id="nama_sub_kategori" class="form-control" value="<?= old('nama_sub_kategori', $record['nama_sub_kategori'] ?? '') ?>" maxlength="150" required autocomplete="off">
                            <div id="suggestions" class="suggestions-box" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Wajib Merk</label><br>
                            <div class="form-check">
                                <input type="checkbox" name="wajib_merk" class="form-check-input" id="wajib_merk" value="1" <?= ($isEdit && $record['wajib_merk']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="wajib_merk">Ya</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Wajib Type</label><br>
                            <div class="form-check">
                                <input type="checkbox" name="wajib_type" class="form-check-input" id="wajib_type" value="1" <?= ($isEdit && $record['wajib_type']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="wajib_type">Ya</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Wajib Ruangan</label><br>
                            <div class="form-check">
                                <input type="checkbox" name="wajib_ruangan" class="form-check-input" id="wajib_ruangan" value="1" <?= ($isEdit && $record['wajib_ruangan']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="wajib_ruangan">Ya</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"><?= old('keterangan', $record['keterangan'] ?? '') ?></textarea>
                </div>

                <?php if ($isEdit): ?>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" <?= $record['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('/master/sub-kategori') ?>" class="btn btn-secondary">Batal</a>
            </form>
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
    var input = document.getElementById('nama_sub_kategori');
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
            fetch('<?= base_url("/master/sub-kategori/search") ?>?q=' + encodeURIComponent(keyword))
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
                        div.textContent = item.nama_sub_kategori;
                        div.addEventListener('click', function () {
                            input.value = item.nama_sub_kategori;
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
        if (!e.target.closest('#nama_sub_kategori') && !e.target.closest('#suggestions')) {
            suggestionsBox.style.display = 'none';
        }
    });
});
</script>
<?php $this->endSection(); ?>