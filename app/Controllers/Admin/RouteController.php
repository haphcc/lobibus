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
            'from_location_id' => 'From location',
            'to_location_id' => 'To location',
        ])) {
            $this->redirect('/admin/routes/create', 'error', $error);
        }
        if ($this->postInt('from_location_id') === $this->postInt('to_location_id')) {
            $this->redirect('/admin/routes/create', 'error', 'From and to locations must be different.');
        }

        $this->routes->create($_POST);
        $this->redirect('/admin/routes', 'success', 'Route created.');
    }

    public function edit(): void
    {
        $route = $this->routes->find($this->queryInt('id'));
        if ($route === null) {
            $this->redirect('/admin/routes', 'error', 'Route not found.');
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
            $this->redirect('/admin/routes/edit?id=' . $id, 'error', 'From and to locations must be different.');
        }

        $this->routes->update($id, $_POST);
        $this->redirect('/admin/routes', 'success', 'Route updated.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->routes->isUsed($id)) {
            $this->redirect('/admin/routes', 'error', 'Cannot delete a route used by trips.');
        }

        $this->routes->delete($id);
        $this->redirect('/admin/routes', 'success', 'Route deleted.');
    }
}
