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
        <div class="card-body" style="overflow: visible;">
            <?php
                $isEdit = isset($record) && !empty($record);
                $actionUrl = $isEdit ? base_url('/master/aset/' . $record['all_id']) : base_url('/master/aset');
            ?>
            <form action="<?= $actionUrl ?>" method="post">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <h6 class="font-weight-bold text-primary mb-3">Identitas Aset</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Golongan <span class="text-danger">*</span></label>
                            <select name="golongan_id" id="golongan_id" class="form-control" required onchange="loadKategori()">
                                <option value="">-- Pilih Golongan --</option>
                                <?php foreach ($golonganOptions as $g): ?>
                                    <option value="<?= $g['gl_id'] ?>" <?= ($selectedGolonganId ?? '') == $g['gl_id'] ? 'selected' : '' ?>><?= esc($g['nama_golongan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="kategori_id" class="form-control" required onchange="loadSubKategori()">
                                <option value="">-- Pilih Golongan terlebih dahulu --</option>
                                <?php if (!empty($kategoriOptions)): ?>
                                    <?php foreach ($kategoriOptions as $k): ?>
                                        <option value="<?= $k['kt_id'] ?>" <?= ($selectedKategoriId ?? '') == $k['kt_id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Sub Kategori <span class="text-danger">*</span></label>
                            <select name="sub_kategori_id" id="sub_kategori_id" class="form-control" required onchange="onSubKategoriChange()">
                                <option value="">-- Pilih Kategori terlebih dahulu --</option>
                                <?php if (!empty($subKategoriOptions)): ?>
                                    <?php foreach ($subKategoriOptions as $sk): ?>
                                        <option value="<?= $sk['sk_id'] ?>" <?= ($selectedSubKategoriId ?? '') == $sk['sk_id'] ? 'selected' : '' ?>><?= esc($sk['nama_sub_kategori']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
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
                            <label>Merk <span id="merk-wajib" class="text-danger" style="display:none">*</span></label>
                            <select name="merk_id" id="merk_id" class="form-control" onchange="loadType()">
                                <option value="">-- Tidak Ada --</option>
                                <?php foreach ($merk as $m): ?>
                                    <option value="<?= $m['mr_id'] ?>" <?= ($isEdit && ($record['merk_id'] ?? '') == $m['mr_id']) ? 'selected' : '' ?>><?= esc($m['nama_merk']) ?> (<?= esc($m['kode_merk']) ?>)</option>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sumber Dana <span class="text-danger">*</span></label>
                            <select name="sumber_dana_id" id="sumber_dana_id" class="form-control" required>
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
                                    <option value="<?= $u['id_unit_kerja'] ?>" <?= ($isEdit && ($record['unit_kerja_id'] ?? '') == $u['id_unit_kerja']) ? 'selected' : '' ?>><?= esc($u['nama_unit']) ?> (<?= esc($u['kode_unit'] ?? $u['id_unit_kerja']) ?>)</option>
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
                                    <?php
                                        $rText = trim(
                                            ($r['nama_gedung'] ?? '') .
                                            (empty($r['nama_lantai']) ? '' : ' / ' . $r['nama_lantai']) .
                                            ' / ' . $r['nama_ruangan'] .
                                            ' (' . $r['kode_ruangan'] . ')'
                                        );
                                    ?>
                                    <option value="<?= $r['rg_id'] ?>" <?= ($isEdit && ($record['ruangan_id'] ?? '') == $r['rg_id']) ? 'selected' : '' ?>><?= esc($rText) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Pilih Ruangan (otomatis terfilter sesuai Sub Unit). Format: Gedung / Lantai / Ruangan.</small>
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
                                        <option value="<?= esc($pjData['id_kpe']) ?>" selected><?= format_penanggung_jawab($pjData) ?></option>
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

<style>
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var skSelect = document.getElementById('sub_kategori_id');
    if (skSelect && skSelect.value) {
        onSubKategoriChange();
    }
    initSelect2();
});

var initialMerkId = <?= $isEdit && !empty($record['merk_id']) ? json_encode((string)$record['merk_id']) : 'null' ?>;
var savedTypeId = <?= $isEdit && !empty($record['type_id']) ? json_encode((string)$record['type_id']) : 'null' ?>;

var ruanganData = [
    <?php foreach ($ruangan as $r): ?>
        <?php
            $rText = trim(
                ($r['nama_gedung'] ?? '') .
                (empty($r['nama_lantai']) ? '' : ' / ' . $r['nama_lantai']) .
                ' / ' . $r['nama_ruangan'] .
                ' (' . $r['kode_ruangan'] . ')'
            );
        ?>
        { id: '<?= esc($r['rg_id']) ?>', text: '<?= esc($rText) ?>' },
    <?php endforeach; ?>
];
var savedRuanganId = <?= $isEdit && !empty($record['ruangan_id']) ? json_encode((string)$record['ruangan_id']) : 'null' ?>;

function loadKategori() {
    var golonganId = document.getElementById('golongan_id').value;
    var kategoriSelect = document.getElementById('kategori_id');
    var subKategoriSelect = document.getElementById('sub_kategori_id');

    kategoriSelect.innerHTML = '<option value="">Memuat...</option>';
    subKategoriSelect.innerHTML = '<option value="">-- Pilih Kategori terlebih dahulu --</option>';

    document.getElementById('merk_id').value = '';
    var typeSelect = document.getElementById('type_id');
    typeSelect.innerHTML = '<option value="">-- Pilih Type --</option>';
    document.getElementById('merk-wajib').style.display = 'none';
    document.getElementById('merk_id').required = false;
    document.getElementById('type-wajib').style.display = 'none';
    document.getElementById('type_id').required = false;

    if (!golonganId) {
        kategoriSelect.innerHTML = '<option value="">-- Pilih Golongan terlebih dahulu --</option>';
        return;
    }

    fetch('<?= base_url('/master/aset/get-kategori/') ?>' + golonganId)
        .then(r => r.json())
        .then(data => {
            kategoriSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            data.forEach(function(k) {
                var opt = document.createElement('option');
                opt.value = k.kt_id;
                opt.textContent = k.nama_kategori;
                kategoriSelect.appendChild(opt);
            });
        })
        .catch(function() {
            kategoriSelect.innerHTML = '<option value="">-- Pilih Golongan terlebih dahulu --</option>';
        });
}

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

    if (!kategoriId) {
        subKategoriSelect.innerHTML = '<option value="">-- Pilih Kategori terlebih dahulu --</option>';
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
        })
        .catch(function() {
            subKategoriSelect.innerHTML = '<option value="">-- Pilih Kategori terlebih dahulu --</option>';
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
    var $ruangan = $('#ruangan_id');
    if ($ruangan.length && $ruangan.data('select2')) {
        $ruangan.select2('destroy');
    }
    subUnitSelect.innerHTML = '<option value="">Memuat...</option>';
    if (!unitId) {
        subUnitSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
        resetRuanganSelect([]);
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
            resetRuanganSelect([]);
        });
}

function formatRuanganText(r) {
    var parts = [];
    if (r.nama_gedung) parts.push(r.nama_gedung);
    if (r.nama_lantai) parts.push(r.nama_lantai);
    parts.push(r.nama_ruangan + ' (' + r.kode_ruangan + ')');
    return parts.join(' / ');
}

function resetRuanganSelect(data) {
    var $ruangan = $('#ruangan_id');
    if (!$ruangan.length) return;
    if ($ruangan.data('select2')) {
        $ruangan.select2('destroy');
    }
    $ruangan.empty();
    var placeholder = '<option value="">-- Tidak Ada --</option>';
    $ruangan.append(placeholder);
    var dataItems = Array.isArray(data) ? data : [];
    if (dataItems.length) {
        dataItems.forEach(function(r) {
            var opt = new Option(formatRuanganText(r), String(r.rg_id), false, false);
            $ruangan.append(opt);
        });
    }
    $ruangan.val('').trigger('change');
    $ruangan.select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih Ruangan atau ketik minimal 3 huruf (Gedung / Lantai / Ruangan)',
        allowClear: true,
        width: '100%',
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }
            var term = params.term.toLowerCase();
            var text = data.text.toLowerCase();
            if (term.length < 3) {
                return data;
            }
            if (text.indexOf(term) > -1) {
                return data;
            }
            return null;
        }
    });
}

