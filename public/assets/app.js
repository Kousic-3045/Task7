document.addEventListener('DOMContentLoaded', () => {
    const invalidFields = document.querySelectorAll('.is-invalid');
    invalidFields.forEach((field) => {
        field.setAttribute('aria-invalid', 'true');
    });
});
