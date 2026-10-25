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
            'route_id' => 'Route',
            'bus_id' => 'Bus',
            'departure_time' => 'Departure time',
            'arrival_time' => 'Arrival time',
            'price' => 'Price',
        ])) {
            $this->redirect('/admin/trips/create', 'error', $error);
        }

        $this->trips->create($this->normalizedPost());
        $this->redirect('/admin/trips', 'success', 'Trip created.');
    }

    public function edit(): void
    {
        $trip = $this->trips->find($this->queryInt('id'));
        if ($trip === null) {
            $this->redirect('/admin/trips', 'error', 'Trip not found.');
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
            'route_id' => 'Route',
            'bus_id' => 'Bus',
            'departure_time' => 'Departure time',
            'arrival_time' => 'Arrival time',
            'price' => 'Price',
        ])) {
            $this->redirect('/admin/trips/edit?id=' . $id, 'error', $error);
        }

        $this->trips->update($id, $this->normalizedPost());
        $this->redirect('/admin/trips', 'success', 'Trip updated.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->trips->isBooked($id)) {
            $this->redirect('/admin/trips', 'error', 'Cannot delete a trip that has bookings.');
        }

        $this->trips->delete($id);
        $this->redirect('/admin/trips', 'success', 'Trip deleted.');
    }

    private function normalizedPost(): array
    {
        $data = $_POST;
        $data['departure_time'] = str_replace('T', ' ', $this->postString('departure_time')) . ':00';
        $data['arrival_time'] = str_replace('T', ' ', $this->postString('arrival_time')) . ':00';
        return $data;
    }
}
