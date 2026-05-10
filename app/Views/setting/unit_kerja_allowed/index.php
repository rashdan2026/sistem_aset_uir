<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Setting Unit Kerja</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Unit Kerja (<?= $totalAllowed ?> / <?= $totalAll ?> diaktifkan)
            </h6>
            <button type="button" class="btn btn-sm btn-primary" id="btnSaveAll">
                <i class="bi bi-save"></i> Simpan Semua
            </button>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle"></i>
                Pilih unit kerja mana saja yang boleh tampil di aplikasi. Unit yang tidak diaktifkan tidak akan muncul di dropdown, filter, atau pencarian pada semua menu.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">
                                <input type="checkbox" id="checkAll" title="Pilih Semua">
                            </th>
                            <th width="80">ID</th>
                            <th>Nama Unit Kerja</th>
                            <th width="100" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($units as $unit): ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="unit-checkbox"
                                       value="<?= esc($unit['id_unit_kerja']) ?>"
                                       <?= $unit['is_allowed'] ? 'checked' : '' ?>>
                            </td>
                            <td><?= esc($unit['id_unit_kerja']) ?></td>
                            <td><?= esc($unit['nama_unit']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $unit['is_allowed'] ? 'bg-success' : 'bg-secondary' ?> status-badge"
                                      data-id="<?= esc($unit['id_unit_kerja']) ?>">
                                    <?= $unit['is_allowed'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var checkAll = document.getElementById('checkAll');
    var checkboxes = document.querySelectorAll('.unit-checkbox');
    var btnSave = document.getElementById('btnSaveAll');

    function getCsrfToken() {
        return document.querySelector('input[name="<?= csrf_token() ?>"]').value;
    }

    checkAll.addEventListener('change', function() {
        var checked = this.checked;
        checkboxes.forEach(function(cb) {
            cb.checked = checked;
        });
    });

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            var id = this.value;
            var isActive = this.checked ? 1 : 0;
            var badge = document.querySelector('.status-badge[data-id="' + id + '"]');
            var csrfToken = getCsrfToken();

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= base_url('/setting/unit-kerja/toggle') ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var res = JSON.parse(xhr.responseText);
                        if (res.success && badge) {
                            badge.textContent = isActive ? 'Aktif' : 'Nonaktif';
                            badge.className = 'badge ' + (isActive ? 'bg-success' : 'bg-secondary') + ' status-badge';
                            badge.setAttribute('data-id', id);
                        } else if (res.message) {
                            alert('Gagal: ' + res.message);
                        }
                    } catch (e) {
                        alert('Respons tidak valid dari server.');
                    }
                } else if (xhr.status === 403) {
                    alert('Akses ditolak. Anda tidak memiliki izin.');
                } else {
                    alert('Terjadi kesalahan: ' + xhr.statusText);
                }
            };
            xhr.onerror = function() {
                alert('Koneksi gagal. Silakan coba lagi.');
            };
            var csrfName = '<?= csrf_token() ?>';
            var csrfToken = getCsrfToken();
            var params = csrfName + '=' + encodeURIComponent(csrfToken) + '&id_unit_kerja=' + id + '&is_active=' + isActive;
            xhr.send(params);
        });
    });

    btnSave.addEventListener('click', function() {
        var allowedIds = [];
        checkboxes.forEach(function(cb) {
            if (cb.checked) {
                allowedIds.push(cb.value);
            }
        });

        var csrfToken = getCsrfToken();

        btnSave.disabled = true;
        btnSave.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= base_url('/setting/unit-kerja/save-all') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.onload = function() {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-save"></i> Simpan Semua';

            if (xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert('Gagal: ' + (res.message || 'Unknown error'));
                    }
                } catch (e) {
                    alert('Respons tidak valid dari server.');
                }
            } else if (xhr.status === 403) {
                alert('Akses ditolak. Anda tidak memiliki izin.');
            } else {
                alert('Terjadi kesalahan (' + xhr.status + '): ' + xhr.statusText);
            }
        };
        xhr.onerror = function() {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-save"></i> Simpan Semua';
            alert('Koneksi gagal. Silakan coba lagi.');
        };

        var params = 'csrf_test_name=' + encodeURIComponent(csrfToken);
        allowedIds.forEach(function(id) {
            params += '&allowed_ids[]=' + id;
        });
        xhr.send(params);
    });
});
</script>
<?php $this->endSection() ?>
