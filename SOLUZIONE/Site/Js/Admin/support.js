$(document).ready(async function () {
    let response = await request('GET', '../../Controllers/Read/Admin/getSupport.php', {});
    response = JSON.parse(response);

    let users = response.users;
    let numUsers = response.numUsers;

    for (let i = 0; i < numUsers / 10; i++) {
        let page = `<li class="page-item"><a class="page-link" href="#" onclick="changePagination()">${i + 1}</a></li>`;
        if (i == 0) {
            page = `<li class="page-item active"><a class="page-link" href="#" onclick="changePagination()">${i + 1}</a></li>`;
        }
        $('#pagination').append(page);
    }

    for (let i = 0; i < numUsers; i++) {
        let user = users[i];
        let userRow = `
            <tr>
                <td>${i + 1}</td>
                <td>${user.username}</td>
                <td>${user.numeroTessera}</td>
                <td>${user.email}</td>
                <td>
                    <button class="btn btn-primary" onclick="genNumeroTessera()">Rigenera Tessera</button>
                </td>
            </tr>
        `;
        $('#supportTable').append(userRow);
    }

});

async function genNumeroTessera() {
    let username = $(event.target).parent().prev().prev().prev().text();
    let email = $(event.target).parent().prev().text();
    let data = {
        username: username,
        email: email
    };

    let response = await request('POST', '../../Controllers/Update/genNumeroTessera.php', data);
    response = JSON.parse(response);

    if (response.status == 'success') {
        let data = {
            email: email,
            subject: 'Tessera Rigenerata',
            message: `La tua tessera è stata rigenerata.<br>Ecco il tuo nuovo numero tessera: `,
            cardReset: true
        };

        let sendMail = await request('POST', '../../Controllers/sendEmail.php', data);
        let array = sendMail.split("<br>");
        sendMail = JSON.parse(array[array.length - 1].trim());

        if (sendMail.status == 'success') {
            alert('Tessera rigenerata con successo; è stata inviata una email all\'utente con il nuovo numero tessera.');
            location.reload();
        } else {
            alert(response.message);
        }
    } else {
        alert(response.message);
    }
}


async function changePagination() {
    $('#pagination').find('.active').removeClass('active');
    $(event.target).parent().addClass('active');
    let page = $(event.target).text();

    let response = await request('GET', '../../Controllers/Read/Admin/getReport.php', { pagina: page });
    response = JSON.parse(tableData);

    let users = response.users;
    let numUsers = response.numUsers;
    $('#supportTable').html('');
    for (let i = 0; i < numUsers; i++) {
        let user = users[i];
        let userRow = `
            <tr>
                <td>${i + 1}</td>
                <td>${user.username}</td>
                <td>${user.numeroTessera}</td>
                <td>${user.email}</td>
                <td>
                    <button class="btn btn-primary" onclick="genNumeroTessera()">Rigenera Tessera</button>
                </td>
            </tr>
        `;
        $('#supportTable').append(userRow);
    }
}
