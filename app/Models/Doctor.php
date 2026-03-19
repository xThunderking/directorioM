<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Doctor
{
    public function __construct(private readonly PDO $connection)
    {
    }

    /**
     * @return array<int, array{id: int, nombre: string}>
     */
    public function searchSpecialties(string $query = '', int $limit = 8): array
    {
        $sql = 'SELECT id, nombre FROM especialidades';
        $params = [];

        if ($query !== '') {
            $sql .= ' WHERE nombre LIKE :query';
            $params['query'] = '%' . $query . '%';
        }

        $sql .= ' ORDER BY nombre ASC LIMIT :limit';

        $statement = $this->connection->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function search(string $query = ''): array
    {
        $sql = 'SELECT d.id, d.nombre_completo, d.telefono, d.id_especialidad, e.nombre AS especialidad '
            . 'FROM doctores d '
            . 'INNER JOIN especialidades e ON e.id = d.id_especialidad '
            . 'WHERE 1=1';
        $params = [];

        if ($query !== '') {
            $sql .= ' AND (d.nombre_completo LIKE :query OR d.telefono LIKE :query OR e.nombre LIKE :query)';
            $params['query'] = '%' . $query . '%';
        }

        $sql .= ' ORDER BY e.nombre ASC, d.nombre_completo ASC';

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function create(string $nombreCompleto, string $telefono, int $specialtyId): bool
    {
        $insertDoctor = $this->connection->prepare(
            'INSERT INTO doctores (nombre_completo, telefono, id_especialidad) '
            . 'VALUES (:nombre_completo, :telefono, :id_especialidad)'
        );

        return $insertDoctor->execute([
            'nombre_completo' => $nombreCompleto,
            'telefono' => $telefono,
            'id_especialidad' => $specialtyId,
        ]);
    }

    public function update(int $doctorId, string $nombreCompleto, string $telefono, int $specialtyId): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE doctores '
            . 'SET nombre_completo = :nombre_completo, telefono = :telefono, id_especialidad = :id_especialidad '
            . 'WHERE id = :id'
        );

        return $statement->execute([
            'id' => $doctorId,
            'nombre_completo' => $nombreCompleto,
            'telefono' => $telefono,
            'id_especialidad' => $specialtyId,
        ]);
    }

    public function delete(int $doctorId): bool
    {
        $statement = $this->connection->prepare('DELETE FROM doctores WHERE id = :id');

        return $statement->execute(['id' => $doctorId]);
    }

    public function doctorExists(int $doctorId): bool
    {
        $statement = $this->connection->prepare('SELECT 1 FROM doctores WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $doctorId]);

        return (bool) $statement->fetchColumn();
    }

    public function specialtyExists(int $specialtyId): bool
    {
        $statement = $this->connection->prepare('SELECT 1 FROM especialidades WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $specialtyId]);

        return (bool) $statement->fetchColumn();
    }

    public function specialtyNameExists(string $specialtyName): bool
    {
        $statement = $this->connection->prepare('SELECT 1 FROM especialidades WHERE nombre = :nombre LIMIT 1');
        $statement->execute(['nombre' => $specialtyName]);

        return (bool) $statement->fetchColumn();
    }

    public function createSpecialty(string $specialtyName): bool
    {
        $statement = $this->connection->prepare('INSERT INTO especialidades (nombre) VALUES (:nombre)');

        return $statement->execute(['nombre' => $specialtyName]);
    }

    public function findSpecialtyIdByName(string $specialtyName): int
    {
        $statement = $this->connection->prepare('SELECT id FROM especialidades WHERE LOWER(nombre) = LOWER(:nombre) LIMIT 1');
        $statement->execute(['nombre' => trim($specialtyName)]);

        $result = $statement->fetchColumn();

        return $result === false ? 0 : (int) $result;
    }
}
