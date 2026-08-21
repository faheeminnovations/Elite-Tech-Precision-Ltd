// Form Validation Functions with SweetAlert

document.addEventListener('DOMContentLoaded', function() {
    // Initialize validation for all forms
    initializeFormValidation();
});

// Set default SweetAlert configuration when loaded
if (typeof Swal !== 'undefined') {
    Swal.mixin({
        customClass: {
            popup: 'swal2-eliteflow'
        },
        buttonsStyling: false
    });
}

function initializeFormValidation() {
    // Customer form validation
    const customerForms = document.querySelectorAll('form[data-form-type="customer"]');
    customerForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateCustomerForm(form)) {
                e.preventDefault();
            }
            // If validation passes, let the form submit normally
        });
    });

    // Contract form validation
    const contractForms = document.querySelectorAll('form[data-form-type="contract"]');
    contractForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateContractForm(form)) {
                e.preventDefault();
            }
            // If validation passes, let the form submit normally
        });
    });

    // Service form validation
    const serviceForms = document.querySelectorAll('form[data-form-type="service"]');
    serviceForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateServiceForm(form)) {
                e.preventDefault();
            }
            // If validation passes, let the form submit normally
        });
    });
}

// Customer Form Validation
function validateCustomerForm(form) {
    let isValid = true;
    const errors = [];

    // Name validation (required)
    const name = form.querySelector('[name="name"]');
    if (name && !name.value.trim()) {
        errors.push('Customer name is required');
        markFieldInvalid(name);
        isValid = false;
    } else if (name) {
        markFieldValid(name);
    }

    // Email validation (if provided)
    const email = form.querySelector('[name="email"]');
    if (email && email.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value.trim())) {
            errors.push('Please provide a valid email address');
            markFieldInvalid(email);
            isValid = false;
        } else {
            markFieldValid(email);
        }
    }

    // Phone validation (if provided)
    const phone = form.querySelector('[name="phone"]');
    if (phone && phone.value.trim()) {
        const phoneRegex = /^[0-9+\-\s()]*$/;
        if (!phoneRegex.test(phone.value.trim())) {
            errors.push('Phone number format is invalid');
            markFieldInvalid(phone);
            isValid = false;
        } else {
            markFieldValid(phone);
        }
    }

    // Category validation (if provided)
    const category = form.querySelector('[name="category"]');
    if (category && category.value.trim() && !['chain', 'new'].includes(category.value.trim())) {
        errors.push('Category must be either chain or new');
        markFieldInvalid(category);
        isValid = false;
    } else if (category) {
        markFieldValid(category);
    }

    // Date validation (if provided)
    const completionDate = form.querySelector('[name="completion_date"]');
    if (completionDate && completionDate.value.trim()) {
        if (!isValidDate(completionDate.value)) {
            errors.push('Completion date must be a valid date');
            markFieldInvalid(completionDate);
            isValid = false;
        } else {
            markFieldValid(completionDate);
        }
    }

    if (!isValid) {
        showValidationError(errors);
    }

    return isValid;
}

// Contract Form Validation
function validateContractForm(form) {
    let isValid = true;
    const errors = [];

    // Customer name validation (required)
    const customerName = form.querySelector('[name="customer_name"]');
    if (customerName && !customerName.value.trim()) {
        errors.push('Customer name is required');
        markFieldInvalid(customerName);
        isValid = false;
    } else if (customerName) {
        markFieldValid(customerName);
    }

    // Contract reference validation (required)
    const contractRef = form.querySelector('[name="contract_ref"]');
    if (contractRef && !contractRef.value.trim()) {
        errors.push('Contract reference is required');
        markFieldInvalid(contractRef);
        isValid = false;
    } else if (contractRef) {
        markFieldValid(contractRef);
    }

    // Email validation (if provided)
    const customerEmail = form.querySelector('[name="customer_email"]');
    if (customerEmail && customerEmail.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(customerEmail.value.trim())) {
            errors.push('Please provide a valid email address');
            markFieldInvalid(customerEmail);
            isValid = false;
        } else {
            markFieldValid(customerEmail);
        }
    }

    // Contract value validation (if provided)
    const contractValue = form.querySelector('[name="contract_value"]');
    if (contractValue && contractValue.value.trim()) {
        if (isNaN(contractValue.value) || parseFloat(contractValue.value) < 0) {
            errors.push('Contract value must be a positive number');
            markFieldInvalid(contractValue);
            isValid = false;
        } else {
            markFieldValid(contractValue);
        }
    }

    // Date validations
    const startDate = form.querySelector('[name="start_date"]');
    const expiryDate = form.querySelector('[name="expiry_date"]');
    const nextPpmDue = form.querySelector('[name="next_ppm_due"]');
    const lastPpmDate = form.querySelector('[name="last_ppm_date"]');

    if (startDate && startDate.value.trim()) {
        if (!isValidDate(startDate.value)) {
            errors.push('Start date must be a valid date');
            markFieldInvalid(startDate);
            isValid = false;
        } else {
            markFieldValid(startDate);
        }
    }

    if (expiryDate && expiryDate.value.trim()) {
        if (!isValidDate(expiryDate.value)) {
            errors.push('Expiry date must be a valid date');
            markFieldInvalid(expiryDate);
            isValid = false;
        } else if (startDate && startDate.value && expiryDate.value <= startDate.value) {
            errors.push('Expiry date must be after start date');
            markFieldInvalid(expiryDate);
            isValid = false;
        } else {
            markFieldValid(expiryDate);
        }
    }

    if (lastPpmDate && lastPpmDate.value.trim()) {
        if (!isValidDate(lastPpmDate.value)) {
            errors.push('Last PPM date must be a valid date');
            markFieldInvalid(lastPpmDate);
            isValid = false;
        } else {
            markFieldValid(lastPpmDate);
        }
    }

    if (nextPpmDue && nextPpmDue.value.trim()) {
        if (!isValidDate(nextPpmDue.value)) {
            errors.push('Next PPM due date must be a valid date');
            markFieldInvalid(nextPpmDue);
            isValid = false;
        } else {
            markFieldValid(nextPpmDue);
        }
    }

    if (!isValid) {
        showValidationError(errors);
    }

    return isValid;
}

