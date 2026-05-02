<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= isset($gedung) ? 'Edit' : 'Tambah' ?> Gedung</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Gedung</h6>
                </div>
                <div class="card-body">
                    <?= form_open(isset($gedung) ? base_url('/master/gedung/' . $gedung['gd_id']) : base_url('/master/gedung')) ?>
                        <?php if (isset($gedung)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>
                        
                        <div class="form-group mb-3">
                            <label for="unit_kerja_id">Unit Kerja *</label>
                            <select name="unit_kerja_id" id="unit_kerja_id" class="form-control select2-search" data-placeholder="Ketik nama unit (min. 3 karakter)" required>
                                <?php if (!empty($unitKerja)): ?>
                                    <?php foreach ($unitKerja as $uk): ?>
                                        <option value="<?= esc($uk['id_unit_kerja']) ?>" <?= old('unit_kerja_id', $gedung['unit_kerja_id'] ?? '') == $uk['id_unit_kerja'] ? 'selected' : '' ?>>
                                            <?= esc($uk['nama_unit']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="kode_gedung">Kode Gedung *</label>
                            <input type="text" name="kode_gedung" id="kode_gedung" 
                                   class="form-control" 
                                   value="<?= old('kode_gedung', $gedung['kode_gedung'] ?? '') ?>" 
                                   required maxlength="30">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="nama_gedung">Nama Gedung *</label>
                            <input type="text" name="nama_gedung" id="nama_gedung" 
                                   class="form-control" 
                                   value="<?= old('nama_gedung', $gedung['nama_gedung'] ?? '') ?>" 
                                   required maxlength="150">
                        </div>

                        <div class="form-group mb-3">
                            <label for="jumlah_lantai">Jumlah Lantai *</label>
                            <input type="number" name="jumlah_lantai" id="jumlah_lantai" 
                                   class="form-control" 
                                   value="<?= old('jumlah_lantai', $gedung['jumlah_lantai'] ?? 1) ?>" 
                                   min="1" max="99"
                                   <?= isset($gedung) ? 'readonly' : '' ?>>
                            <?php if (!isset($gedung)): ?>
                                <small class="text-muted">Lantai akan otomatis dibuat dengan kode LT_{id_gedung}_1, LT_{id_gedung}_2, dst.</small>
                            <?php else: ?>
                                <small class="text-muted">Jumlah lantai tidak dapat diubah. Kelola lantai melalui menu <a href="<?= base_url('/master/lantai') ?>">Master Lantai</a>.</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="alamat_ringkas">Alamat Ringkas</label>
                            <input type="text" name="alamat_ringkas" id="alamat_ringkas" 
                                   class="form-control" 
                                   value="<?= old('alamat_ringkas', $gedung['alamat_ringkas'] ?? '') ?>" 
                                   maxlength="255">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control"><?= old('keterangan', $gedung['keterangan'] ?? '') ?></textarea>
                        </div>
                        
                        <?php if (isset($gedung)): ?>
                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" 
                                           class="form-check-input" 
                                           value="1" <?= old('is_active', $gedung['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <label for="is_active" class="form-check-label">Aktif</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="<?= base_url('/master/gedung') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>

<?php $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#unit_kerja_id').select2({
        theme: 'bootstrap-5',
        ajax: {
            url: '<?= base_url('/search/unit-kerja') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term,
                    limit: 20
                };
            },
            processResults: function(data) {
                return { results: data };
            },
            cache: true
        },
        minimumInputLength: 3,
        placeholder: 'Ketik nama unit (min. 3 karakter)',
        allowClear: true
    });
});
</script>
<?php $this->endSection() ?>