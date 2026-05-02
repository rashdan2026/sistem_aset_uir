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
                <div class="card-body">
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

                        <div class="mb-3">
                            <label for="nama_golongan" class="form-label">Nama Golongan *</label>
                            <input type="text" name="nama_golongan" id="nama_golongan" class="form-control"
                                   value="<?= old('nama_golongan', $record['nama_golongan'] ?? '') ?>" required maxlength="150">
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
<?php $this->endSection() ?>