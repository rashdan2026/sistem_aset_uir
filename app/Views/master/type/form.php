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
                <div class="card-body">
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
                                        <option value="<?= esc($m['mr_id']) ?>" <?= old('merk_id', $record['merk_id'] ?? '') == $m['mr_id'] ? 'selected' : '' ?>>
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

                        <div class="mb-3">
                            <label for="nama_type" class="form-label">Nama Type *</label>
                            <input type="text" name="nama_type" id="nama_type" class="form-control"
                                   value="<?= old('nama_type', $record['nama_type'] ?? '') ?>" required maxlength="100">
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
<?php $this->endSection() ?>