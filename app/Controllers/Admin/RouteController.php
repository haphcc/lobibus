<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Location;
use App\Models\Route as RouteModel;

final class RouteController extends AdminController
{
    private RouteModel $routes;

    public function __construct()
    {
        $this->routes = new RouteModel();
    }

    public function index(): void
    {
        $this->view('admin.routes.index', [
            'title' => 'Quan ly tuyen',
            'routes' => $this->routes->allWithLocations(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.routes.create', [
            'title' => 'Them tuyen',
            'locations' => (new Location())->all(),
        ], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields([
            'from_location_id' => 'điểm đi',
            'to_location_id' => 'điểm đến',
        ])) {
            $this->redirect('/admin/routes/create', 'error', $error);
        }
        if ($this->postInt('from_location_id') === $this->postInt('to_location_id')) {
            $this->redirect('/admin/routes/create', 'error', 'Điểm đi và điểm đến phải khác nhau.');
        }
        if ($error = $this->validatePayload()) {
            $this->redirect('/admin/routes/create', 'error', $error);
        }

        $this->routes->create($_POST);
        $this->redirect('/admin/routes', 'success', 'Đã thêm tuyến.');
    }

    public function edit(): void
    {
        $route = $this->routes->find($this->queryInt('id'));
        if ($route === null) {
            $this->redirect('/admin/routes', 'error', 'Không tìm thấy tuyến.');
        }

        $this->view('admin.routes.edit', [
            'title' => 'Sua tuyen',
            'route' => $route,
            'locations' => (new Location())->all(),
        ], 'admin');
    }

    public function update(): void
    {
        $id = $this->postInt('id');
        if ($this->postInt('from_location_id') === $this->postInt('to_location_id')) {
            $this->redirect('/admin/routes/edit?id=' . $id, 'error', 'Điểm đi và điểm đến phải khác nhau.');
        }
        if ($error = $this->validatePayload()) {
            $this->redirect('/admin/routes/edit?id=' . $id, 'error', $error);
        }

        $this->routes->update($id, $_POST);
        $this->redirect('/admin/routes', 'success', 'Đã cập nhật tuyến.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->routes->isUsed($id)) {
            $this->redirect('/admin/routes', 'error', 'Không thể xóa tuyến đang được dùng trong chuyến xe.');
        }

        $this->routes->delete($id);
        $this->redirect('/admin/routes', 'success', 'Đã xóa tuyến.');
    }

    private function validatePayload(): ?string
    {
        return $this->requireOptionalNumber('distance_km', 'Quãng đường', 0)
            ?? $this->requireOptionalNumber('duration_minutes', 'Thời gian dự kiến', 1)
            ?? $this->requireAllowed('status', 'Trạng thái', ['active', 'inactive']);
    }
}
