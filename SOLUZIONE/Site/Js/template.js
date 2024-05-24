let navBarUser = `
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
            <a class="nav-link" href="./home.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./profile.php">Profilo</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./travelSummary.php">Riepiloghi Viaggi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./support.php">Supporto Utente</a>
        </li>
    </ul>
    <button class="btn btn-danger" onclick="window.location.href='../logout.php'">Logout</button>
</div>
`;

let navBarAdmin = `
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
            <a class="nav-link" href="./home.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./map.php">Mappa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./report.php">Report</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./userSupport.php">Richieste Supporto</a>
        </li>
    </ul>
    <button class="btn btn-danger" onclick="window.location.href='../logout.php'">Logout</button>
</div>
`;

let navBarGuest = `
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
            <a class="nav-link" href="./home.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./login.php">Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./register.php">Register</a>
        </li>
    </ul>
</div>
`;

function generateNavBar(userType) {
    $("nav").attr("class", "navbar navbar-expand-lg navbar-light bg-light");
    if (userType == "admin") {
        $("nav").html(navBarAdmin);
        return;
    }
    if (userType == "guest") {
        $("nav").html(navBarGuest);
        return;
    }
    if (userType == "customer") {
        $("nav").html(navBarUser);
        return;
    }
}
