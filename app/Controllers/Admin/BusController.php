<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Bus;

final class BusController extends AdminController
{
    private Bus $buses;

    public function __construct()
    {
        $this->buses = new Bus();
    }

    public function index(): void
    {
        $this->view('admin.buses.index', [
            'title' => 'Quan ly xe',
            'buses' => $this->buses->all(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.buses.create', ['title' => 'Them xe'], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields([
            'name' => 'tên xe',
            'license_plate' => 'biển số',
            'total_seats' => 'tổng số ghế',
        ])) {
            $this->redirect('/admin/buses/create', 'error', $error);
        }
        if ($error = $this->validatePayload()) {
            $this->redirect('/admin/buses/create', 'error', $error);
        }

        $this->buses->create($_POST);
        $this->redirect('/admin/buses', 'success', 'Đã thêm xe.');
    }

    public function edit(): void
    {
        $bus = $this->buses->find($this->queryInt('id'));
        if ($bus === null) {
            $this->redirect('/admin/buses', 'error', 'Không tìm thấy xe.');
        }

        $this->view('admin.buses.edit', [
            'title' => 'Sua xe',
            'bus' => $bus,
        ], 'admin');
    }

    public function update(): void
    {
        $id = $this->postInt('id');
        if ($error = $this->requireFields([
            'name' => 'tên xe',
            'license_plate' => 'biển số',
            'total_seats' => 'tổng số ghế',
        ])) {
            $this->redirect('/admin/buses/edit?id=' . $id, 'error', $error);
        }
        if ($error = $this->validatePayload()) {
            $this->redirect('/admin/buses/edit?id=' . $id, 'error', $error);
        }

        $this->buses->update($id, $_POST);
        $this->redirect('/admin/buses', 'success', 'Đã cập nhật xe.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->buses->isUsed($id)) {
            $this->redirect('/admin/buses', 'error', 'Không thể xóa xe đang có ghế hoặc chuyến xe.');
        }

        $this->buses->delete($id);
        $this->redirect('/admin/buses', 'success', 'Đã xóa xe.');
    }

    private function validatePayload(): ?string
    {
        return $this->requireInteger('total_seats', 'Tổng số ghế', 1)
            ?? $this->requireAllowed('bus_type', 'Loại xe', ['standard', 'sleeper', 'limousine'])
            ?? $this->requireAllowed('status', 'Trạng thái', ['active', 'maintenance', 'inactive']);
    }
}
