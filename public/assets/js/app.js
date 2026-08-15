(function () {
    var button = document.getElementById('refresh-positions');
    var status = document.getElementById('refresh-status');

    if (!button) {
        return;
    }

    button.addEventListener('click', function () {
        button.disabled = true;
        if (status) {
            status.textContent = 'Refreshing positions\u2026';
            status.hidden = false;
        }

        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=refresh',
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.json();
            })
            .then(function (data) {
                if (status) {
                    status.textContent = 'Updated ' + data.date + ' \u2014 ' + Object.keys(data.positions).length + ' keywords refreshed.';
                }
            })
            .catch(function () {
                if (status) {
                    status.textContent = 'Refresh failed. Please try again.';
                }
            })
            .finally(function () {
                button.disabled = false;
            });
    });
})();
