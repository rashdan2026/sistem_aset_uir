<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= isset($user) ? 'Edit' : 'Tambah' ?> User</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form User</h6>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <?php if (isset($user)): ?>
                    <?= form_open(base_url('/setting/user/' . $user['id']), ['id' => 'userForm']) ?>
                        <input type="hidden" name="_method" value="PUT">
                    <?php else: ?>
                    <?= form_open(base_url('/setting/user'), ['id' => 'userForm']) ?>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" name="username" id="username" class="form-control"
                               value="<?= old('username', $user['username'] ?? '') ?>" required maxlength="50">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <?= isset($user) ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password *' ?>
                        </label>
                        <input type="password" name="password" id="password" class="form-control"
                               <?= isset($user) ? '' : 'required' ?> minlength="6">
                        <?php if (isset($user)): ?>
                            <div class="form-text">Isi hanya jika ingin mengubah password. Minimal 6 karakter.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap *</label>
                        <input type="text" name="full_name" id="full_name" class="form-control"
                               value="<?= old('full_name', $user['full_name'] ?? '') ?>" required maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                               value="<?= old('email', $user['email'] ?? '') ?>" maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label for="default_unit_kerja_id" class="form-label">Unit Kerja Default</label>
                        <select name="default_unit_kerja_id" id="default_unit_kerja_id" class="form-select select2-unit-kerja">
                            <option value="">Pilih Unit Kerja</option>
                            <?php foreach ($unitKerjaList as $uk): ?>
                                <option value="<?= esc($uk['id_unit_kerja']) ?>"
                                    <?= old('default_unit_kerja_id', $user['default_unit_kerja_id'] ?? '') == $uk['id_unit_kerja'] ? 'selected' : '' ?>>
                                    <?= esc($uk['nama_unit']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Roles *</label>
                        <div class="row">
                            <?php foreach ($roles as $role): ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" name="roles[]"
                                           value="<?= esc($role['id']) ?>"
                                           id="role_<?= esc($role['id']) ?>"
                                           class="form-check-input"
                                           <?= in_array($role['id'], $userRoleIds) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="role_<?= esc($role['id']) ?>">
                                        <strong><?= esc($role['role_name']) ?></strong>
                                        <br><small class="text-muted"><?= esc($role['role_code']) ?></small>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                                   <?= old('is_active', isset($user) ? $user['is_active'] : 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="force_password_change" id="force_password_change" class="form-check-input" value="1"
                                   <?= old('force_password_change', isset($user) ? $user['force_password_change'] : 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="force_password_change">
                                Paksa Ganti Password Saat Login
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="<?= base_url('/setting/user') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function() {
    var unitKerjaData = [
        <?php foreach ($unitKerjaList as $uk): ?>
        { id: <?= $uk['id_unit_kerja'] ?>, text: '<?= esc($uk['nama_unit'], 'js') ?>' },
        <?php endforeach; ?>
    ];

    var selectedUnitKerja = '<?= old('default_unit_kerja_id', $user['default_unit_kerja_id'] ?? '') ?>';

    var $select = $('.select2-unit-kerja');
    $select.select2({
        data: unitKerjaData,
        theme: 'bootstrap-5',
        placeholder: 'Pilih Unit Kerja',
        allowClear: true,
        matcher: function(params, data) {
            if ($.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) return data;
            if (data.id && data.id.toString() === params.term) return data;
            return null;
        }
    });

    if (selectedUnitKerja && selectedUnitKerja !== '') {
        $select.val(selectedUnitKerja).trigger('change');
    }
});
</script>
<?php $this->endSection() ?>