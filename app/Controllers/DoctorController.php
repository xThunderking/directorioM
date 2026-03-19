<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Doctor;

class DoctorController extends Controller
{
    public function __construct(private readonly Doctor $doctorModel, private readonly array $config)
    {
    }

    public function index(): void
    {
        $query = trim($_GET['q'] ?? '');
        $format = trim($_GET['format'] ?? '');

        if ($format === 'json') {
            $this->searchJson($query);
            return;
        }

        if ($format === 'specialties') {
            $this->specialtiesJson($query);
            return;
        }

        $doctors = $this->doctorModel->search($query);

        $status = trim($_GET['status'] ?? '');
        $error = trim($_GET['error'] ?? '');

        $this->view('doctors/index', [
            'title' => 'Buscar Doctores',
            'appName' => $this->config['app_name'] ?? 'Directorio Medico',
            'baseUrl' => $this->config['base_url'] ?? '',
            'doctors' => $doctors,
            'query' => $query,
            'status' => $status,
            'error' => $error,
        ]);
    }

    public function store(): void
    {
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $specialtyId = (int) ($_POST['id_especialidad'] ?? 0);

        if ($nombreCompleto === '' || $telefono === '' || $specialtyId <= 0) {
            $this->redirectWithQuery(['error' => 'Todos los campos son obligatorios']);
            return;
        }

        if (!$this->doctorModel->specialtyExists($specialtyId)) {
            $this->redirectWithQuery(['error' => 'Debes seleccionar una especialidad valida de la lista']);
            return;
        }

        $this->doctorModel->create($nombreCompleto, $telefono, $specialtyId);
        $this->redirectWithQuery(['status' => 'created']);
    }

    public function storeSpecialty(): void
    {
        $specialtyName = trim($_POST['nombre_especialidad'] ?? '');

        if ($specialtyName === '') {
            $this->redirectWithQuery(['error' => 'El nombre de la especialidad es obligatorio']);
            return;
        }

        if ($this->doctorModel->specialtyNameExists($specialtyName)) {
            $this->redirectWithQuery(['error' => 'Esa especialidad ya existe']);
            return;
        }

        $this->doctorModel->createSpecialty($specialtyName);
        $this->redirectWithQuery(['status' => 'specialty_created']);
    }

    public function update(): void
    {
        $doctorId = (int) ($_POST['doctor_id'] ?? 0);
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $specialtyId = (int) ($_POST['id_especialidad'] ?? 0);

        if ($doctorId <= 0 || $nombreCompleto === '' || $telefono === '' || $specialtyId <= 0) {
            $this->redirectWithQuery(['error' => 'Datos invalidos para editar el doctor']);
            return;
        }

        if (!$this->doctorModel->doctorExists($doctorId)) {
            $this->redirectWithQuery(['error' => 'El doctor seleccionado no existe']);
            return;
        }

        if (!$this->doctorModel->specialtyExists($specialtyId)) {
            $this->redirectWithQuery(['error' => 'Debes seleccionar una especialidad valida de la lista']);
            return;
        }

        $this->doctorModel->update($doctorId, $nombreCompleto, $telefono, $specialtyId);
        $this->redirectWithQuery(['status' => 'updated']);
    }

    private function searchJson(string $query): void
    {
        $doctors = $this->doctorModel->search($query);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['doctors' => $doctors], JSON_UNESCAPED_UNICODE);
    }

    private function specialtiesJson(string $query): void
    {
        $specialties = $this->doctorModel->searchSpecialties($query);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['specialties' => $specialties], JSON_UNESCAPED_UNICODE);
    }

    private function redirectWithQuery(array $params): void
    {
        $baseUrl = $this->config['base_url'] ?? '/';
        $queryString = http_build_query($params);
        $target = $queryString !== '' ? $baseUrl . '?' . $queryString : $baseUrl;

        header('Location: ' . $target);
        exit;
    }
}
