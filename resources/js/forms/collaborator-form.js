// Funcionalidade específica do formulário de colaboradores
document.addEventListener('DOMContentLoaded', function() {
    const positionSelect = document.getElementById('position_id');
    const departmentDisplay = document.getElementById('department_display');

    // Dados dos departamentos para lookup (será passado pelo Blade)
    const departments = window.collaboratorDepartments || [];

    function updateDepartment() {
        const selectedOption = positionSelect?.options[positionSelect.selectedIndex];
        const departmentId = selectedOption?.getAttribute('data-department-id');

        if (departmentId && departmentDisplay) {
            const department = departments.find(dept => dept.id == departmentId);
            departmentDisplay.value = department ? department.name : '';
        } else if (departmentDisplay) {
            departmentDisplay.value = '';
        }
    }

    if (positionSelect) {
        positionSelect.addEventListener('change', updateDepartment);
        // Chamar na inicialização para definir o departamento se já houver um cargo selecionado
        updateDepartment();
    }

    // Aplicar máscaras nos campos
    applyInputMasks();
});

// Função para aplicar máscaras nos campos de entrada
function applyInputMasks() {
    // Máscara para CPF
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });
    }

    // Máscara para telefone
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }

    // Máscara para CEP
    const zipCodeInput = document.getElementById('zip_code');
    if (zipCodeInput) {
        zipCodeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }
}
