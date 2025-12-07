window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    const tables = document.querySelectorAll('.datatable');

    tables.forEach((table) => {
        new simpleDatatables.DataTable(table);
    });
});
