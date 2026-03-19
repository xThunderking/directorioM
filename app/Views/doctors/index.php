<?php
$toneCount = 8;
$getToneClass = static function (string $specialty) use ($toneCount): string {
    $hash = abs(crc32($specialty));
    return 'specialty-tone-' . (($hash % $toneCount) + 1);
};
?>

<?php if (($status ?? '') === 'created'): ?>
    <div class="alert alert-success shadow-sm border-0 app-alert" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>Doctor agregado correctamente.
    </div>
<?php endif; ?>

<?php if (($status ?? '') === 'specialty_created'): ?>
    <div class="alert alert-success shadow-sm border-0 app-alert" role="alert">
        <i class="bi bi-stars me-2"></i>Especialidad agregada correctamente.
    </div>
<?php endif; ?>

<?php if (($status ?? '') === 'updated'): ?>
    <div class="alert alert-success shadow-sm border-0 app-alert" role="alert">
        <i class="bi bi-pencil-square me-2"></i>Doctor actualizado correctamente.
    </div>
<?php endif; ?>

<?php if (($error ?? '') !== ''): ?>
    <div class="alert alert-danger shadow-sm border-0 app-alert" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<section class="card border-0 search-card mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-end">
            <div class="flex-grow-1">
                <label for="doctorSearch" class="form-label search-label">Busqueda rapida</label>
                <div class="input-group input-group-lg shadow-sm search-input-wrap">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input
                        type="text"
                        class="form-control border-start-0"
                        id="doctorSearch"
                        value="<?= htmlspecialchars((string) ($query ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Nombre, telefono o especialidad..."
                        autocomplete="off"
                    >
                    <button type="button" class="btn btn-outline-secondary" id="clearSearchBtn" title="Limpiar busqueda">
                        <i class="bi bi-x-circle me-1"></i>Limpiar
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Resultados en tiempo real mientras escribes.</small>
            </div>
            <div class="d-grid d-md-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#addSpecialtyModal">
                    <i class="bi bi-tags-fill me-2"></i>Agregar Especialidad
                </button>
                <button type="button" class="btn btn-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                    <i class="bi bi-person-plus-fill me-2"></i>Agregar Doctor
                </button>
            </div>
        </div>

        <div class="result-meta mt-4">
            <span class="result-chip">
                <i class="bi bi-people-fill me-1"></i>
                <strong id="resultCount"><?= count($doctors) ?></strong> doctores visibles
            </span>
            <span class="result-chip muted-chip">
                <i class="bi bi-funnel-fill me-1"></i>Ordenados por especialidad
            </span>
        </div>
    </div>
</section>

