if (document.getElementById('init-button')) {
    document.getElementById('init-form').addEventListener('submit', function (event) {
        event.preventDefault();
        fetch('init.php', {
            method: 'POST'
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });
}