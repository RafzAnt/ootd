document.querySelectorAll('[data-plan-date]').forEach((button) => {
    button.addEventListener('click', () => {
        const dateField = document.getElementById('planned_for');
        const outfitField = document.getElementById('ootd_id');

        if (!dateField || !outfitField) {
            return;
        }

        dateField.value = button.dataset.planDate;
        outfitField.focus();
    });
});
