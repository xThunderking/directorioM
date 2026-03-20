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

        if ($format === 'excel') {
            $this->exportExcel($query);
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

    private function exportExcel(string $query): void
    {
        $doctors = $this->doctorModel->search($query);

        $fileName = 'directorio_doctores_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";

        echo '<html><head><meta charset="UTF-8"></head><body>';
        echo '<table border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-family:Segoe UI,Arial,sans-serif;font-size:12px;">';
        echo '<tr>';
        echo '<td colspan="3" style="background:#12577a;color:#ffffff;font-size:16px;font-weight:700;padding:12px 14px;">Directorio Medico - Reporte de Doctores</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td style="background:#0f7f89;color:#ffffff;font-weight:700;padding:8px 10px;">Nombre Completo</td>';
        echo '<td style="background:#0f7f89;color:#ffffff;font-weight:700;padding:8px 10px;">Telefono</td>';
        echo '<td style="background:#0f7f89;color:#ffffff;font-weight:700;padding:8px 10px;">Especialidad</td>';
        echo '</tr>';

        if (empty($doctors)) {
            echo '<tr><td colspan="3" style="padding:10px;text-align:center;color:#6b7280;">No hay resultados para exportar.</td></tr>';
        } else {
            $palette = ['#ffc9c9', '#ffd7b3', '#ffe7a3', '#c7f2d0', '#bff5eb', '#c9dcff', '#e0ccff', '#ffcde7'];
            $paletteAccent = ['#b4233f', '#b35316', '#9f6a08', '#1e7b42', '#0f7f72', '#3056b5', '#6a3fb5', '#b03b78'];
            $lastSpecialty = '';

            foreach ($doctors as $doctor) {
                $specialty = (string) $doctor['especialidad'];

                if ($specialty !== $lastSpecialty) {
                    $colorIndex = abs(crc32($specialty)) % count($palette);
                    $groupColor = $palette[$colorIndex];
                    $accentColor = $paletteAccent[$colorIndex];

                    echo '<tr>';
                    echo '<td colspan="3" style="background:' . htmlspecialchars($groupColor, ENT_QUOTES, 'UTF-8') . ';color:' . htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') . ';font-weight:800;padding:8px 10px;text-transform:uppercase;letter-spacing:.5px;border-top:2px solid ' . htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') . ';border-bottom:2px solid ' . htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') . ';">' . htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '</tr>';

                    $lastSpecialty = $specialty;
                }

                $rowColorIndex = abs(crc32($specialty)) % count($palette);
                $rowColor = $palette[$rowColorIndex];
                $rowAccent = $paletteAccent[$rowColorIndex];

                echo '<tr>';
                echo '<td style="background:' . htmlspecialchars($rowColor, ENT_QUOTES, 'UTF-8') . ';padding:7px 10px;">' . htmlspecialchars((string) $doctor['nombre_completo'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td style="background:' . htmlspecialchars($rowColor, ENT_QUOTES, 'UTF-8') . ';padding:7px 10px;font-weight:800;font-size:13px;color:' . htmlspecialchars($rowAccent, ENT_QUOTES, 'UTF-8') . ';">' . htmlspecialchars((string) $doctor['telefono'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td style="background:' . htmlspecialchars($rowColor, ENT_QUOTES, 'UTF-8') . ';padding:7px 10px;color:#2b3340;">' . htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '</tr>';
            }
        }

        echo '</table></body></html>';
        exit;
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
