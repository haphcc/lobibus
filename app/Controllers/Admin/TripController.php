<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Bus;
use App\Models\Route as RouteModel;
use App\Models\Trip;

final class TripController extends AdminController
{
    private Trip $trips;

    public function __construct()
    {
        $this->trips = new Trip();
    }

    public function index(): void
    {
        $this->view('admin.trips.index', [
            'title' => 'Quan ly chuyen',
            'trips' => $this->trips->allWithDetails(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.trips.create', [
            'title' => 'Them chuyen',
            'routes' => (new RouteModel())->allWithLocations(),
            'buses' => (new Bus())->all(),
        ], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields([
            'route_id' => 'tuyến',
            'bus_id' => 'xe',
            'departure_time' => 'giờ khởi hành',
            'arrival_time' => 'giờ đến',
            'price' => 'giá vé',
        ])) {
            $this->redirect('/admin/trips/create', 'error', $error);
        }
        if ($error = $this->validatePayload()) {
            $this->redirect('/admin/trips/create', 'error', $error);
        }

        $this->trips->create($this->normalizedPost());
        $this->redirect('/admin/trips', 'success', 'Đã thêm chuyến.');
    }

    public function edit(): void
    {
        $trip = $this->trips->find($this->queryInt('id'));
        if ($trip === null) {
            $this->redirect('/admin/trips', 'error', 'Không tìm thấy chuyến.');
        }

        $this->view('admin.trips.edit', [
            'title' => 'Sua chuyen',
            'trip' => $trip,
            'routes' => (new RouteModel())->allWithLocations(),
            'buses' => (new Bus())->all(),
        ], 'admin');
    }

    public function update(): void
    {
        $id = $this->postInt('id');
        if ($error = $this->requireFields([
            'route_id' => 'tuyến',
            'bus_id' => 'xe',
            'departure_time' => 'giờ khởi hành',
            'arrival_time' => 'giờ đến',
            'price' => 'giá vé',
        ])) {
            $this->redirect('/admin/trips/edit?id=' . $id, 'error', $error);
        }
        if ($error = $this->validatePayload()) {
            $this->redirect('/admin/trips/edit?id=' . $id, 'error', $error);
        }

        $this->trips->update($id, $this->normalizedPost());
        $this->redirect('/admin/trips', 'success', 'Đã cập nhật chuyến.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->trips->isBooked($id)) {
            $this->redirect('/admin/trips', 'error', 'Không thể xóa chuyến đã có đơn đặt vé.');
        }

        $this->trips->delete($id);
        $this->redirect('/admin/trips', 'success', 'Đã xóa chuyến.');
    }

    private function normalizedPost(): array
    {
        $data = $_POST;
        $data['departure_time'] = str_replace('T', ' ', $this->postString('departure_time')) . ':00';
        $data['arrival_time'] = str_replace('T', ' ', $this->postString('arrival_time')) . ':00';
        return $data;
    }

    private function validatePayload(): ?string
    {
        $dateError = $this->requireDateTime('departure_time', 'Giờ khởi hành')
            ?? $this->requireDateTime('arrival_time', 'Giờ đến');
        if ($dateError !== null) {
            return $dateError;
        }

        $departure = new \DateTimeImmutable($this->postString('departure_time'));
        $arrival = new \DateTimeImmutable($this->postString('arrival_time'));
        if ($arrival <= $departure) {
            return 'Giờ đến phải sau giờ khởi hành.';
        }

        return $this->requireInteger('route_id', 'Tuyến', 1)
            ?? $this->requireInteger('bus_id', 'Xe', 1)
            ?? $this->requireNumber('price', 'Giá vé', 0)
            ?? $this->requireAllowed('status', 'Trạng thái', ['scheduled', 'running', 'completed', 'cancelled']);
    }
}
