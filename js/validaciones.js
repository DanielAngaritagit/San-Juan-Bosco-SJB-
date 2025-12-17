function validateLettersOnly(input, errorElementId) {
    const errorElement = document.getElementById(errorElementId);
    if (!/^[a-zA-Z\s]*$/.test(input.value)) {
        errorElement.style.display = 'block';
        input.classList.add('is-invalid');
    } else {
        errorElement.style.display = 'none';
        input.classList.remove('is-invalid');
    }
}

function validateNumbersOnly(input, errorElementId) {
    const errorElement = document.getElementById(errorElementId);
    if (!/^[0-9]*$/.test(input.value)) {
        errorElement.style.display = 'block';
        input.classList.add('is-invalid');
    } else {
        errorElement.style.display = 'none';
        input.classList.remove('is-invalid');
    }
}

function validateLength(input, errorElementId, maxLength) {
    const errorElement = document.getElementById(errorElementId);
    if (input.value.length > maxLength) {
        errorElement.textContent = `El campo no puede tener más de ${maxLength} caracteres.`;
        errorElement.style.display = 'block';
        input.classList.add('is-invalid');
    } else {
        errorElement.style.display = 'none';
        input.classList.remove('is-invalid');
    }
}

function validateAgeRange(input, errorElementId, minAge, maxAge) {
    const errorElement = document.getElementById(errorElementId);
    const birthDate = new Date(input.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    if (age < minAge || age > maxAge) {
        errorElement.textContent = `La edad debe estar entre ${minAge} y ${maxAge} años.`;
        errorElement.style.display = 'block';
        input.classList.add('is-invalid');
    } else {
        errorElement.style.display = 'none';
        input.classList.remove('is-invalid');
    }
}

function validateMinAge(input, errorElementId, minAge) {
    const errorElement = document.getElementById(errorElementId);
    const birthDate = new Date(input.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    if (age < minAge) {
        errorElement.textContent = `Debe ser mayor de ${minAge} años.`;
        errorElement.style.display = 'block';
        input.classList.add('is-invalid');
    } else {
        errorElement.style.display = 'none';
        input.classList.remove('is-invalid');
    }
}

function validateEmail(input, errorElementId) {
    const errorElement = document.getElementById(errorElementId);
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(input.value)) {
        errorElement.style.display = 'block';
        input.classList.add('is-invalid');
    } else {
        errorElement.style.display = 'none';
        input.classList.remove('is-invalid');
    }
}

function checkFormValidity(formId, buttonId) {
    const form = document.getElementById(formId);
    const button = document.getElementById(buttonId);
    const requiredInputs = form.querySelectorAll('[required]');
    const policyCheckbox = form.querySelector('input[name="politica"]');

    let allValid = true;
    requiredInputs.forEach(input => {
        if (!input.value) {
            allValid = false;
        }
    });

    if (policyCheckbox && !policyCheckbox.checked) {
        allValid = false;
    }

    button.disabled = !allValid;
}

document.addEventListener('DOMContentLoaded', () => {
    const forms = {
        'form-acudiente': 'btn-acudiente',
        'form-profesor': 'btn-profesor',
        'form-estudiante': 'btn-estudiante',
        'form-admin': 'btn-admin'
    };

    for (const [formId, buttonId] of Object.entries(forms)) {
        const form = document.getElementById(formId);
        if (form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', () => checkFormValidity(formId, buttonId));
                input.addEventListener('change', () => checkFormValidity(formId, buttonId));
            });
        }
    }
});