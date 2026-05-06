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
                $sdId = $isEdit ? $record['sd_id'] : '';
            ?>
            <form action="<?= base_url($isEdit ? '/master/sumber-dana/' . $sdId : '/master/sumber-dana') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="<?= $isEdit ? 'PUT' : 'POST' ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Sumber Dana <span class="text-danger">*</span></label>
                            <input type="text" name="kode_sumber_dana" class="form-control" value="<?= old('kode_sumber_dana', $record['kode_sumber_dana'] ?? '') ?>" maxlength="30" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Sumber Dana <span class="text-danger">*</span></label>
                            <input type="text" name="nama_sumber_dana" class="form-control" value="<?= old('nama_sumber_dana', $record['nama_sumber_dana'] ?? '') ?>" maxlength="150" required>
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
                <a href="<?= base_url('/master/sumber-dana') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>