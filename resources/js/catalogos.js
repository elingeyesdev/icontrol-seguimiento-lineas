document.addEventListener('DOMContentLoaded', () => {
    const userSelect = document.getElementById('user_id');

    if (!userSelect) {
        return;
    }

    userSelect.addEventListener('change', () => {
        const selectedOption = userSelect.options[userSelect.selectedIndex];

        ['nombre', 'apellido', 'ci', 'telefono', 'correo'].forEach((field) => {
            const input = document.getElementById(field);

            if (input) {
                input.value = selectedOption?.dataset[field] ?? '';
            }
        });
    });
});