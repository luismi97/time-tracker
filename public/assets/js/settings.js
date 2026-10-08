document.addEventListener('DOMContentLoaded', () => {
    const addIpButton = document.getElementById('add-current-ip');
    const allowedIps = document.getElementById('allowed-ips');
    if (addIpButton && allowedIps) {
        addIpButton.addEventListener('click', () => {
            const ip = addIpButton.dataset.ip;
            const lines = allowedIps.value.split(/[\s,]+/).filter(Boolean);
            if (ip && !lines.includes(ip)) {
                allowedIps.value = [...lines, ip].join('\n');
            }
        });
    }

    const is24h = document.getElementById('is-24h');
    const is7Days = document.getElementById('is-7-days');
    const sameDayField = document.getElementById('same-day-field');
    const singleFields = document.getElementById('single-hours-fields');
    const manualFields = document.getElementById('manual-days-fields');

    if (!is24h || !sameDayField || !singleFields || !manualFields) {
        return;
    }

    const update = () => {
        const open24h = is24h.checked;
        const sameEveryDay = !!(is7Days && is7Days.checked);

        sameDayField.classList.toggle('hidden', open24h);
        singleFields.classList.toggle('hidden', open24h || !sameEveryDay);
        manualFields.classList.toggle('hidden', open24h || sameEveryDay);
    };

    is24h.addEventListener('change', update);
    if (is7Days) {
        is7Days.addEventListener('change', update);
    }
    update();
});
