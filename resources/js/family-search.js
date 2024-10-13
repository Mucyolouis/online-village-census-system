<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const familyCodeSelect = document.querySelector('[name="existing_family_code"]');
    if (familyCodeSelect) {
        familyCodeSelect.addEventListener('input', function(e) {
            const searchTerm = e.target.value;
            if (searchTerm.length >= 3) {
                fetch(`/api/family-search?search=${searchTerm}`)
                    .then(response => response.json())
                    .then(data => {
                        // Clear existing options
                        familyCodeSelect.innerHTML = '';
                        
                        // Add new options
                        data.forEach(family => {
                            const option = document.createElement('option');
                            option.value = family.family_code;
                            option.textContent = `${family.family_code} - ${family.head_of_family.firstname} ${family.head_of_family.lastname}`;
                            familyCodeSelect.appendChild(option);
                        });
                    });
            }
        });
    }
});
</script>