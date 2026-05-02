<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= isset($record) ? 'Edit' : 'Tambah' ?> Ruangan</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Ruangan</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($record)): ?>
                    <form action="<?= base_url('/master/ruangan/' . $record['rg_id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                    <?php else: ?>
                    <form action="<?= base_url('/master/ruangan') ?>" method="post">
                        <?= csrf_field() ?>
                    <?php endif; ?>

                        <div class="form-group mb-3">
                            <label for="gedung_id">Gedung *</label>
                            <select name="gedung_id" id="gedung_id" class="form-control" required>
                                <option value="">Pilih Gedung</option>
                                <?php if (!empty($gedung)): ?>
                                    <?php foreach ($gedung as $g): ?>
                                    <option value="<?= esc($g['gd_id']) ?>" <?= (string)old('gedung_id', $record['gedung_id'] ?? '') === (string)$g['gd_id'] ? 'selected' : '' ?>>
                                        <?= esc($g['nama_gedung']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="lantai_id">Lantai *</label>
                            <select name="lantai_id" id="lantai_id" class="form-control" required>
                                <option value="">Pilih Lantai</option>
                                <?php if (isset($lantai) && !empty($lantai)): ?>
                                    <?php foreach ($lantai as $l): ?>
                                    <option value="<?= esc($l['lt_id']) ?>" <?= old('lantai_id', $record['lantai_id'] ?? '') == $l['lt_id'] ? 'selected' : '' ?>>
                                        <?= esc($l['nama_lantai']) ?> (Lt.<?= esc($l['nomor_lantai']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="sub_unit_id">Sub Unit *</label>
                            <select name="sub_unit_id" id="sub_unit_id" class="form-control" required>
                                <option value="">Pilih Sub Unit</option>
                                <?php if (isset($subUnit) && !empty($subUnit)): ?>
                                    <?php foreach ($subUnit as $su): ?>
                                    <option value="<?= esc($su['su_id']) ?>" <?= old('sub_unit_id', $record['sub_unit_id'] ?? '') == $su['su_id'] ? 'selected' : '' ?>>
                                        <?= esc($su['nama_sub_unit']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kode_ruangan">Kode Ruangan *</label>
                            <input type="text" name="kode_ruangan" id="kode_ruangan"
                                   class="form-control"
                                   value="<?= old('kode_ruangan', $record['kode_ruangan'] ?? '') ?>"
                                   required maxlength="30">
                        </div>

                        <div class="form-group mb-3">
                            <label for="nama_ruangan">Nama Ruangan *</label>
                            <input type="text" name="nama_ruangan" id="nama_ruangan"
                                   class="form-control"
                                   value="<?= old('nama_ruangan', $record['nama_ruangan'] ?? '') ?>"
                                   required maxlength="150">
                        </div>

                        <div class="form-group mb-3">
                            <label for="jenis_ruangan">Jenis Ruangan</label>
                            <select name="jenis_ruangan" id="jenis_ruangan" class="form-control">
                                <option value="">Pilih Jenis</option>
                                <?php if (!empty($jenisRuanganOptions)): ?>
                                    <?php foreach ($jenisRuanganOptions as $jenis): ?>
                                    <option value="<?= esc($jenis) ?>" <?= old('jenis_ruangan', $record['jenis_ruangan'] ?? '') == $jenis ? 'selected' : '' ?>><?= esc($jenis) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="penanggung_jawab_id_kpe">Penanggung Jawab</label>
                            <select name="penanggung_jawab_id_kpe" id="penanggung_jawab_id_kpe" class="form-control" data-placeholder="Ketik NPK atau nama (min. 3 karakter)">
                                <?php if (!empty($record['penanggung_jawab_id_kpe'])): ?>
                                    <?php
                                    $pjModel = model('App\Models\Reference\PenanggungJawabReadOnlyModel');
                                    $pjData = $pjModel->getById($record['penanggung_jawab_id_kpe']);
                                    if ($pjData): ?>
                                        <option value="<?= esc($pjData['id_kpe']) ?>" selected><?= esc($pjData['nama_gelar']) ?> (<?= esc($pjData['npk']) ?>)</option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">Opsional. Ketik minimal 3 huruf untuk mencari.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kapasitas">Kapasitas</label>
                            <input type="number" name="kapasitas" id="kapasitas"
                                   class="form-control"
                                   value="<?= old('kapasitas', $record['kapasitas'] ?? '') ?>"
                                   min="1">
                        </div>

                        <div class="form-group mb-3">
                            <label for="luas_m2">Luas (m2)</label>
                            <input type="number" name="luas_m2" id="luas_m2"
                                   class="form-control"
                                   value="<?= old('luas_m2', $record['luas_m2'] ?? '') ?>"
                                   step="0.01" min="0">
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
                            <a href="<?= base_url('/master/ruangan') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('gedung_id').addEventListener('change', function() {
    var gedungId = this.value;
    var lantaiSelect = document.getElementById('lantai_id');
    lantaiSelect.innerHTML = '<option value="">Pilih Lantai</option>';

    if (gedungId) {
        fetch('<?= base_url('/master/lantai/by-gedung/') ?>' + gedungId)
            .then(response => response.json())
            .then(data => {
                data.forEach(function(l) {
                    var option = document.createElement('option');
                    option.value = l.id;
                    option.textContent = l.nama_lantai + ' (Lt.' + l.nomor_lantai + ')';
                    lantaiSelect.appendChild(option);
                });
            });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var $pj = $('#penanggung_jawab_id_kpe');
    if ($pj.length) {
        var initialData = <?= isset($pjData) && !empty($pjData) ? json_encode(['id' => $pjData['id_kpe'], 'text' => $pjData['nama_gelar'] . ' (' . $pjData['npk'] . ')']) : 'null' ?>;
        $pj.select2({
            theme: 'bootstrap-5',
            ajax: {
                url: '<?= base_url('/search/penanggung-jawab') ?>',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { q: params.term, limit: 20 };
                },
                processResults: function(data) {
                    return { results: data };
                },
                cache: true
            },
            minimumInputLength: 3,
            placeholder: 'Ketik NPK atau nama (min. 3 karakter)',
            allowClear: true,
            width: '100%',
            initSelection: function(element, callback) {
                if (initialData) {
                    callback(initialData);
                }
            }
        });
    }
});
</script>
<?php $this->endSection() ?>