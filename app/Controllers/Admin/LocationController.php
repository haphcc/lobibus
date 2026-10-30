<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Location;

final class LocationController extends AdminController
{
    private Location $locations;

    public function __construct()
    {
        $this->locations = new Location();
    }

    public function index(): void
    {
        $this->view('admin.locations.index', [
            'title' => 'Quan ly dia diem',
            'locations' => $this->locations->all(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.locations.create', ['title' => 'Them dia diem'], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields(['name' => 'tên địa điểm'])) {
            $this->redirect('/admin/locations/create', 'error', $error);
        }
        if ($error = $this->validateCoordinates('/admin/locations/create')) {
            $this->redirect('/admin/locations/create', 'error', $error);
        }

        $this->locations->create($_POST);
        $this->redirect('/admin/locations', 'success', 'Đã thêm địa điểm.');
    }

    public function edit(): void
    {
        $location = $this->locations->find($this->queryInt('id'));
        if ($location === null) {
            $this->redirect('/admin/locations', 'error', 'Không tìm thấy địa điểm.');
        }

        $this->view('admin.locations.edit', [
            'title' => 'Sua dia diem',
            'location' => $location,
        ], 'admin');
    }

    public function update(): void
    {
        $id = $this->postInt('id');
        if ($error = $this->requireFields(['name' => 'tên địa điểm'])) {
            $this->redirect('/admin/locations/edit?id=' . $id, 'error', $error);
        }
        if ($error = $this->validateCoordinates('/admin/locations/edit?id=' . $id)) {
            $this->redirect('/admin/locations/edit?id=' . $id, 'error', $error);
        }

        $this->locations->update($id, $_POST);
        $this->redirect('/admin/locations', 'success', 'Đã cập nhật địa điểm.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->locations->isUsed($id)) {
            $this->redirect('/admin/locations', 'error', 'Không thể xóa địa điểm đang được dùng trong tuyến xe.');
        }

        $this->locations->delete($id);
        $this->redirect('/admin/locations', 'success', 'Đã xóa địa điểm.');
    }

    private function validateCoordinates(string $path): ?string
    {
        unset($path);
        return $this->requireOptionalNumber('latitude', 'Vĩ độ', -90, 90)
            ?? $this->requireOptionalNumber('longitude', 'Kinh độ', -180, 180);
    }
}
