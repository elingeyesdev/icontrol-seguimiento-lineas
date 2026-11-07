document.addEventListener('DOMContentLoaded', () => {
    const roleOptions = document.querySelectorAll('.role-option');
    const count = document.getElementById('selected-role-count');
    const label = document.getElementById('selected-role-label');

    if (!count || !label) {
        return;
    }

    const updateRoleSelection = () => {
        const selected = document.querySelectorAll('.role-option-input:checked').length;

        count.textContent = selected;
        label.textContent = selected === 1 ? 'rol seleccionado' : 'roles seleccionados';

        roleOptions.forEach((option) => {
            const input = option.querySelector('.role-option-input');
            option.classList.toggle('selected', input?.checked ?? false);
        });
    };

    roleOptions.forEach((option) => {
        option.querySelector('.role-option-input')?.addEventListener('change', updateRoleSelection);
    });

    updateRoleSelection();
});