<section class="card border-0 directory-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 directory-table">
                <thead>
                    <tr>
                        <th scope="col">Nombre completo</th>
                        <th scope="col">Telefono</th>
                        <th scope="col">Especialidad</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="doctorsTableBody">
                <?php if (empty($doctors)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">No hay resultados para esta busqueda.</td>
                    </tr>
                <?php else: ?>
                    <?php $lastSpecialty = null; ?>
                    <?php foreach ($doctors as $doctor): ?>
                        <?php $specialty = (string) $doctor['especialidad']; ?>
                        <?php if ($specialty !== $lastSpecialty): ?>
                            <?php $toneClass = $getToneClass($specialty); ?>
                            <tr class="specialty-group-row <?= htmlspecialchars($toneClass, ENT_QUOTES, 'UTF-8') ?>">
                                <td colspan="4">
                                    <span class="specialty-pill <?= htmlspecialchars($toneClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="bi bi-bookmark-star-fill me-2"></i><?= htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                            </tr>
                            <?php $lastSpecialty = $specialty; ?>
                        <?php endif; ?>

                        <tr class="doctor-row <?= htmlspecialchars($toneClass, ENT_QUOTES, 'UTF-8') ?>">
                            <td class="fw-semibold"><?= htmlspecialchars((string) $doctor['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="phone-cell">
                                <span class="phone-chip phone-emphasis"><i class="bi bi-telephone-fill me-2"></i><?= htmlspecialchars((string) $doctor['telefono'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td>
                                <span class="text-muted"><?= htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="text-center">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary edit-doctor-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editDoctorModal"
                                    data-doctor-id="<?= htmlspecialchars((string) $doctor['id'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-doctor-name="<?= htmlspecialchars((string) $doctor['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-doctor-phone="<?= htmlspecialchars((string) $doctor['telefono'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-specialty-id="<?= htmlspecialchars((string) $doctor['id_especialidad'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-specialty-name="<?= htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <i class="bi bi-pencil-square me-1"></i>Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="modal fade" id="addDoctorModal" tabindex="-1" aria-labelledby="addDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-glass">
            <div class="modal-header border-0 pb-0 modal-header-fancy">
                <h5 class="modal-title" id="addDoctorModalLabel"><i class="bi bi-person-vcard-fill me-2"></i>Agregar Doctor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" action="<?= htmlspecialchars((string) ($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>" id="addDoctorForm">
                <div class="modal-body pt-3">
                    <input type="hidden" name="action" value="store">
                    <input type="hidden" name="id_especialidad" id="id_especialidad" value="">
                    <div class="mb-3">
                        <label for="nombre_completo" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Telefono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <div class="position-relative">
                        <label for="especialidad_busqueda" class="form-label">Especialidad</label>
                        <input
                            type="text"
                            class="form-control"
                            id="especialidad_busqueda"
                            placeholder="Escribe para buscar especialidad..."
                            autocomplete="off"
                            required
                        >
                        <div class="specialty-suggestions shadow-sm" id="specialtySuggestions" role="listbox" aria-label="Sugerencias de especialidad"></div>
                        <small class="text-muted d-block mt-2">Selecciona una especialidad de la lista sugerida.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editDoctorModal" tabindex="-1" aria-labelledby="editDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-glass">
            <div class="modal-header border-0 pb-0 modal-header-fancy">
                <h5 class="modal-title" id="editDoctorModalLabel"><i class="bi bi-pencil-square me-2"></i>Editar Doctor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" action="<?= htmlspecialchars((string) ($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>" id="editDoctorForm">
                <div class="modal-body pt-3">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="doctor_id" id="edit_doctor_id" value="">
                    <input type="hidden" name="id_especialidad" id="edit_id_especialidad" value="">

                    <div class="mb-3">
                        <label for="edit_nombre_completo" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" id="edit_nombre_completo" name="nombre_completo" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_telefono" class="form-label">Telefono</label>
                        <input type="text" class="form-control" id="edit_telefono" name="telefono" required>
                    </div>
                    <div class="position-relative">
                        <label for="edit_especialidad_busqueda" class="form-label">Especialidad</label>
                        <input
                            type="text"
                            class="form-control"
                            id="edit_especialidad_busqueda"
                            placeholder="Escribe para buscar especialidad..."
                            autocomplete="off"
                            required
                        >
                        <div class="specialty-suggestions shadow-sm" id="editSpecialtySuggestions" role="listbox" aria-label="Sugerencias para editar especialidad"></div>
                        <small class="text-muted d-block mt-2">Debes elegir una especialidad de la lista.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addSpecialtyModal" tabindex="-1" aria-labelledby="addSpecialtyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-glass">
            <div class="modal-header border-0 pb-0 modal-header-fancy secondary">
                <h5 class="modal-title" id="addSpecialtyModalLabel"><i class="bi bi-tags-fill me-2"></i>Agregar Especialidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" action="<?= htmlspecialchars((string) ($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>" id="addSpecialtyForm">
                <div class="modal-body pt-3">
                    <input type="hidden" name="action" value="store_specialty">
                    <label for="nombre_especialidad" class="form-label">Nombre de la especialidad</label>
                    <input
                        type="text"
                        class="form-control form-control-lg"
                        id="nombre_especialidad"
                        name="nombre_especialidad"
                        placeholder="Ej. Medicina del Deporte"
                        required
                    >
                    <small class="text-muted d-block mt-2">Esta especialidad quedara disponible para seleccionar en el modal de doctores.</small>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle-fill me-2"></i>Guardar Especialidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    const searchInput = document.getElementById('doctorSearch');
    const tableBody = document.getElementById('doctorsTableBody');
    const resultCount = document.getElementById('resultCount');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const specialtyInput = document.getElementById('especialidad_busqueda');
    const specialtyIdInput = document.getElementById('id_especialidad');
    const specialtySuggestions = document.getElementById('specialtySuggestions');
    const addDoctorForm = document.getElementById('addDoctorForm');
    const editDoctorForm = document.getElementById('editDoctorForm');
    const editDoctorIdInput = document.getElementById('edit_doctor_id');
    const editDoctorNameInput = document.getElementById('edit_nombre_completo');
    const editDoctorPhoneInput = document.getElementById('edit_telefono');
    const editSpecialtyInput = document.getElementById('edit_especialidad_busqueda');
    const editSpecialtyIdInput = document.getElementById('edit_id_especialidad');
    const editSpecialtySuggestions = document.getElementById('editSpecialtySuggestions');
    const addSpecialtyForm = document.getElementById('addSpecialtyForm');
    const specialtyNameInput = document.getElementById('nombre_especialidad');
    const baseUrl = <?= json_encode((string) ($baseUrl ?? ''), JSON_UNESCAPED_UNICODE) ?>;
    const toneCount = 8;

    if (!searchInput || !tableBody || !baseUrl || !resultCount || !clearSearchBtn || !specialtyInput || !specialtyIdInput || !specialtySuggestions || !addDoctorForm || !editDoctorForm || !editDoctorIdInput || !editDoctorNameInput || !editDoctorPhoneInput || !editSpecialtyInput || !editSpecialtyIdInput || !editSpecialtySuggestions || !addSpecialtyForm || !specialtyNameInput) {
        return;
    }

    const escapeHtml = (text) => {
        return text
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const hashTone = (value) => {
        let hash = 0;
        for (let index = 0; index < value.length; index += 1) {
            hash = ((hash << 5) - hash) + value.charCodeAt(index);
            hash |= 0;
        }
        return `specialty-tone-${(Math.abs(hash) % toneCount) + 1}`;
    };

    const buildRows = (doctors) => {
        resultCount.textContent = String(doctors.length);

        if (!Array.isArray(doctors) || doctors.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted">No hay resultados para esta busqueda.</td></tr>';
            return;
        }

        let currentSpecialty = '';
        const rows = [];

        doctors.forEach((doctor) => {
            const nombre = String(doctor.nombre_completo ?? '');
            const telefono = String(doctor.telefono ?? '');
            const especialidad = String(doctor.especialidad ?? '');
            const doctorId = Number(doctor.id ?? 0);
            const specialtyId = Number(doctor.id_especialidad ?? 0);

            if (especialidad !== currentSpecialty) {
                currentSpecialty = especialidad;
                const toneClass = hashTone(especialidad);

                rows.push(`
                    <tr class="specialty-group-row ${toneClass}">
                        <td colspan="4">
                            <span class="specialty-pill ${toneClass}">
                                <i class="bi bi-bookmark-star-fill me-2"></i>${escapeHtml(especialidad)}
                            </span>
                        </td>
                    </tr>
                `);
            }

            rows.push(`
                <tr class="doctor-row ${hashTone(especialidad)}">
                    <td class="fw-semibold">${escapeHtml(nombre)}</td>
                    <td class="phone-cell"><span class="phone-chip phone-emphasis"><i class="bi bi-telephone-fill me-2"></i>${escapeHtml(telefono)}</span></td>
                    <td><span class="text-muted">${escapeHtml(especialidad)}</span></td>
                    <td class="text-center">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary edit-doctor-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editDoctorModal"
                            data-doctor-id="${doctorId}"
                            data-doctor-name="${escapeHtml(nombre)}"
                            data-doctor-phone="${escapeHtml(telefono)}"
                            data-specialty-id="${specialtyId}"
                            data-specialty-name="${escapeHtml(especialidad)}"
                        >
                            <i class="bi bi-pencil-square me-1"></i>Editar
                        </button>
                    </td>
                </tr>
            `);
        });

        tableBody.innerHTML = rows.join('');
    };

    let timeoutId;
    let addSpecialtyTimeoutId;
    let editSpecialtyTimeoutId;

    const createSpecialtyAutocomplete = (inputEl, hiddenIdEl, suggestionsEl) => {
        const hide = () => {
            suggestionsEl.innerHTML = '';
            suggestionsEl.classList.remove('show');
        };

        const render = (specialties) => {
            if (!Array.isArray(specialties) || specialties.length === 0) {
                hide();
                return;
            }

            suggestionsEl.innerHTML = specialties.map((specialty) => {
                const id = Number(specialty.id ?? 0);
                const name = escapeHtml(String(specialty.nombre ?? ''));

                return `<button type="button" class="specialty-option" data-id="${id}" data-name="${name}">${name}</button>`;
            }).join('');

            suggestionsEl.classList.add('show');
        };

        const search = async () => {
            const query = inputEl.value.trim();

            if (query.length < 1) {
                hide();
                return;
            }

            try {
                const response = await fetch(`${baseUrl}?format=specialties&q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    hide();
                    return;
                }

                const payload = await response.json();
                render(payload.specialties ?? []);
            } catch (error) {
                console.error('No se pudieron cargar especialidades.', error);
                hide();
            }
        };

        suggestionsEl.addEventListener('click', (event) => {
            const option = event.target.closest('.specialty-option');
            if (!option) {
                return;
            }

            inputEl.value = option.dataset.name ?? '';
            hiddenIdEl.value = option.dataset.id ?? '';
            inputEl.classList.remove('is-invalid');
            hide();
        });

        inputEl.addEventListener('focus', () => {
            if (inputEl.value.trim() !== '') {
                search();
            }
        });

        document.addEventListener('click', (event) => {
            if (!suggestionsEl.contains(event.target) && event.target !== inputEl) {
                hide();
            }
        });

        return { search };
    };

    const addSpecialtyAutocomplete = createSpecialtyAutocomplete(specialtyInput, specialtyIdInput, specialtySuggestions);
    const editSpecialtyAutocomplete = createSpecialtyAutocomplete(editSpecialtyInput, editSpecialtyIdInput, editSpecialtySuggestions);

    searchInput.addEventListener('input', () => {
        clearTimeout(timeoutId);

        timeoutId = setTimeout(async () => {
            const query = searchInput.value.trim();
            const url = `${baseUrl}?format=json&q=${encodeURIComponent(query)}`;

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                buildRows(payload.doctors ?? []);
            } catch (error) {
                console.error('No se pudo realizar la busqueda en tiempo real.', error);
            }
        }, 220);
    });

    specialtyInput.addEventListener('input', () => {
        specialtyIdInput.value = '';
        clearTimeout(addSpecialtyTimeoutId);
        addSpecialtyTimeoutId = setTimeout(addSpecialtyAutocomplete.search, 180);
    });

    editSpecialtyInput.addEventListener('input', () => {
        editSpecialtyIdInput.value = '';
        clearTimeout(editSpecialtyTimeoutId);
        editSpecialtyTimeoutId = setTimeout(editSpecialtyAutocomplete.search, 180);
    });

    addDoctorForm.addEventListener('submit', (event) => {
        if (specialtyIdInput.value.trim() === '') {
            event.preventDefault();
            specialtyInput.focus();
            specialtyInput.classList.add('is-invalid');
            return;
        }

        specialtyInput.classList.remove('is-invalid');
    });

    editDoctorForm.addEventListener('submit', (event) => {
        if (editSpecialtyIdInput.value.trim() === '') {
            event.preventDefault();
            editSpecialtyInput.focus();
            editSpecialtyInput.classList.add('is-invalid');
            return;
        }

        editSpecialtyInput.classList.remove('is-invalid');
    });

    addSpecialtyForm.addEventListener('submit', () => {
        specialtyNameInput.value = specialtyNameInput.value.trim();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('.edit-doctor-btn');
        if (!button) {
            return;
        }

        editDoctorIdInput.value = button.dataset.doctorId ?? '';
        editDoctorNameInput.value = button.dataset.doctorName ?? '';
        editDoctorPhoneInput.value = button.dataset.doctorPhone ?? '';
        editSpecialtyInput.value = button.dataset.specialtyName ?? '';
        editSpecialtyIdInput.value = button.dataset.specialtyId ?? '';
        editSpecialtyInput.classList.remove('is-invalid');
    });

    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    });
})();
</script>