function loadRuangan() {
    var subUnitId = document.getElementById('sub_unit_id').value;

    if (!subUnitId) {
        resetRuanganSelect([]);
        return;
    }

    var $ruangan = $('#ruangan_id');
    if ($ruangan.length && $ruangan.data('select2')) {
        $ruangan.select2('destroy');
    }
    var ruanganSelect = document.getElementById('ruangan_id');
    ruanganSelect.innerHTML = '<option value="">Memuat...</option>';
    fetch('<?= base_url('/master/aset/get-ruangan/') ?>' + subUnitId)
        .then(r => r.json())
        .then(data => {
            resetRuanganSelect(data || []);
        })
        .catch(function() {
            resetRuanganSelect([]);
        });
}


function initRuanganSelect2() {
    var $ruangan = $('#ruangan_id');
    if (!$ruangan.length || $ruangan.data('select2')) return;
    var initialData = Array.isArray(ruanganData) ? ruanganData : [];
    $ruangan.select2({
        theme: 'bootstrap-5',
        data: initialData,
        placeholder: 'Pilih Ruangan atau ketik minimal 3 huruf (Gedung / Lantai / Ruangan)',
        allowClear: true,
        width: '100%',
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }
            var term = params.term.toLowerCase();
            var text = data.text.toLowerCase();
            if (term.length < 3) {
                return data;
            }
            if (text.indexOf(term) > -1) {
                return data;
            }
            return null;
        }
    });

    if (savedRuanganId !== null) {
        var hasMatch = false;
        initialData.forEach(function(item) {
            if (String(item.id) === String(savedRuanganId)) hasMatch = true;
        });
        if (hasMatch) {
            $ruangan.val(String(savedRuanganId)).trigger('change');
        }
    }
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

    var unitKerjaData = [
        <?php foreach ($unitKerja as $u): ?>
            { id: '<?= esc($u['id_unit_kerja']) ?>', text: '<?= esc($u['nama_unit']) ?> (<?= esc($u['kode_unit'] ?? $u['id_unit_kerja']) ?>)' },
        <?php endforeach; ?>
    ];

    var merkData = [
        <?php foreach ($merk as $m): ?>
            { id: '<?= esc($m['mr_id']) ?>', text: '<?= esc($m['nama_merk']) ?> (<?= esc($m['kode_merk']) ?>)' },
        <?php endforeach; ?>
    ];

    var $unitKerja = $('#unit_kerja_id');
    if ($unitKerja.length) {
        $unitKerja.select2({
            theme: 'bootstrap-5',
            data: unitKerjaData,
            placeholder: 'Pilih Unit Kerja atau ketik minimal 3 huruf',
            allowClear: true,
            width: '100%',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }
                if (typeof data.text === 'undefined') {
                    return null;
                }
                var term = params.term.toLowerCase();
                var text = data.text.toLowerCase();
                if (term.length < 3) {
                    return data;
                }
                if (text.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }
        });
    }

    var $merk = $('#merk_id');
    if ($merk.length) {
        $merk.select2({
            theme: 'bootstrap-5',
            data: merkData,
            placeholder: 'Pilih Merk atau ketik minimal 3 huruf',
            allowClear: true,
            width: '100%',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }
                if (typeof data.text === 'undefined') {
                    return null;
                }
                var term = params.term.toLowerCase();
                var text = data.text.toLowerCase();
                if (term.length < 3) {
                    return data;
                }
                if (text.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }
        });

        if (initialMerkId !== null) {
            $merk.val(initialMerkId);
            loadTypeForInitialMerk(initialMerkId);
        }
    }

    initRuanganSelect2();
}

function loadTypeForInitialMerk(merkId) {
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

            if (savedTypeId !== null) {
                var existingOpt = typeSelect.querySelector('option[value="' + savedTypeId + '"]');
                if (existingOpt) {
                    typeSelect.value = savedTypeId;
                }
            }
        });
}
</script>
<?php $this->endSection(); ?>