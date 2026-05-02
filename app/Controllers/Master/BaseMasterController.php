<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class BaseMasterController extends BaseController
{
    protected $model;
    protected $modelName;
    protected $viewPath;
    protected $baseRoute;
    protected $pageTitle;
    protected $breadcrumb = [];

    /**
     * Return paginated list
     */
    public function index()
    {
        $db = db_connect();
        $builder = $db->table($this->model->table);
        
        // Apply filters
        $search = $this->request->getGet('q');
        if (!empty($search)) {
            $builder->groupStart()
                ->like('kode_' . $this->modelName, $search)
                ->orLike('nama_' . $this->modelName, $search)
                ->groupEnd();
        }

        // Filter active
        if (method_exists($this->model, 'where') && $this->model->table !== 'sys_users') {
            $builder->where('is_active', 1);
        }

        $builder->orderBy('created_at', 'DESC');
        $pager = $builder->paginate(15, $this->modelName);

        $data = [
            'title' => $this->pageTitle,
            'records' => $pager,
            'pager' => $pager,
            'search' => $search,
            'breadcrumb' => $this->breadcrumb,
        ];

        return view($this->viewPath . '/index', $data);
    }

    /**
     * Show single record
     */
    public function show(int $id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view($this->viewPath . '/show', [
            'title' => 'Detail ' . $this->pageTitle,
            'record' => $record,
            'breadcrumb' => array_merge($this->breadcrumb, [
                ['label' => 'Detail', 'url' => null]
            ]),
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view($this->viewPath . '/form', [
            'title' => 'Tambah ' . $this->pageTitle,
            'mode' => 'create',
            'record' => null,
            'breadcrumb' => array_merge($this->breadcrumb, [
                ['label' => 'Tambah', 'url' => null]
            ]),
        ]);
    }

    /**
     * Store new record
     */
    public function store()
    {
        $data = $this->request->getPost();
        unset($data['submit']);

        $data['is_active'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->model->insert($data)) {
            $this->flashSuccess($this->pageTitle . ' berhasil ditambahkan.');
            return redirect()->to($this->baseRoute);
        }

        $this->flashError('Gagal menyimpan: ' . implode(', ', $this->model->errors()));
        return redirect()->back()->withInput();
    }

    /**
     * Show edit form
     */
    public function edit(int $id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view($this->viewPath . '/form', [
            'title' => 'Ubah ' . $this->pageTitle,
            'mode' => 'edit',
            'record' => $record,
            'breadcrumb' => array_merge($this->breadcrumb, [
                ['label' => 'Ubah', 'url' => null]
            ]),
        ]);
    }

    /**
     * Update record
     */
    public function update(int $id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->request->getPost();
        unset($data['submit']);

        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->model->update($id, $data)) {
            $this->flashSuccess($this->pageTitle . ' berhasil diperbarui.');
            return redirect()->to($this->baseRoute);
        }

        $this->flashError('Gagal menyimpan: ' . implode(', ', $this->model->errors()));
        return redirect()->back()->withInput();
    }

    /**
     * Soft delete record
     */
    public function delete(int $id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if model uses soft deletes
        if (method_exists($this->model, 'delete')) {
            $this->model->delete($id);
        }

        $this->flashSuccess($this->pageTitle . ' berhasil dihapus.');
        return redirect()->to($this->baseRoute);
    }

    /**
     * Toggle active status
     */
    public function toggle(int $id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return $this->jsonResponse(['success' => false, 'message' => 'Record not found'], 404);
        }

        $newStatus = ($record['is_active'] ?? 1) ? 0 : 1;
        $this->model->update($id, ['is_active' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->jsonResponse([
            'success' => true,
            'message' => 'Status berhasil diubah.',
            'new_status' => $newStatus
        ]);
    }
}