/**
 * Small progressive enhancements for the archive.
 * No framework, no build-time component magic.
 */

const autoSubmit = document.querySelectorAll('[data-auto-submit]');

autoSubmit.forEach((select) => {
    select.addEventListener('change', () => {
        select.form?.requestSubmit();
    });
});

const operatorSearch = document.querySelector('[data-operator-search]');
const operatorSelect = document.getElementById('operator-select');

if (operatorSearch && operatorSelect) {
    const filterOptions = () => {
        const term = operatorSearch.value.trim().toLowerCase();

        operatorSelect.querySelectorAll('optgroup').forEach((group) => {
            let visible = 0;

            group.querySelectorAll('option[data-search]').forEach((option) => {
                const matches = term === '' || option.dataset.search.includes(term);
                option.hidden = !matches;
                option.disabled = !matches;

                if (matches) {
                    visible += 1;
                }
            });

            group.hidden = visible === 0;
        });

        const selected = operatorSelect.selectedOptions[0];

        if (selected) {
            selected.hidden = false;
            selected.disabled = false;
        }
    };

    operatorSearch.addEventListener('input', filterOptions);
    operatorSearch.addEventListener('search', filterOptions);
}
