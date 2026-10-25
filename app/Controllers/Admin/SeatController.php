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
            'title' => 'Quan ly ghe',
            'buses' => $buses,
            'selectedBus' => $selectedBus,
            'seats' => $selectedBusId > 0 ? $this->seats->getByBus($selectedBusId) : [],
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.seats.create', [
            'title' => 'Them ghe',
            'buses' => (new Bus())->all(),
            'busId' => $this->queryInt('bus_id'),
        ], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields([
            'bus_id' => 'Bus',
            'seat_number' => 'Seat number',
        ])) {
            $this->redirect('/admin/seats/create', 'error', $error);
        }

        $this->seats->create($_POST);
        $this->redirect('/admin/seats?bus_id=' . $this->postInt('bus_id'), 'success', 'Seat created.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        $seat = $this->seats->find($id);
        $busId = (int) ($seat['bus_id'] ?? 0);

        if ($this->seats->isBooked($id)) {
            $this->redirect('/admin/seats?bus_id=' . $busId, 'error', 'Cannot delete a booked seat.');
        }

        $this->seats->delete($id);
        $this->redirect('/admin/seats?bus_id=' . $busId, 'success', 'Seat deleted.');
    }
}
