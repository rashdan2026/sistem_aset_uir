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

    <div class="card shadow">
        <div class="card-body">
            <?php
                $isEdit = isset($record);
                $kdId = $isEdit ? $record['kd_id'] : '';
            ?>
            <form action="<?= base_url($isEdit ? '/master/kondisi-barang/' . $kdId : '/master/kondisi-barang') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="<?= $isEdit ? 'PUT' : 'POST' ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="kode_kondisi" value="<?= esc($record['kode_kondisi'] ?? '') ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Kondisi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kondisi" class="form-control" value="<?= old('nama_kondisi', $record['nama_kondisi'] ?? '') ?>" maxlength="100" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Level Kondisi <span class="text-danger">*</span></label>
                            <input type="number" name="level_kondisi" class="form-control" value="<?= old('level_kondisi', $record['level_kondisi'] ?? '') ?>" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"><?= old('keterangan', $record['keterangan'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_available_for_use" class="form-check-input" id="is_available_for_use" value="1" <?= ($isEdit && $record['is_available_for_use']) ? 'checked' : '' ?><?= !$isEdit ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_available_for_use">Tersedia untuk penggunaan</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="warna">Warna Badge</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" name="warna" id="warna" class="form-control form-control-color"
                               value="<?= old('warna', $record['warna'] ?? '#198754') ?>"
                               title="Pilih warna untuk badge di daftar Kondisi"
                               style="max-width: 60px; height: 38px;">
                        <code id="warnaPreview"><?= old('warna', $record['warna'] ?? '#198754') ?></code>
                        <small class="text-muted">Hex format #RRGGBB (auto-prepend # jika lupa)</small>
                    </div>
                </div>

                <script>
                document.getElementById('warna').addEventListener('input', function() {
                    document.getElementById('warnaPreview').textContent = this.value;
                });
                </script>

                <?php if ($isEdit): ?>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" <?= $record['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('/master/kondisi-barang') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>