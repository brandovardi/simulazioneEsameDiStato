$(document).ready(async function() {

    let tableData = await request('GET', '../../Controllers/Get/getReport.php', {});
    tableData = JSON.parse(tableData);

    $('#reportTable').DataTable({
        data: [
            tableData
        ]
    });


});