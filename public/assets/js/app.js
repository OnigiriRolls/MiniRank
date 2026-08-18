function applyFilters() {
    var table = document.querySelector(".keyword-table");
    var rows = table ? table.querySelectorAll("tbody tr") : [];
    var noResults = document.getElementById("no-results");

    var searchInput = document.getElementById("keyword-search");
    var query = searchInput ? searchInput.value.trim().toLowerCase() : "";

    var minInput = document.getElementById("position-min");
    var maxInput = document.getElementById("position-max");
    var min = minInput ? parseInt(minInput.value, 10) : NaN;
    var max = maxInput ? parseInt(maxInput.value, 10) : NaN;
    var minSet = minInput ? minInput.value !== "" : false;
    var maxSet = maxInput ? maxInput.value !== "" : false;

    var movementSelect = document.getElementById("movement-filter");
    var movement = movementSelect ? movementSelect.value : "";

    var rangeInvalid = minSet && maxSet && min > max;

    var visible = 0;

    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];

        var cell = row.querySelector("td");
        var phrase = cell ? cell.textContent.toLowerCase() : "";
        var matchesText = query === "" || phrase.indexOf(query) !== -1;

        var position = parseInt(row.getAttribute("data-position") || "0", 10) || 0;
        var matchesRange;
        if (rangeInvalid) {
            matchesRange = false;
        } else if (!minSet && !maxSet) {
            matchesRange = true;
        } else if (position === 0) {
            matchesRange = false;
        } else {
            matchesRange =
                (minSet ? position >= min : true) && (maxSet ? position <= max : true);
        }

        var trend = row.getAttribute("data-trend") || "";
        var matchesMovement = movement === "" || trend === movement;

        var show = matchesText && matchesRange && matchesMovement;
        row.style.display = show ? "" : "none";
        if (show) {
            visible++;
        }
    }

    if (noResults) {
        noResults.hidden = visible !== 0;
    }
}

(function () {
    var searchInput = document.getElementById("keyword-search");
    var minInput = document.getElementById("position-min");
    var maxInput = document.getElementById("position-max");
    var movementSelect = document.getElementById("movement-filter");
    var clearButton = document.getElementById("clear-filters");

    if (searchInput) {
        searchInput.addEventListener("input", applyFilters);
    }
    if (minInput) {
        minInput.addEventListener("input", applyFilters);
    }
    if (maxInput) {
        maxInput.addEventListener("input", applyFilters);
    }
    if (movementSelect) {
        movementSelect.addEventListener("change", applyFilters);
    }
    if (clearButton) {
        clearButton.addEventListener("click", function () {
            if (minInput) {
                minInput.value = "";
            }
            if (maxInput) {
                maxInput.value = "";
            }
            if (movementSelect) {
                movementSelect.value = "";
            }
            applyFilters();
        });
    }
})();

(function () {
    var button = document.getElementById("refresh-positions");
    var status = document.getElementById("refresh-status");

    if (!button) {
        return;
    }

    button.addEventListener("click", function () {
        button.disabled = true;
        if (status) {
            status.textContent = "Refreshing positions\u2026";
            status.hidden = false;
        }

        var params = new URLSearchParams();
        params.set("action", "refresh");
        params.set("project_id", button.dataset.projectId || "");
        params.set("csrf_token", button.dataset.csrf || "");

        fetch("index.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: params.toString(),
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("Request failed");
                }
                return response.json();
            })
            .then(function (data) {
                if (status) {
                    status.textContent =
                        "Updated " +
                        data.date +
                        " \u2014 " +
                        Object.keys(data.positions).length +
                        " keywords refreshed.";
                }

                var table = document.querySelector(".keyword-table");
                var positions = data.positions || {};
                var trends = data.trends || {};
                for (var id in positions) {
                    if (!positions.hasOwnProperty(id)) {
                        continue;
                    }
                    var row = table
                        ? table.querySelector('tr[data-keyword-id="' + id + '"]')
                        : null;
                    if (!row) {
                        continue;
                    }
                    var cells = row.querySelectorAll("td");
                    if (cells.length >= 3) {
                        cells[1].textContent = positions[id];
                        cells[2].textContent =
                            trends[id] === null || trends[id] === undefined
                                ? "\u2013"
                                : trends[id];
                    }
                    row.setAttribute("data-position", String(positions[id]));
                    row.setAttribute(
                        "data-trend",
                        trends[id] === null || trends[id] === undefined
                            ? ""
                            : String(trends[id])
                    );
                }

                applyFilters();
            })
            .catch(function () {
                if (status) {
                    status.textContent = "Refresh failed. Please try again.";
                }
            })
            .finally(function () {
                button.disabled = false;
            });
    });
})();

(function () {
    var canvas = document.getElementById("position-chart");
    var dataEl = document.getElementById("position-chart-data");

    if (!canvas || !dataEl) {
        return;
    }

    var data;
    try {
        data = JSON.parse(dataEl.textContent);
    } catch (e) {
        return;
    }

    new Chart(canvas, {
        type: "line",
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: "Position",
                    data: data.values,
                    borderColor: "#4a7dbd",
                    backgroundColor: "rgba(74, 125, 189, 0.15)",
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    tension: 0.2,
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    reverse: true,
                    ticks: {
                        precision: 0,
                        maxTicksLimit: 8,
                    },
                },
                x: {
                    ticks: {
                        maxTicksLimit: 10,
                        maxRotation: 0,
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return "Position: " + context.parsed.y;
                        },
                    },
                },
            },
        },
    });
})();

(function () {
    var errorEl = document.getElementById("add-error");
    var input = document.querySelector('.add-form input[name="phrase"]');

    if (!errorEl || !input) {
        return;
    }

    input.addEventListener("input", function () {
        errorEl.hidden = true;
    });
})();
