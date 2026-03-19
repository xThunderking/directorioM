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
        $specialtyText = trim($_POST['especialidad_texto'] ?? '');

        if ($specialtyId <= 0 && $specialtyText !== '') {
            $specialtyId = $this->doctorModel->findSpecialtyIdByName($specialtyText);
        }

        if ($nombreCompleto === '' || $telefono === '' || $specialtyId <= 0) {
            $this->redirectWithQuery(['error' => 'Todos los campos son obligatorios']);
            return;
        }

        if (!$this->doctorModel->specialtyExists($specialtyId)) {
            $this->redirectWithQuery(['error' => 'Debes seleccionar una especialidad valida de la lista']);
            return;
        }

        try {
            $saved = $this->doctorModel->create($nombreCompleto, $telefono, $specialtyId);

            if (!$saved) {
                $this->redirectWithQuery(['error' => 'No se pudo guardar el doctor']);
                return;
            }

            $this->redirectWithQuery(['status' => 'created']);
        } catch (\Throwable $exception) {
            $this->redirectWithQuery(['error' => 'Error al guardar doctor: ' . $exception->getMessage()]);
        }
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

        try {
            $saved = $this->doctorModel->createSpecialty($specialtyName);

            if (!$saved) {
                $this->redirectWithQuery(['error' => 'No se pudo guardar la especialidad']);
                return;
            }

            $this->redirectWithQuery(['status' => 'specialty_created']);
        } catch (\Throwable $exception) {
            $this->redirectWithQuery(['error' => 'Error al guardar especialidad: ' . $exception->getMessage()]);
        }
    }

    public function update(): void
    {
        $doctorId = (int) ($_POST['doctor_id'] ?? 0);
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $specialtyId = (int) ($_POST['id_especialidad'] ?? 0);
        $specialtyText = trim($_POST['especialidad_texto'] ?? '');

        if ($specialtyId <= 0 && $specialtyText !== '') {
            $specialtyId = $this->doctorModel->findSpecialtyIdByName($specialtyText);
        }

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

        try {
            $saved = $this->doctorModel->update($doctorId, $nombreCompleto, $telefono, $specialtyId);

            if (!$saved) {
                $this->redirectWithQuery(['error' => 'No se pudo actualizar el doctor']);
                return;
            }

            $this->redirectWithQuery(['status' => 'updated']);
        } catch (\Throwable $exception) {
            $this->redirectWithQuery(['error' => 'Error al actualizar doctor: ' . $exception->getMessage()]);
        }
    }

    public function delete(): void
    {
        $doctorId = (int) ($_POST['doctor_id'] ?? 0);

        if ($doctorId <= 0) {
            $this->redirectWithQuery(['error' => 'Doctor invalido para eliminar']);
            return;
        }

        if (!$this->doctorModel->doctorExists($doctorId)) {
            $this->redirectWithQuery(['error' => 'El doctor seleccionado no existe']);
            return;
        }

        try {
            $deleted = $this->doctorModel->delete($doctorId);

            if (!$deleted) {
                $this->redirectWithQuery(['error' => 'No se pudo eliminar el doctor']);
                return;
            }

            $this->redirectWithQuery(['status' => 'deleted']);
        } catch (\Throwable $exception) {
            $this->redirectWithQuery(['error' => 'Error al eliminar doctor: ' . $exception->getMessage()]);
        }
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
        $requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $baseUrl = is_string($requestPath) && $requestPath !== '' ? $requestPath : ($this->config['base_url'] ?? '/');
        $queryString = http_build_query($params);
        $target = $queryString !== '' ? $baseUrl . '?' . $queryString : $baseUrl;

        header('Location: ' . $target);
        exit;
    }
}
