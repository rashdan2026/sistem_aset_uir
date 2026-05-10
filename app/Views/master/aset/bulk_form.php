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

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h6 class="m-0 font-weight-bold"><i class="bi bi-layers"></i> Mode Massal (Bulk) &mdash; Semua aset akan dibuat sekaligus dalam satu batch</h6>
        </div>
        <div class="card-body" style="overflow: visible;">
            <form action="<?= base_url('/master/bulk-aset') ?>" method="post">
                <?= csrf_field() ?>

                <div class="alert alert-info py-2">
                    <i class="bi bi-info-circle"></i> Form ini akan membuat <strong>beberapa record aset sekaligus</strong> berdasarkan Jumlah yang diisi. Semua aset dalam 1 proses akan memiliki <code>batch_id</code> yang sama untuk memudahkan tracking.
                </div>

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
                            <input type="text" name="nama_aset" class="form-control" value="<?= old('nama_aset') ?>" maxlength="200" required>
                            <small class="form-text text-muted">Nama ini akan dipakai oleh semua aset dalam batch.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control font-weight-bold text-danger" value="<?= old('jumlah', 1) ?>" min="1" max="100" required>
                            <small class="form-text text-muted">Maks. 100 unit per proses.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>No Aset Lama</label>
                            <input type="text" name="nomor_aset_lama" class="form-control" value="<?= old('nomor_aset_lama') ?>" placeholder="Opsional">
                        </div>
                    </div>
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
                                        <option value="<?= $g['gl_id'] ?>"><?= esc($g['nama_golongan']) ?></option>
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
                                    <option value="<?= $m['mr_id'] ?>"><?= esc($m['nama_merk']) ?> (<?= esc($m['kode_merk']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Type/Model <span id="type-wajib" class="text-danger" style="display:none">*</span></label>
                            <select name="type_id" id="type_id" class="form-control">
                                <option value="">-- Pilih Type --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kondisi <span class="text-danger">*</span></label>
                            <select name="kondisi_id" id="kondisi_id" class="form-control" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <?php foreach ($kondisi as $k): ?>
                                    <option value="<?= $k['kd_id'] ?>"><?= esc($k['nama_kondisi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sumber Dana <span class="text-danger">*</span></label>
                            <select name="sumber_dana_id" id="sumber_dana_id" class="form-control" required>
                                <option value="">-- Tidak Ada --</option>
                                <?php foreach ($sumberDana as $sd): ?>
                                    <option value="<?= $sd['sd_id'] ?>"><?= esc($sd['nama_sumber_dana']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tahun Perolehan</label>
                            <input type="number" name="tahun_perolehan" class="form-control" value="<?= old('tahun_perolehan', date('Y')) ?>" min="1900" max="2100">
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="font-weight-bold text-primary mb-3">Lokasi &amp; Penanggung Jawab</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Unit Kerja <span class="text-danger">*</span></label>
                            <select name="unit_kerja_id" id="unit_kerja_id" class="form-control" required onchange="loadSubUnit()">
                                <option value="">-- Pilih Unit Kerja --</option>
                                <?php foreach ($unitKerja as $u): ?>
                                    <option value="<?= $u['id_unit_kerja'] ?>"><?= esc($u['nama_unit']) ?> (<?= esc($u['kode_unit'] ?? $u['id_unit_kerja']) ?>)</option>
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
                                    <option value="<?= $su['su_id'] ?>"><?= esc($su['nama_sub_unit']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ruangan <span id="ruangan-wajib" class="text-danger" style="display:none">*</span></label>
                            <select name="ruangan_id" id="ruangan_id" class="form-control">
                                <option value="">-- Tidak Ada --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Penanggung Jawab</label>
                            <select name="penanggung_jawab_id_kpe" id="penanggung_jawab_id_kpe" class="form-control" data-placeholder="Ketik NPK atau nama (min. 3 karakter)">
                            </select>
                            <small class="form-text text-muted">Opsional. Ketik minimal 3 huruf untuk mencari.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control" value="<?= old('tanggal_perolehan') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nilai Perolehan (Rp)</label>
                            <input type="number" name="nilai_perolehan" class="form-control" value="<?= old('nilai_perolehan') ?>" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Spesifikasi</label>
                    <textarea name="spesifikasi" class="form-control" rows="2"><?= old('spesifikasi') ?></textarea>
                    <small class="form-text text-muted">Opsional. Akan dipakai oleh semua aset dalam batch.</small>
                </div>

                <input type="hidden" name="status_aset" value="draft">

                <div class="mt-3">
                    <button type="submit" class="btn btn-warning btn-lg" onclick="return confirm('Anda akan membuat ' + document.getElementById('jumlah')?.value + ' aset sekaligus. Lanjutkan?')">
                        <i class="bi bi-layers"></i> Registrasi Massal
                    </button>
                    <a href="<?= base_url('/master/aset') ?>" class="btn btn-secondary ml-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.suggestions-box {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    max-height: 200px;
    overflow-y: auto;
    z-index: 9999;
    margin-top: 2px;
}
.suggestion-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}
.suggestion-item:last-child {
    border-bottom: none;
}
.suggestion-item:hover {
    background-color: #f8f9fa;
}
</style>

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

    var $merk = $('#merk_id');
    if ($merk.length && $merk.data('select2')) {
        $merk.val(null).trigger('change');
    } else {
        document.getElementById('merk_id').value = '';
    }
    var typeSelect = document.getElementById('type_id');
    typeSelect.innerHTML = '<option value="">-- Pilih Type --</option>';
    document.getElementById('merk-wajib').style.display = 'none';
    document.getElementById('merk_id').required = false;
    document.getElementById('type-wajib').style.display = 'none';
    document.getElementById('type_id').required = false;
    document.getElementById('ruangan-wajib').style.display = 'none';
    document.getElementById('ruangan_id').required = false;

    loadGolongan(kategoriId);

    if (!kategoriId) {
        subKategoriSelect.innerHTML = '<option value="">-- Pilih Sub Kategori --</option>';
        return;
    }

    fetch('<?= base_url('/master/bulk-aset/get-sub-kategori/') ?>' + kategoriId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
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
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('merk-wajib').style.display = (data.wajib_merk == 1) ? 'inline' : 'none';
            document.getElementById('merk_id').required = (data.wajib_merk == 1);
            document.getElementById('type-wajib').style.display = (data.wajib_type == 1) ? 'inline' : 'none';
            document.getElementById('type_id').required = (data.wajib_type == 1);
            document.getElementById('ruangan-wajib').style.display = (data.wajib_ruangan == 1) ? 'inline' : 'none';
            document.getElementById('ruangan_id').required = (data.wajib_ruangan == 1);
        })
        .catch(function() {});
}

function loadType() {
    var merkId = document.getElementById('merk_id').value;
    var typeSelect = document.getElementById('type_id');
    typeSelect.innerHTML = '<option value="">Memuat...</option>';
    if (!merkId) {
        typeSelect.innerHTML = '<option value="">-- Pilih Type --</option>';
        return;
    }
    fetch('<?= base_url('/master/bulk-aset/get-type/') ?>' + merkId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
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
    fetch('<?= base_url('/master/bulk-aset/get-sub-unit/') ?>' + unitId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
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
    fetch('<?= base_url('/master/bulk-aset/get-ruangan/') ?>' + subUnitId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
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
    fetch('<?= base_url('/master/bulk-aset/get-golongan/') ?>' + kategoriId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            golonganSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
            data.forEach(function(g) {
                var opt = document.createElement('option');
                opt.value = g.gl_id;
                opt.textContent = g.nama_golongan;
                golonganSelect.appendChild(opt);
            });
        })
        .catch(function() {
            golonganSelect.innerHTML = '<option value="">-- Tidak Ada --</option>';
        });
}

function initSelect2() {
    var merkData = [
        <?php foreach ($merk as $m): ?>
            { id: '<?= esc($m['mr_id']) ?>', text: '<?= esc($m['nama_merk']) ?> (<?= esc($m['kode_merk']) ?>)' },
        <?php endforeach; ?>
    ];

    var $merk = $('#merk_id');
    if ($merk.length && !$merk.data('select2')) {
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
    }

    var unitKerjaData = [
        <?php foreach ($unitKerja as $u): ?>
            { id: '<?= esc($u['id_unit_kerja']) ?>', text: '<?= esc($u['nama_unit']) ?> (<?= esc($u['kode_unit'] ?? $u['id_unit_kerja']) ?>)' },
        <?php endforeach; ?>
    ];

    var $unitKerja = $('#unit_kerja_id');
    if ($unitKerja.length && !$unitKerja.data('select2')) {
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

    var $pj = $('#penanggung_jawab_id_kpe');
    if ($pj.length && !$pj.data('select2')) {
        $pj.select2({
            theme: 'bootstrap-5',
            ajax: {
                url: '<?= base_url('/search/penanggung-jawab') ?>',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(data) {
                    return { results: data };
                }
            },
            minimumInputLength: 3,
            placeholder: 'Ketik NPK atau nama...',
            allowClear: true
        });
    }
}
</script>
<?php $this->endSection(); ?>