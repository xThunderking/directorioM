# Directorio Medico

Proyecto web en PHP con arquitectura MVC para buscar doctores por nombre y especialidad.

## Stack

- PHP 8+
- MySQL / MariaDB
- HTML5
- Bootstrap 5
- CSS3

## Estructura

```
directorioM/
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Core/
│   ├── Models/
│   └── Views/
├── database/
│   └── schema.sql
├── public/
│   ├── assets/
│   ├── .htaccess
│   └── index.php
└── .htaccess
```

## Instalacion en XAMPP

1. Copia el proyecto en `htdocs`:
	- `c:/xampp/htdocs/directorioM`
2. Inicia Apache y MySQL en XAMPP.
3. Crea la base de datos y tabla ejecutando:
	- `database/schema.sql`
4. Verifica credenciales en:
	- `app/Config/config.php`

## URL de acceso

- `http://localhost/directorioM/public`

Si tienes mod_rewrite habilitado, tambien puedes usar:

- `http://localhost/directorioM/`

## Funcionalidades

- Busqueda por nombre completo
- Filtro por especialidad
- Tabla responsive con Bootstrap
- Consultas preparadas con PDO para seguridad

## SQL incluido

El script `database/schema.sql` incluye:

- Creacion de la base de datos `directorio_medico`
- Creacion de la tabla `doctores`
- Datos de ejemplo para pruebas
