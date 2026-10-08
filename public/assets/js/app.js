document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (toggle && sidebar && overlay) {
        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        };

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', closeSidebar);
    }

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }

    // Checkbox que oculta (y deshabilita, para que no se validen ni envien) un grupo de campos.
    document.querySelectorAll('[data-hides]').forEach((checkbox) => {
        const target = document.querySelector(checkbox.dataset.hides);
        if (!target) {
            return;
        }

        const update = () => {
            target.classList.toggle('hidden', checkbox.checked);
            target.querySelectorAll('input, select, textarea').forEach((field) => {
                field.disabled = checkbox.checked;
            });
        };

        checkbox.addEventListener('change', update);
        update();
    });

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm') || 'Estas seguro?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
