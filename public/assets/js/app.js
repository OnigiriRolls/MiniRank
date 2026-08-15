(function () {
    var searchInput = document.getElementById('keyword-search');
    var noResults = document.getElementById('no-results');

    if (!searchInput) {
        return;
    }

    var table = document.querySelector('.keyword-table');
    var rows = table ? table.querySelectorAll('tbody tr') : [];

    searchInput.addEventListener('input', function () {
        var query = searchInput.value.trim().toLowerCase();
        var visible = 0;

        for (var i = 0; i < rows.length; i++) {
            var cell = rows[i].querySelector('td');
            var phrase = cell ? cell.textContent.toLowerCase() : '';
            var show = query === '' || phrase.indexOf(query) !== -1;
            rows[i].style.display = show ? '' : 'none';
            if (show) {
                visible++;
            }
        }

        if (noResults) {
            noResults.hidden = visible !== 0;
        }
    });
})();

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

                var table = document.querySelector('.keyword-table');
                var positions = data.positions || {};
                var trends = data.trends || {};
                for (var id in positions) {
                    if (!positions.hasOwnProperty(id)) {
                        continue;
                    }
                    var row = table ? table.querySelector('tr[data-keyword-id="' + id + '"]') : null;
                    if (!row) {
                        continue;
                    }
                    var cells = row.querySelectorAll('td');
                    if (cells.length >= 3) {
                        cells[1].textContent = positions[id];
                        cells[2].textContent = trends[id] === null || trends[id] === undefined ? '\u2013' : trends[id];
                    }
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