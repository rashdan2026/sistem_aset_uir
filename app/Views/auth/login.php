<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="bg-white text-white p-4 text-center border-bottom">
                    <img src="<?= base_url('/logo.png') ?>" alt="Logo UIR" style="height: 60px; width: auto;" class="mb-2">
                    <h3 class="mb-0 text-dark">Sistem Informasi Aset UIR</h3>
                    <p class="mb-0 small text-muted">Masuk untuk melanjutkan</p>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= esc(session('error')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?= form_open('/auth/login', ['class' => 'needs-validation', 'novalidate' => '']) ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?= old('username') ?>"
                                   required>
                            <div class="invalid-feedback">Username wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <div class="invalid-feedback">Password wajib diisi.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                            </button>
                        </div>
                    <?= form_close() ?>
                </div>
                <div class="card-footer bg-light text-center py-3 small text-muted">
                    © <?= date('Y') ?> Universitas Islam Riau — Sistem Informasi Aset
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap validation
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<?php $this->endSection() ?>