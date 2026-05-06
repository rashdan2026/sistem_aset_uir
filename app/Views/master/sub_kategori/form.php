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
                        <div class="form-group">
                            <label>Nama Sub Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_sub_kategori" class="form-control" value="<?= old('nama_sub_kategori', $record['nama_sub_kategori'] ?? '') ?>" maxlength="150" required>
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
<?php $this->endSection(); ?>