// Service Form Validation
function validateServiceForm(form) {
    let isValid = true;
    const errors = [];

    // Job reference validation (required)
    const jobRef = form.querySelector('[name="job_ref"]');
    if (jobRef && !jobRef.value.trim()) {
        errors.push('Job reference is required');
        markFieldInvalid(jobRef);
        isValid = false;
    } else if (jobRef) {
        markFieldValid(jobRef);
    }

    // Customer name validation (required)
    const customerName = form.querySelector('[name="customer_name"]');
    if (customerName && !customerName.value.trim()) {
        errors.push('Customer name is required');
        markFieldInvalid(customerName);
        isValid = false;
    } else if (customerName) {
        markFieldValid(customerName);
    }

    // Service type validation (required)
    const serviceType = form.querySelector('[name="service_type"]');
    const validServiceTypes = ['PPM', 'Repair', 'Installation', 'Service Call', 'Inspection'];
    if (serviceType && !serviceType.value.trim()) {
        errors.push('Service type is required');
        markFieldInvalid(serviceType);
        isValid = false;
    } else if (serviceType && !validServiceTypes.includes(serviceType.value.trim())) {
        errors.push('Service type must be one of: PPM, Repair, Installation, Service Call, Inspection');
        markFieldInvalid(serviceType);
        isValid = false;
    } else if (serviceType) {
        markFieldValid(serviceType);
    }

    // Status validation (required)
    const status = form.querySelector('[name="status"]');
    const validStatuses = ['scheduled', 'in-progress', 'completed', 'cancelled'];
    if (status && !status.value.trim()) {
        errors.push('Status is required');
        markFieldInvalid(status);
        isValid = false;
    } else if (status && !validStatuses.includes(status.value.trim())) {
        errors.push('Status must be one of: scheduled, in-progress, completed, cancelled');
        markFieldInvalid(status);
        isValid = false;
    } else if (status) {
        markFieldValid(status);
    }

    // Engineer ID validation (if provided)
    const engineerId = form.querySelector('[name="engineer_id"]');
    if (engineerId && engineerId.value.trim()) {
        if (isNaN(engineerId.value)) {
            errors.push('Invalid engineer selection');
            markFieldInvalid(engineerId);
            isValid = false;
        } else {
            markFieldValid(engineerId);
        }
    }

    // Date validations
    const visitDate = form.querySelector('[name="visit_date"]');
    const nextPpmDue = form.querySelector('[name="next_ppm_due"]');

    if (visitDate && visitDate.value.trim()) {
        if (!isValidDate(visitDate.value)) {
            errors.push('Visit date must be a valid date');
            markFieldInvalid(visitDate);
            isValid = false;
        } else {
            markFieldValid(visitDate);
        }
    }

    if (nextPpmDue && nextPpmDue.value.trim()) {
        if (!isValidDate(nextPpmDue.value)) {
            errors.push('Next PPM due date must be a valid date');
            markFieldInvalid(nextPpmDue);
            isValid = false;
        } else {
            markFieldValid(nextPpmDue);
        }
    }

    // Remedial required validation (if provided)
    const remedialRequired = form.querySelector('[name="remedial_required"]');
    if (remedialRequired && remedialRequired.value.trim() && !['yes', 'no'].includes(remedialRequired.value.trim())) {
        errors.push('Remedial required must be either yes or no');
        markFieldInvalid(remedialRequired);
        isValid = false;
    } else if (remedialRequired) {
        markFieldValid(remedialRequired);
    }

    if (!isValid) {
        showValidationError(errors);
    }

    return isValid;
}

// Helper Functions
function isValidDate(dateString) {
    const date = new Date(dateString);
    return !isNaN(date.getTime());
}

function markFieldInvalid(field) {
    if (field) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
    }
}

function markFieldValid(field) {
    if (field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    }
}

function showValidationError(errors) {
    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        html: errors.join('<br>'),
        confirmButtonColor: '#FF6B35',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'swal2-eliteflow'
        }
    });
}
