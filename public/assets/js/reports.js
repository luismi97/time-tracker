document.addEventListener('DOMContentLoaded', () => {
    const periodSelect = document.getElementById('report-period');
    if (!periodSelect) {
        return;
    }

    const dateField = document.getElementById('report-date-field');
    const monthFields = document.getElementById('report-month-fields');
    const rangeFields = document.getElementById('report-range-fields');

    const updateFields = () => {
        const period = periodSelect.value;
        dateField.classList.toggle('hidden', !(period === 'day' || period === 'week'));
        monthFields.classList.toggle('hidden', period !== 'month');
        rangeFields.classList.toggle('hidden', period !== 'custom');
    };

    periodSelect.addEventListener('change', updateFields);
    updateFields();
});
