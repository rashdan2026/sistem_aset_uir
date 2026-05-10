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
                                    <option value="<?= esc($g['gd_id']) ?>" <?= (!empty($record['gedung_id']) && $record['gedung_id'] == $g['gd_id']) ? 'selected' : '' ?>>
                                        <?= esc($g['nama_gedung']) ?> (<?= esc($g['kode_gedung']) ?>)
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
                                <?php if (!empty($subUnit)): ?>
                                    <?php foreach ($subUnit as $su): ?>
                                    <option value="<?= esc($su['su_id']) ?>" <?= (!empty($record['sub_unit_id']) && $record['sub_unit_id'] == $su['su_id']) ? 'selected' : '' ?>>
                                        <?= esc($su['nama_sub_unit']) ?> (<?= esc($su['kode_sub_unit']) ?>)
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

                        <div class="form-group mb-3" style="position: relative;">
                            <label for="nama_ruangan">Nama Ruangan *</label>
                            <input type="text" name="nama_ruangan" id="nama_ruangan"
                                   class="form-control"
                                   value="<?= old('nama_ruangan', $record['nama_ruangan'] ?? '') ?>"
                                   required maxlength="150" autocomplete="off">
                            <div id="suggestions" class="suggestions-box" style="display: none;"></div>
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
    var namaRuanganInput = document.getElementById('nama_ruangan');
    var suggestionsBox = document.getElementById('suggestions');
    var timeout = null;

    namaRuanganInput.addEventListener('keyup', function () {
        var keyword = this.value.trim();

        clearTimeout(timeout);

        if (keyword.length < 4) {
            suggestionsBox.style.display = 'none';
            suggestionsBox.innerHTML = '';
            return;
        }

        timeout = setTimeout(function() {
            fetch('<?= base_url("/master/ruangan/search") ?>?q=' + encodeURIComponent(keyword))
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    suggestionsBox.innerHTML = '';

                    if (data.length === 0) {
                        suggestionsBox.style.display = 'none';
                        return;
                    }

                    data.forEach(function(item) {
                        var div = document.createElement('div');
                        div.className = 'suggestion-item';
                        div.textContent = item.nama_ruangan + ' (' + item.kode_ruangan + ')';
                        div.addEventListener('click', function () {
                            namaRuanganInput.value = item.nama_ruangan;
                            suggestionsBox.style.display = 'none';
                        });
                        suggestionsBox.appendChild(div);
                    });

                    suggestionsBox.style.display = 'block';
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    suggestionsBox.style.display = 'none';
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#nama_ruangan') && !e.target.closest('#suggestions')) {
            suggestionsBox.style.display = 'none';
        }
    });
});

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
        var initialDataPj = <?= isset($pjData) && !empty($pjData) ? json_encode(['id' => $pjData['id_kpe'], 'text' => $pjData['nama_gelar'] . ' (' . $pjData['npk'] . ')']) : 'null' ?>;
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
                if (initialDataPj) {
                    callback(initialDataPj);
                }
            }
        });
    }

    var gedungData = [
        <?php if (!empty($gedung)): ?>
            <?php foreach ($gedung as $g): ?>
                { id: '<?= esc($g['gd_id']) ?>', text: '<?= esc($g['nama_gedung']) ?> (<?= esc($g['kode_gedung']) ?>)' },
            <?php endforeach; ?>
        <?php endif; ?>
    ];

    var subUnitData = [
        <?php if (!empty($subUnit)): ?>
            <?php foreach ($subUnit as $su): ?>
                { id: '<?= esc($su['su_id']) ?>', text: '<?= esc($su['nama_sub_unit']) ?> (<?= esc($su['kode_sub_unit']) ?>)' },
            <?php endforeach; ?>
        <?php endif; ?>
    ];

    var $gedung = $('#gedung_id');
    if ($gedung.length) {
        $gedung.select2({
            theme: 'bootstrap-5',
            data: gedungData,
            placeholder: 'Pilih Gedung atau ketik minimal 3 huruf',
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
        }).on('select2:select', function(e) {
            var gedungId = e.params.data.id;
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
    }

    var $subUnit = $('#sub_unit_id');
    if ($subUnit.length) {
        $subUnit.select2({
            theme: 'bootstrap-5',
            data: subUnitData,
            placeholder: 'Pilih Sub Unit atau ketik minimal 3 huruf',
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
});
</script>
<?php $this->endSection() ?>