<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= isset($record) ? 'Edit' : 'Tambah' ?> Lantai</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Lantai</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($record)): ?>
                    <form action="<?= base_url('/master/lantai/' . $record['lt_id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                    <?php else: ?>
                    <form action="<?= base_url('/master/lantai') ?>" method="post">
                        <?= csrf_field() ?>
                    <?php endif; ?>

                        <div class="form-group mb-3">
                            <label for="gedung_id">Gedung *</label>
                            <select name="gedung_id" id="gedung_id" class="form-control" required>
                                <option value="">Pilih Gedung</option>
                                <?php foreach ($gedung as $g): ?>
                                <option value="<?= esc($g['gd_id']) ?>" <?= old('gedung_id', $record['gedung_id'] ?? '') == $g['gd_id'] ? 'selected' : '' ?>>
                                    <?= esc($g['nama_gedung']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kode_lantai">Kode Lantai *</label>
                            <input type="text" name="kode_lantai" id="kode_lantai"
                                   class="form-control"
                                   value="<?= old('kode_lantai', $record['kode_lantai'] ?? '') ?>"
                                   required maxlength="30">
                        </div>

                        <div class="form-group mb-3">
                            <label for="nama_lantai">Nama Lantai *</label>
                            <input type="text" name="nama_lantai" id="nama_lantai"
                                   class="form-control"
                                   value="<?= old('nama_lantai', $record['nama_lantai'] ?? '') ?>"
                                   required maxlength="100">
                        </div>

                        <div class="form-group mb-3">
                            <label for="nomor_lantai">Nomor Lantai *</label>
                            <input type="number" name="nomor_lantai" id="nomor_lantai"
                                   class="form-control"
                                   value="<?= old('nomor_lantai', $record['nomor_lantai'] ?? '') ?>"
                                   required min="1">
                        </div>

                        <div class="form-group mb-3">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control"><?= old('keterangan', $record['keterangan'] ?? '') ?></textarea>
                        </div>

                        <?php if (isset($record)): ?>
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active"
                                       class="form-check-input"
                                       value="1" <?= old('is_active', $record['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label for="is_active" class="form-check-label">Aktif</label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="<?= base_url('/master/lantai') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>