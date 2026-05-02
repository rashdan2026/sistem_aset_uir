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
                $isEdit = isset($record) && !empty($record);
                $actionUrl = $isEdit ? base_url('/master/aset/' . $record['all_id']) : base_url('/master/aset');
            ?>
            <form action="<?= $actionUrl ?>" method="post">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="put">
                <?php endif; ?>

                <!-- Section: Identitas Aset -->
                <h6 class="font-weight-bold text-primary mb-3">Identitas Aset</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="kategori_id" class="form-control" required onchange="loadSubKategori()">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k['kt_id'] ?>" <?= ($selectedKategoriId ?? '') == $k['kt_id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sub Kategori <span class="text-danger">*</span></label>
                            <select name="sub_kategori_id" id="sub_kategori_id" class="form-control" required onchange="onSubKategoriChange()">
                                <option value="">-- Pilih Sub Kategori --</option>
                                <?php foreach ($subKategori as $sk): ?>
                                    <option value="<?= $sk['sk_id'] ?>" <?= ($selectedSubKategoriId ?? '') == $sk['sk_id'] ? 'selected' : '' ?>><?= esc($sk['nama_sub_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Aset <span class="text-danger">*</span></label>
                            <input type="text" name="nama_aset" id="nama_aset" class="form-control" value="<?= old('nama_aset', $record['nama_aset'] ?? '') ?>" maxlength="200" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>No Aset Lama</label>
                            <input type="text" name="nomor_aset_lama" class="form-control" value="<?= old('nomor_aset_lama', $record['nomor_aset_lama'] ?? '') ?>" placeholder="Jika ada">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" value="<?= old('serial_number', $record['serial_number'] ?? '') ?>" placeholder="Opsional">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Spesifikasi</label>
                    <textarea name="spesifikasi" class="form-control" rows="3"><?= old('spesifikasi', $record['spesifikasi'] ?? '') ?></textarea>
                </div>

                <hr>
                <h6 class="font-weight-bold text-primary mb-3">Klasifikasi Aset</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Golongan</label>
                            <select name="golongan_id" id="golongan_id" class="form-control">
                                <option value="">-- Pilih Kategori terlebih dahulu --</option>
                                <?php if (!empty($golongan)): ?>
                                    <?php foreach ($golongan as $g): ?>
                                        <option value="<?= $g['gl_id'] ?>" <?= ($isEdit && ($record['golongan_id'] ?? '') == $g['gl_id']) ? 'selected' : '' ?>><?= esc($g['nama_golongan']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">Pilih Kategori terlebih dahulu untuk memfilter Golongan.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Merk <span id="merk-wajib" class="text-danger" style="display:none">*</span></label>
                            <select name="merk_id" id="merk_id" class="form-control" onchange="loadType()">
                                <option value="">-- Tidak Ada --</option>
                                <?php foreach ($merk as $m): ?>
                                    <option value="<?= $m['mr_id'] ?>" <?= ($isEdit && ($record['merk_id'] ?? '') == $m['mr_id']) ? 'selected' : '' ?>><?= esc($m['nama_merk']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Type/Model <span id="type-wajib" class="text-danger" style="display:none">*</span></label>
                            <select name="type_id" id="type_id" class="form-control">
                                <option value="">-- Pilih Type --</option>
                                <?php foreach ($type as $t): ?>
                                    <option value="<?= $t['ty_id'] ?>" <?= ($isEdit && ($record['type_id'] ?? '') == $t['ty_id']) ? 'selected' : '' ?>><?= esc($t['nama_type']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kondisi <span class="text-danger">*</span></label>
                            <select name="kondisi_id" id="kondisi_id" class="form-control" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <?php foreach ($kondisi as $k): ?>
                                    <option value="<?= $k['kd_id'] ?>" <?= ($isEdit && ($record['kondisi_id'] ?? '') == $k['kd_id']) ? 'selected' : '' ?>><?= esc($k['nama_kondisi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sumber Dana</label>
                            <select name="sumber_dana_id" class="form-control">
                                <option value="">-- Tidak Ada --</option>
                                <?php foreach ($sumberDana as $sd): ?>
                                    <option value="<?= $sd['sd_id'] ?>" <?= ($isEdit && ($record['sumber_dana_id'] ?? '') == $sd['sd_id']) ? 'selected' : '' ?>><?= esc($sd['nama_sumber_dana']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="font-weight-bold text-primary mb-3">Lokasi & Penanggung Jawab</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Unit Kerja <span class="text-danger">*</span></label>
                            <select name="unit_kerja_id" id="unit_kerja_id" class="form-control" required onchange="loadSubUnit()">
                                <option value="">-- Pilih Unit Kerja --</option>
                                <?php foreach ($unitKerja as $u): ?>
                                    <option value="<?= $u['id_unit_kerja'] ?>" <?= ($isEdit && ($record['unit_kerja_id'] ?? '') == $u['id_unit_kerja']) ? 'selected' : '' ?>><?= esc($u['nama_unit']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Sub Unit</label>
                            <select name="sub_unit_id" id="sub_unit_id" class="form-control" onchange="loadRuangan()">
                                <option value="">-- Tidak Ada --</option>
                                <?php foreach ($subUnit as $su): ?>
                                    <option value="<?= $su['su_id'] ?>" <?= ($isEdit && ($record['sub_unit_id'] ?? '') == $su['su_id']) ? 'selected' : '' ?>><?= esc($su['nama_sub_unit']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ruangan <span id="ruangan-wajib" class="text-danger" style="display:none">*</span></label>
                            <select name="ruangan_id" id="ruangan_id" class="form-control">
                                <option value="">-- Tidak Ada --</option>
                                <?php foreach ($ruangan as $r): ?>
                                    <option value="<?= $r['rg_id'] ?>" <?= ($isEdit && ($record['ruangan_id'] ?? '') == $r['rg_id']) ? 'selected' : '' ?>><?= esc($r['nama_ruangan']) ?> (<?= esc($r['kode_ruangan']) ?>) - <?= esc($r['nama_gedung'] ?? '') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Penanggung Jawab</label>
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
                    </div>
                </div>

                <hr>
                <h6 class="font-weight-bold text-primary mb-3">Perolehan</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tahun Perolehan</label>
                            <input type="number" name="tahun_perolehan" class="form-control" value="<?= old('tahun_perolehan', $record['tahun_perolehan'] ?? date('Y')) ?>" min="1900" max="2100">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control" value="<?= old('tanggal_perolehan', $record['tanggal_perolehan'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nilai Perolehan (Rp)</label>
                            <input type="number" name="nilai_perolehan" class="form-control" value="<?= old('nilai_perolehan', $record['nilai_perolehan'] ?? '') ?>" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status Aset</label>
                            <select name="status_aset" class="form-control">
                                <option value="draft" <?= ($isEdit && ($record['status_aset'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Draft</option>
                                <option value="aktif" <?= ($isEdit && ($record['status_aset'] ?? '') === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= ($isEdit && ($record['status_aset'] ?? '') === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                                <option value="hilang" <?= ($isEdit && ($record['status_aset'] ?? '') === 'hilang') ? 'selected' : '' ?>>Hilang</option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if ($isEdit): ?>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" <?= ($record['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Perbarui' : 'Simpan' ?></button>
                    <a href="<?= base_url('/master/aset') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var skSelect = document.getElementById('sub_kategori_id');
    if (skSelect && skSelect.value) {
        onSubKategoriChange();
    }
    initSelect2();
});

function loadSubKategori() {
    var kategoriId = document.getElementById('kategori_id').value;
    var subKategoriSelect = document.getElementById('sub_kategori_id');
    subKategoriSelect.innerHTML = '<option value="">Memuat...</option>';

    document.getElementById('merk_id').value = '';
    var typeSelect = document.getElementById('type_id');
    typeSelect.innerHTML = '<option value="">-- Pilih Type --</option>';
    document.getElementById('merk-wajib').style.display = 'none';
    document.getElementById('merk_id').required = false;
    document.getElementById('type-wajib').style.display = 'none';
    document.getElementById('type_id').required = false;

    loadGolongan(kategoriId);

    if (!kategoriId) {
        subKategoriSelect.innerHTML = '<option value="">-- Pilih Sub Kategori --</option>';
        return;
    }

    fetch('<?= base_url('/master/aset/get-sub-kategori/') ?>' + kategoriId)
        .then(r => r.json())
        .then(data => {
            subKategoriSelect.innerHTML = '<option value="">-- Pilih Sub Kategori --</option>';
            data.forEach(function(sk) {
                var opt = document.createElement('option');
                opt.value = sk.sk_id;
                opt.textContent = sk.nama_sub_kategori;
                subKategoriSelect.appendChild(opt);
            });
        });
}

function onSubKategoriChange() {
    var skId = document.getElementById('sub_kategori_id').value;
    if (!skId) {
        document.getElementById('merk-wajib').style.display = 'none';
        document.getElementById('merk_id').required = false;
        document.getElementById('type-wajib').style.display = 'none';
        document.getElementById('type_id').required = false;
        document.getElementById('ruangan-wajib').style.display = 'none';
        document.getElementById('ruangan_id').required = false;
        return;
    }
    fetch('<?= base_url('/master/aset/lookup-sub-kategori/') ?>' + skId)
        .then(r => r.json())
        .then(data => {
            document.getElementById('merk-wajib').style.display = (data.wajib_merk == 1) ? 'inline' : 'none';
            document.getElementById('merk_id').required = (data.wajib_merk == 1);
            document.getElementById('type-wajib').style.display = (data.wajib_type == 1) ? 'inline' : 'none';
            document.getElementById('type_id').required = (data.wajib_type == 1);
            document.getElementById('ruangan-wajib').style.display = (data.wajib_ruangan == 1) ? 'inline' : 'none';
            document.getElementById('ruangan_id').required = (data.wajib_ruangan == 1);
        })
        .catch(() => {});
}

function loadType() {
    var merkId = document.getElementById('merk_id').value;
    var typeSelect = document.getElementById('type_id');
    typeSelect.innerHTML = '<option value="">Memuat...</option>';
    if (!merkId) {
        typeSelect.innerHTML = '<option value="">-- Pilih Type --</option>';
        return;
    }
    fetch('<?= base_url('/master/aset/get-type/') ?>' + merkId)
        .then(r => r.json())
        .then(data => {
            typeSelect.innerHTML = '<option value="">-- Pilih Type --</option>';
            data.forEach(function(t) {
                var opt = document.createElement('option');
                opt.value = t.ty_id;
                opt.textContent = t.nama_type;
                typeSelect.appendChild(opt);
            });
        });
}

function loadSubUnit() {
    var unitId = document.getElementById('unit_kerja_id').value;
    var subUnitSelect = document.getElementById('sub_unit_id');
    var ruanganSelect = document.getElementById('ruangan_id');
    subUnitSelect.innerHTML = '<option value="">Memuat...</option>';
    ruanganSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
    if (!unitId) {
        subUnitSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
        return;
    }
    fetch('<?= base_url('/master/aset/get-sub-unit/') ?>' + unitId)
        .then(r => r.json())
        .then(data => {
            subUnitSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
            data.forEach(function(su) {
                var opt = document.createElement('option');
                opt.value = su.su_id;
                opt.textContent = su.nama_sub_unit;
                subUnitSelect.appendChild(opt);
            });
        });
}

function loadRuangan() {
    var subUnitId = document.getElementById('sub_unit_id').value;
    var ruanganSelect = document.getElementById('ruangan_id');

    if (!subUnitId) {
        ruanganSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
        return;
    }

    ruanganSelect.innerHTML = '<option value="">Memuat...</option>';
    fetch('<?= base_url('/master/aset/get-ruangan/') ?>' + subUnitId)
        .then(r => r.json())
        .then(data => {
            ruanganSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
            data.forEach(function(r) {
                var opt = document.createElement('option');
                opt.value = r.rg_id;
                opt.textContent = r.nama_ruangan + ' (' + r.kode_ruangan + ') - ' + (r.nama_gedung || '');
                ruanganSelect.appendChild(opt);
            });
        });
}

function loadGolongan(kategoriId) {
    var golonganSelect = document.getElementById('golongan_id');
    if (!golonganSelect) return;
    golonganSelect.innerHTML = '<option value="">Memuat...</option>';

    if (!kategoriId) {
        golonganSelect.innerHTML = '<option value="">-- Pilih Kategori terlebih dahulu --</option>';
        return;
    }

    fetch('<?= base_url('/master/aset/get-golongan/') ?>' + kategoriId)
        .then(r => r.json())
        .then(data => {
            golonganSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
            data.forEach(function(g) {
                var opt = document.createElement('option');
                opt.value = g.gl_id;
                opt.textContent = g.nama_golongan;
                <?php if (!empty($record['golongan_id'])): ?>
                if (g.gl_id == <?= $record['golongan_id'] ?>) {
                    opt.selected = true;
                }
                <?php endif; ?>
                golonganSelect.appendChild(opt);
            });
        })
        .catch(function() {
            golonganSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
        });
}

function initSelect2() {
    var $pj = $('#penanggung_jawab_id_kpe');
    if ($pj.length && !$pj.data('select2')) {
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
            allowClear: true
        });
    }
}
</script>
<?php $this->endSection(); ?>