document.getElementById('addTaskButton').addEventListener('click', function() {
    const formContainer = document.getElementById('addTaskFormContainer');
    // Alterna a visibilidade do formulário
    if (formContainer.style.display === 'none' || formContainer.style.display === '') {
        formContainer.style.display = 'block'; // Exibe o formulário
    } else {
        formContainer.style.display = 'none'; // Oculta o formulário
    }
});