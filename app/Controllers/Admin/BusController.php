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
            'name' => 'Name',
            'license_plate' => 'License plate',
            'total_seats' => 'Total seats',
        ])) {
            $this->redirect('/admin/buses/create', 'error', $error);
        }

        $this->buses->create($_POST);
        $this->redirect('/admin/buses', 'success', 'Bus created.');
    }

    public function edit(): void
    {
        $bus = $this->buses->find($this->queryInt('id'));
        if ($bus === null) {
            $this->redirect('/admin/buses', 'error', 'Bus not found.');
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
            'name' => 'Name',
            'license_plate' => 'License plate',
            'total_seats' => 'Total seats',
        ])) {
            $this->redirect('/admin/buses/edit?id=' . $id, 'error', $error);
        }

        $this->buses->update($id, $_POST);
        $this->redirect('/admin/buses', 'success', 'Bus updated.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->buses->isUsed($id)) {
            $this->redirect('/admin/buses', 'error', 'Cannot delete a bus used by seats or trips.');
        }

        $this->buses->delete($id);
        $this->redirect('/admin/buses', 'success', 'Bus deleted.');
    }
}
