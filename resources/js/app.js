import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.querySelector('[data-sidebar-open]');
    const closeBtn = document.querySelector('[data-sidebar-close]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const aside = document.querySelector('[data-sidebar]');

    const setOpen = (open) => {
        if (!aside) {
            return;
        }
        aside.classList.toggle('-translate-x-full', !open);
        aside.classList.toggle('translate-x-0', open);
        backdrop?.classList.toggle('hidden', !open);
    };

    openBtn?.addEventListener('click', () => setOpen(true));
    closeBtn?.addEventListener('click', () => setOpen(false));
    backdrop?.addEventListener('click', () => setOpen(false));

    const profileMenu = document.querySelector('[data-profile-menu]');
    const profileToggle = document.querySelector('[data-profile-toggle]');
    const profileDropdown = document.querySelector('[data-profile-dropdown]');

    const setProfileOpen = (open) => {
        if (!profileDropdown || !profileToggle) {
            return;
        }
        profileDropdown.classList.toggle('hidden', !open);
        profileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    profileToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = profileDropdown && !profileDropdown.classList.contains('hidden');
        setProfileOpen(!isOpen);
    });

    document.addEventListener('click', (e) => {
        if (profileMenu && !profileMenu.contains(e.target)) {
            setProfileOpen(false);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            setProfileOpen(false);
            setAppointmentModalOpen(false);
            setStudentModalOpen(false);
            setCounselorSessionsModalOpen(false);
            setCounselorModalOpen(false);
            setSessionNotesModalOpen(false);
        }
    });

    const appointmentModal = document.querySelector('[data-appointment-modal]');
    const appointmentModalOpenBtns = document.querySelectorAll('[data-appointment-modal-open]');
    const appointmentModalCloseBtns = document.querySelectorAll('[data-appointment-modal-close]');
    const appointmentModalBackdrop = document.querySelector('[data-appointment-modal-backdrop]');
    const appointmentForm = document.querySelector('[data-appointment-form]');
    const appointmentEditBtns = document.querySelectorAll('[data-appointment-edit]');
    const appointmentModalTitle = document.querySelector('[data-appointment-modal-title]');
    const appointmentModalSubtitle = document.querySelector('[data-appointment-modal-subtitle]');
    const appointmentSubmitBtn = document.querySelector('[data-appointment-submit]');

    const appointmentStoreUrl = appointmentForm?.dataset.storeUrl;
    const appointmentTimeSelect = document.getElementById('appointment_time');

    const setAppointmentModalOpen = (open) => {
        if (!appointmentModal) {
            return;
        }
        appointmentModal.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    const ensureTimeOption = (time) => {
        if (!appointmentTimeSelect || !time) {
            return;
        }
        const exists = Array.from(appointmentTimeSelect.options).some((o) => o.value === time);
        if (!exists) {
            const option = document.createElement('option');
            option.value = time;
            option.textContent = time;
            appointmentTimeSelect.add(option);
        }
    };

    const setAppointmentFormMode = (mode, data = {}) => {
        if (!appointmentForm) {
            return;
        }

        const isEdit = mode === 'edit';
        const methodInput = appointmentForm.querySelector('input[name="_method"]');
        const statusField = appointmentForm.querySelector('[data-appointment-status-field]');
        const statusSelect = document.getElementById('appointment_status');

        if (isEdit) {
            appointmentForm.action = data.updateUrl ?? appointmentForm.action;
            if (!methodInput) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                appointmentForm.appendChild(input);
            } else {
                methodInput.value = 'PUT';
            }
            appointmentModalTitle.textContent = 'Edit Appointment';
            appointmentModalSubtitle.textContent = 'Update appointment details and status';
            if (appointmentSubmitBtn) {
                appointmentSubmitBtn.textContent = 'Update Appointment';
            }
            statusField?.classList.remove('hidden');
            if (statusSelect) {
                statusSelect.required = true;
                statusSelect.disabled = false;
            }
        } else {
            appointmentForm.action = appointmentStoreUrl ?? appointmentForm.action;
            methodInput?.remove();
            appointmentModalTitle.textContent = 'Schedule New Appointment';
            appointmentModalSubtitle.textContent = 'Assign a student, counselor, date, and counseling type';
            if (appointmentSubmitBtn) {
                appointmentSubmitBtn.textContent = 'Add Appointment';
            }
            statusField?.classList.add('hidden');
            if (statusSelect) {
                statusSelect.required = false;
                statusSelect.disabled = true;
            }
        }

        const recordId = document.getElementById('appointment_record_id');
        if (recordId) {
            recordId.value = data.id ?? '';
        }

        const studentId = document.getElementById('appointment_student_id');
        const counselorId = document.getElementById('appointment_counselor_id');
        const date = document.getElementById('appointment_date');
        const type = document.getElementById('appointment_type');
        const status = document.getElementById('appointment_status');
        const notes = document.getElementById('appointment_notes');

        if (studentId) {
            studentId.value = data.studentId ?? '';
        }
        if (counselorId) {
            counselorId.value = data.counselorId ?? '';
        }
        if (date) {
            date.value = data.date ?? '';
        }
        if (type) {
            type.value = data.type ?? '';
        }
        if (status) {
            status.value = data.status ?? 'scheduled';
        }
        if (notes) {
            notes.value = data.notes ?? '';
        }

        if (appointmentTimeSelect) {
            ensureTimeOption(data.time ?? '');
            appointmentTimeSelect.value = data.time ?? '';
        }
    };

    appointmentModalOpenBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            setAppointmentFormMode('add');
            setAppointmentModalOpen(true);
        });
    });

    appointmentEditBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            setAppointmentFormMode('edit', {
                id: btn.dataset.id,
                updateUrl: btn.dataset.updateUrl,
                studentId: btn.dataset.studentId,
                counselorId: btn.dataset.counselorId,
                date: btn.dataset.date,
                time: btn.dataset.time,
                type: btn.dataset.type,
                status: btn.dataset.status,
                notes: btn.dataset.notes ?? '',
            });
            setAppointmentModalOpen(true);
        });
    });

    appointmentModalCloseBtns.forEach((btn) => {
        btn.addEventListener('click', () => setAppointmentModalOpen(false));
    });

    appointmentModalBackdrop?.addEventListener('click', () => setAppointmentModalOpen(false));

    const studentModal = document.querySelector('[data-student-modal]');
    const studentModalOpenBtns = document.querySelectorAll('[data-student-modal-open]');
    const studentModalCloseBtns = document.querySelectorAll('[data-student-modal-close]');
    const studentModalBackdrop = document.querySelector('[data-student-modal-backdrop]');
    const studentForm = document.querySelector('[data-student-form]');
    const studentEditBtns = document.querySelectorAll('[data-student-edit]');
    const studentModalTitle = document.querySelector('[data-student-modal-title]');
    const studentModalSubtitle = document.querySelector('[data-student-modal-subtitle]');
    const studentStatusField = document.querySelector('[data-student-status-field]');
    const studentSubmitBtn = document.querySelector('[data-student-submit]');

    const studentStoreUrl = studentForm?.dataset.storeUrl;
    const studentFields = {
        name: document.getElementById('student_name'),
        studentId: document.getElementById('student_studentId'),
        email: document.getElementById('student_email'),
        program: document.getElementById('student_program'),
        yearLevel: document.getElementById('student_yearLevel'),
        status: document.getElementById('student_status'),
        recordId: document.getElementById('student_record_id'),
    };

    const setStudentModalOpen = (open) => {
        if (!studentModal) {
            return;
        }
        studentModal.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    const setStudentFormMode = (mode, data = {}) => {
        if (!studentForm) {
            return;
        }

        const isEdit = mode === 'edit';
        const methodInput = studentForm.querySelector('input[name="_method"]');

        if (isEdit) {
            studentForm.action = data.updateUrl ?? studentForm.action;
            if (!methodInput) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                studentForm.appendChild(input);
            } else {
                methodInput.value = 'PUT';
            }
            studentModalTitle.textContent = 'Edit Student';
            studentModalSubtitle.textContent = 'Update student record information';
            studentSubmitBtn.textContent = 'Update Student';
            studentStatusField?.classList.remove('hidden');
            if (studentFields.status) {
                studentFields.status.required = true;
            }
            if (studentFields.recordId) {
                studentFields.recordId.value = data.id ?? '';
            }
        } else {
            studentForm.action = studentStoreUrl ?? studentForm.action;
            methodInput?.remove();
            studentModalTitle.textContent = 'Add New Student';
            studentModalSubtitle.textContent = 'Enter student information to create a new record';
            studentSubmitBtn.textContent = 'Save Student';
            studentStatusField?.classList.add('hidden');
            if (studentFields.status) {
                studentFields.status.required = false;
            }
            if (studentFields.recordId) {
                studentFields.recordId.value = '';
            }
        }

        if (studentFields.name) {
            studentFields.name.value = data.name ?? '';
        }
        if (studentFields.studentId) {
            studentFields.studentId.value = data.studentId ?? '';
        }
        if (studentFields.email) {
            studentFields.email.value = data.email ?? '';
        }
        if (studentFields.program) {
            studentFields.program.value = data.program ?? '';
        }
        if (studentFields.yearLevel) {
            studentFields.yearLevel.value = data.yearLevel ?? '';
        }
        if (studentFields.status) {
            studentFields.status.value = data.status ?? 'active';
        }
    };

    studentModalOpenBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            setStudentFormMode('add');
            setStudentModalOpen(true);
        });
    });

    studentEditBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            setStudentFormMode('edit', {
                id: btn.dataset.id,
                updateUrl: btn.dataset.updateUrl,
                name: btn.dataset.name,
                studentId: btn.dataset.studentId,
                email: btn.dataset.email,
                program: btn.dataset.program,
                yearLevel: btn.dataset.yearLevel,
                status: btn.dataset.status,
            });
            setStudentModalOpen(true);
        });
    });

    studentModalCloseBtns.forEach((btn) => {
        btn.addEventListener('click', () => setStudentModalOpen(false));
    });

    studentModalBackdrop?.addEventListener('click', () => setStudentModalOpen(false));

    const studentIdHint = document.querySelector('[data-student-id-hint]');
    const studentNameHint = document.querySelector('[data-student-name-hint]');
    const studentEmailHint = document.querySelector('[data-student-email-hint]');

    const STUDENT_NAME_PATTERN = /^[a-zA-Z]+(?:[\s'.-][a-zA-Z]+)*$/;
    const STUDENT_GMAIL_PATTERN = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i;

    const validateStudentId = (value, showAlert = false) => {
        const digits = value.replace(/\D/g, '');
        const valid = /^\d{8}$/.test(digits);
        const message = valid
            ? ''
            : digits.length === 0
                ? 'Student ID is required and must be exactly 8 digits.'
                : `Student ID must be exactly 8 digits. You entered ${digits.length} digit(s).`;

        if (studentFields.studentId) {
            studentFields.studentId.setCustomValidity(valid ? '' : message);
            studentFields.studentId.classList.toggle('border-red-400', !valid && digits.length > 0);
        }

        if (studentIdHint) {
            if (valid) {
                studentIdHint.textContent = '';
                studentIdHint.classList.add('hidden');
            } else if (digits.length > 0) {
                studentIdHint.textContent = message;
                studentIdHint.classList.remove('hidden');
            } else {
                studentIdHint.classList.add('hidden');
            }
        }

        if (!valid && showAlert) {
            window.alert(message);
        }

        return valid;
    };

    studentFields.studentId?.addEventListener('input', () => {
        if (studentFields.studentId) {
            studentFields.studentId.value = studentFields.studentId.value.replace(/\D/g, '').slice(0, 8);
        }
        validateStudentId(studentFields.studentId?.value ?? '', false);
    });

    studentFields.studentId?.addEventListener('blur', () => {
        validateStudentId(studentFields.studentId?.value ?? '', false);
    });

    const showFieldHint = (input, hintEl, valid, message) => {
        if (input) {
            input.setCustomValidity(valid ? '' : message);
            input.classList.toggle('border-red-400', !valid && message);
        }
        if (hintEl) {
            if (valid) {
                hintEl.textContent = '';
                hintEl.classList.add('hidden');
            } else if (message) {
                hintEl.textContent = message;
                hintEl.classList.remove('hidden');
            } else {
                hintEl.classList.add('hidden');
            }
        }
    };

    const validateStudentName = (value, showAlert = false) => {
        const trimmed = value.trim();
        const valid = trimmed.length > 0 && STUDENT_NAME_PATTERN.test(trimmed);
        const message = valid
            ? ''
            : trimmed.length === 0
                ? 'Full name is required.'
                : 'Full name may only contain letters, spaces, hyphens, apostrophes, and periods.';

        showFieldHint(studentFields.name, studentNameHint, valid, !valid && trimmed.length > 0 ? message : '');

        if (!valid && showAlert) {
            window.alert(message);
        }

        return valid;
    };

    const validateStudentEmail = (value, showAlert = false) => {
        const trimmed = value.trim();
        const valid = trimmed.length > 0 && STUDENT_GMAIL_PATTERN.test(trimmed);
        const message = valid
            ? ''
            : trimmed.length === 0
                ? 'Email address is required.'
                : 'Email must be a valid Gmail address (example: name@gmail.com).';

        showFieldHint(studentFields.email, studentEmailHint, valid, !valid && trimmed.length > 0 ? message : '');

        if (!valid && showAlert) {
            window.alert(message);
        }

        return valid;
    };

    studentFields.name?.addEventListener('input', () => {
        if (studentFields.name) {
            studentFields.name.value = studentFields.name.value.replace(/[^a-zA-Z\s'.-]/g, '');
        }
        validateStudentName(studentFields.name?.value ?? '', false);
    });

    studentFields.name?.addEventListener('blur', () => {
        validateStudentName(studentFields.name?.value ?? '', false);
    });

    studentFields.email?.addEventListener('input', () => {
        validateStudentEmail(studentFields.email?.value ?? '', false);
    });

    studentFields.email?.addEventListener('blur', () => {
        validateStudentEmail(studentFields.email?.value ?? '', false);
    });

    studentForm?.addEventListener('submit', (event) => {
        const nameValid = validateStudentName(studentFields.name?.value ?? '', false);
        const idValid = validateStudentId(studentFields.studentId?.value ?? '', false);
        const emailValid = validateStudentEmail(studentFields.email?.value ?? '', false);

        if (!nameValid || !idValid || !emailValid) {
            event.preventDefault();

            if (!nameValid) {
                validateStudentName(studentFields.name?.value ?? '', true);
                studentFields.name?.focus();
                studentFields.name?.reportValidity();
            } else if (!idValid) {
                validateStudentId(studentFields.studentId?.value ?? '', true);
                studentFields.studentId?.focus();
                studentFields.studentId?.reportValidity();
            } else {
                validateStudentEmail(studentFields.email?.value ?? '', true);
                studentFields.email?.focus();
                studentFields.email?.reportValidity();
            }
        }
    });

    const counselorSessionsModal = document.querySelector('[data-counselor-sessions-modal]');
    const counselorSessionsOpenBtns = document.querySelectorAll('[data-counselor-sessions-open]');
    const counselorSessionsCloseBtns = document.querySelectorAll('[data-counselor-sessions-close]');
    const counselorSessionsBackdrop = document.querySelector('[data-counselor-sessions-backdrop]');
    const counselorSessionsBody = document.querySelector('[data-counselor-sessions-body]');
    const counselorSessionsTitle = document.querySelector('[data-counselor-sessions-title]');
    const counselorSessionsSubtitle = document.querySelector('[data-counselor-sessions-subtitle]');

    const setCounselorSessionsModalOpen = (open) => {
        if (!counselorSessionsModal) {
            return;
        }
        counselorSessionsModal.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    const openCounselorSessions = (counselorId, counselorName) => {
        const template = document.getElementById(`counselor-sessions-${counselorId}`);
        if (!template || !counselorSessionsBody) {
            return;
        }
        counselorSessionsBody.innerHTML = template.innerHTML;
        if (counselorSessionsTitle) {
            counselorSessionsTitle.textContent = 'Session History';
        }
        if (counselorSessionsSubtitle) {
            counselorSessionsSubtitle.textContent = `${counselorName} — view sessions and update status`;
        }
        setCounselorSessionsModalOpen(true);
    };

    counselorSessionsOpenBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            openCounselorSessions(btn.dataset.counselorId, btn.dataset.counselorName);
        });
    });

    counselorSessionsCloseBtns.forEach((btn) => {
        btn.addEventListener('click', () => setCounselorSessionsModalOpen(false));
    });

    counselorSessionsBackdrop?.addEventListener('click', () => setCounselorSessionsModalOpen(false));

    const activeCounselorId = counselorSessionsModal?.dataset.activeCounselorId;
    const activeCounselorName = counselorSessionsModal?.dataset.activeCounselorName;
    if (activeCounselorId && activeCounselorName) {
        openCounselorSessions(activeCounselorId, activeCounselorName);
    }

    const counselorModal = document.querySelector('[data-counselor-modal]');
    const counselorModalOpenBtns = document.querySelectorAll('[data-counselor-modal-open]');
    const counselorModalCloseBtns = document.querySelectorAll('[data-counselor-modal-close]');
    const counselorModalBackdrop = document.querySelector('[data-counselor-modal-backdrop]');
    const counselorForm = document.querySelector('[data-counselor-form]');
    const counselorEditBtns = document.querySelectorAll('[data-counselor-edit]');
    const counselorModalTitle = document.querySelector('[data-counselor-modal-title]');
    const counselorModalSubtitle = document.querySelector('[data-counselor-modal-subtitle]');
    const counselorSubmitBtn = document.querySelector('[data-counselor-submit]');

    const counselorStoreUrl = counselorForm?.dataset.storeUrl;

    const setCounselorModalOpen = (open) => {
        if (!counselorModal) {
            return;
        }
        counselorModal.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    const setCounselorFormMode = (mode, data = {}) => {
        if (!counselorForm) {
            return;
        }

        const isEdit = mode === 'edit';
        const methodInput = counselorForm.querySelector('input[name="_method"]');

        if (isEdit) {
            counselorForm.action = data.updateUrl ?? counselorForm.action;
            if (!methodInput) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                counselorForm.appendChild(input);
            } else {
                methodInput.value = 'PUT';
            }
            if (counselorModalTitle) {
                counselorModalTitle.textContent = 'Edit Counselor';
            }
            if (counselorModalSubtitle) {
                counselorModalSubtitle.textContent = 'Update counselor profile information';
            }
            if (counselorSubmitBtn) {
                counselorSubmitBtn.textContent = 'Update Counselor';
            }
        } else {
            counselorForm.action = counselorStoreUrl ?? counselorForm.action;
            methodInput?.remove();
            if (counselorModalTitle) {
                counselorModalTitle.textContent = 'Add New Counselor';
            }
            if (counselorModalSubtitle) {
                counselorModalSubtitle.textContent = 'Register a new counselor profile';
            }
            if (counselorSubmitBtn) {
                counselorSubmitBtn.textContent = 'Add Counselor';
            }
        }

        const recordId = document.getElementById('counselor_record_id');
        const name = document.getElementById('counselor_name');
        const email = document.getElementById('counselor_email');
        const phone = document.getElementById('counselor_phone');
        const specialization = document.getElementById('counselor_specialization');
        const availability = document.getElementById('counselor_availability');

        if (recordId) {
            recordId.value = data.id ?? '';
        }
        if (name) {
            name.value = data.name ?? '';
        }
        if (email) {
            email.value = data.email ?? '';
        }
        if (phone) {
            phone.value = data.phone ?? '';
        }
        if (specialization) {
            specialization.value = data.specialization ?? '';
        }
        if (availability) {
            availability.value = data.availability ?? '';
        }

        const counselorAvatarInput = counselorForm.querySelector('[data-counselor-avatar-input]');
        const counselorRemoveAvatarCheckbox = counselorForm.querySelector('[name="remove_avatar"]');
        if (counselorAvatarInput) {
            counselorAvatarInput.value = '';
        }
        if (counselorRemoveAvatarCheckbox) {
            counselorRemoveAvatarCheckbox.checked = false;
        }
        setCounselorAvatarPreview(data.avatarUrl ?? '', data.name ?? '');
    };

    const counselorAvatarInput = counselorForm?.querySelector('[data-counselor-avatar-input]');
    const counselorAvatarPreview = counselorForm?.querySelector('[data-counselor-avatar-preview]');
    const counselorAvatarFallback = counselorForm?.querySelector('[data-counselor-avatar-preview-fallback]');
    const counselorRemoveAvatarField = counselorForm?.querySelector('[data-counselor-remove-avatar-field]');
    const counselorNameInput = document.getElementById('counselor_name');

    const counselorInitialsFromName = (name) => name
        .trim()
        .split(/\s+/)
        .filter(Boolean)
        .map((part) => part[0].toUpperCase())
        .slice(0, 2)
        .join('');

    const setCounselorAvatarPreview = (avatarUrl, name) => {
        const initials = counselorInitialsFromName(name) || '?';
        if (counselorAvatarFallback) {
            counselorAvatarFallback.textContent = initials;
        }
        if (avatarUrl && counselorAvatarPreview) {
            counselorAvatarPreview.src = avatarUrl;
            counselorAvatarPreview.alt = name;
            counselorAvatarPreview.classList.remove('hidden');
            counselorAvatarFallback?.classList.add('hidden');
            counselorAvatarFallback?.classList.remove('flex');
        } else if (counselorAvatarPreview) {
            counselorAvatarPreview.src = '';
            counselorAvatarPreview.classList.add('hidden');
            counselorAvatarFallback?.classList.remove('hidden');
            counselorAvatarFallback?.classList.add('flex');
        }
        if (counselorRemoveAvatarField) {
            counselorRemoveAvatarField.classList.toggle('hidden', !avatarUrl);
        }
    };

    counselorAvatarInput?.addEventListener('change', () => {
        const file = counselorAvatarInput.files?.[0];
        if (!file || !counselorAvatarPreview) {
            return;
        }
        const name = counselorNameInput?.value ?? '';
        counselorAvatarPreview.src = URL.createObjectURL(file);
        counselorAvatarPreview.alt = name;
        counselorAvatarPreview.classList.remove('hidden');
        counselorAvatarFallback?.classList.add('hidden');
        counselorAvatarFallback?.classList.remove('flex');
        if (counselorRemoveAvatarField) {
            counselorRemoveAvatarField.classList.add('hidden');
        }
    });

    counselorNameInput?.addEventListener('input', () => {
        if (counselorAvatarPreview && !counselorAvatarPreview.classList.contains('hidden')) {
            return;
        }
        if (counselorAvatarFallback) {
            counselorAvatarFallback.textContent = counselorInitialsFromName(counselorNameInput.value) || '?';
        }
    });

    counselorModalOpenBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            setCounselorFormMode('add');
            setCounselorModalOpen(true);
        });
    });

    counselorEditBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            setCounselorFormMode('edit', {
                id: btn.dataset.id,
                updateUrl: btn.dataset.updateUrl,
                name: btn.dataset.name,
                email: btn.dataset.email,
                phone: btn.dataset.phone,
                specialization: btn.dataset.specialization,
                availability: btn.dataset.availability,
                avatarUrl: btn.dataset.avatarUrl ?? '',
            });
            setCounselorModalOpen(true);
        });
    });

    counselorModalCloseBtns.forEach((btn) => {
        btn.addEventListener('click', () => setCounselorModalOpen(false));
    });

    counselorModalBackdrop?.addEventListener('click', () => setCounselorModalOpen(false));

    const sessionNotesModal = document.querySelector('[data-session-notes-modal]');
    const sessionNotesOpenBtns = document.querySelectorAll('[data-session-notes-open]');
    const sessionNotesCloseBtns = document.querySelectorAll('[data-session-notes-close]');
    const sessionNotesBackdrop = document.querySelector('[data-session-notes-backdrop]');
    const sessionNotesForm = document.querySelector('[data-session-notes-form]');
    const sessionNotesTextarea = document.getElementById('session_notes');
    const sessionNotesSubtitle = document.querySelector('[data-session-notes-subtitle]');
    const sessionNotesRemoveBtn = document.querySelector('[data-session-notes-remove]');

    const setSessionNotesModalOpen = (open) => {
        if (!sessionNotesModal) {
            return;
        }
        sessionNotesModal.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    sessionNotesOpenBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            if (sessionNotesForm) {
                sessionNotesForm.action = btn.dataset.updateUrl ?? '#';
            }
            const existingNotes = btn.dataset.notes ?? '';
            if (sessionNotesTextarea) {
                sessionNotesTextarea.value = existingNotes;
            }
            if (sessionNotesSubtitle) {
                sessionNotesSubtitle.textContent = `${btn.dataset.student} — ${btn.dataset.date} at ${btn.dataset.time}`;
            }
            if (sessionNotesRemoveBtn) {
                sessionNotesRemoveBtn.classList.toggle('hidden', !existingNotes.trim());
            }
            setSessionNotesModalOpen(true);
        });
    });

    sessionNotesRemoveBtn?.addEventListener('click', () => {
        const studentLabel = sessionNotesSubtitle?.textContent?.split(' — ')[0] ?? 'this session';
        if (!confirm(`Remove session notes for ${studentLabel}?`)) {
            return;
        }
        if (sessionNotesTextarea) {
            sessionNotesTextarea.value = '';
        }
        sessionNotesForm?.requestSubmit();
    });

    sessionNotesCloseBtns.forEach((btn) => {
        btn.addEventListener('click', () => setSessionNotesModalOpen(false));
    });

    sessionNotesBackdrop?.addEventListener('click', () => setSessionNotesModalOpen(false));

    const avatarInput = document.querySelector('[data-avatar-input]');
    const avatarPreview = document.querySelector('[data-avatar-preview]');
    const avatarFallback = document.querySelector('[data-avatar-preview-fallback]');

    avatarInput?.addEventListener('change', () => {
        const file = avatarInput.files?.[0];
        if (!file || !avatarPreview) {
            return;
        }
        const url = URL.createObjectURL(file);
        avatarPreview.src = url;
        avatarPreview.classList.remove('hidden');
        avatarFallback?.classList.add('hidden');
        avatarFallback?.classList.remove('flex');
    });

    const PROFILE_NAME_PATTERN = /^[a-zA-Z]+(?:[\s'.-][a-zA-Z]+)*$/;
    const profileForm = document.querySelector('[data-profile-form]');
    const profileNameInput = document.querySelector('[data-profile-name-input]');
    const profileNameHint = document.querySelector('[data-profile-name-hint]');
    const profilePasswordChanged = document.querySelector('[data-profile-password-changed]');

    const validateProfileName = (value, showAlert = false) => {
        const trimmed = value.trim();
        const valid = trimmed.length > 0 && PROFILE_NAME_PATTERN.test(trimmed);
        const message = valid
            ? ''
            : trimmed.length === 0
                ? 'Full name is required.'
                : 'Full name may only contain letters, spaces, hyphens, apostrophes, and periods.';

        if (profileNameInput) {
            profileNameInput.setCustomValidity(valid ? '' : message);
            profileNameInput.classList.toggle('border-red-400', !valid && trimmed.length > 0);
        }

        if (profileNameHint) {
            if (valid) {
                profileNameHint.textContent = '';
                profileNameHint.classList.add('hidden');
            } else if (trimmed.length > 0) {
                profileNameHint.textContent = message;
                profileNameHint.classList.remove('hidden');
            } else {
                profileNameHint.classList.add('hidden');
            }
        }

        if (!valid && showAlert) {
            window.alert(message);
        }

        return valid;
    };

    profileNameInput?.addEventListener('input', () => {
        if (profileNameInput) {
            profileNameInput.value = profileNameInput.value.replace(/[^a-zA-Z\s'.-]/g, '');
        }
        validateProfileName(profileNameInput?.value ?? '', false);
    });

    profileNameInput?.addEventListener('blur', () => {
        validateProfileName(profileNameInput?.value ?? '', false);
    });

    profileForm?.addEventListener('submit', (event) => {
        if (!validateProfileName(profileNameInput?.value ?? '', true)) {
            event.preventDefault();
            profileNameInput?.focus();
            profileNameInput?.reportValidity();
        }
    });

    if (profilePasswordChanged) {
        window.alert('Your password has been changed successfully.');
    }
});
