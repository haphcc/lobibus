<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Bus;
use App\Models\Seat;

final class SeatController extends AdminController
{
    private Seat $seats;

    public function __construct()
    {
        $this->seats = new Seat();
    }

    public function index(): void
    {
        $busId = $this->queryInt('bus_id');
        $buses = (new Bus())->all();
        $selectedBus = $busId > 0 ? (new Bus())->find($busId) : ($buses[0] ?? null);
        $selectedBusId = (int) ($selectedBus['id'] ?? 0);

        $this->view('admin.seats.index', [
            'title' => 'Quản lý ghế',
            'buses' => $buses,
            'selectedBus' => $selectedBus,
            'seats' => $selectedBusId > 0 ? $this->seats->getByBus($selectedBusId) : [],
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.seats.create', [
            'title' => 'Thêm ghế',
            'buses' => (new Bus())->all(),
            'busId' => $this->queryInt('bus_id'),
        ], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields([
            'bus_id' => 'xe',
            'seat_number' => 'số ghế',
        ])) {
            $this->redirect('/admin/seats/create', 'error', $error);
        }
        if ($error = $this->requireInteger('bus_id', 'Xe', 1)
            ?? $this->requireAllowed('seat_type', 'Loại ghế', ['standard', 'sleeper', 'vip'])) {
            $this->redirect('/admin/seats/create', 'error', $error);
        }

        $this->seats->create($_POST);
        $this->redirect('/admin/seats?bus_id=' . $this->postInt('bus_id'), 'success', 'Đã thêm ghế.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        $seat = $this->seats->find($id);
        $busId = (int) ($seat['bus_id'] ?? 0);

        if ($this->seats->isBooked($id)) {
            $this->redirect('/admin/seats?bus_id=' . $busId, 'error', 'Không thể xóa ghế đã có đơn đặt vé.');
        }

        $this->seats->delete($id);
        $this->redirect('/admin/seats?bus_id=' . $busId, 'success', 'Đã xóa ghế.');
    }
}
