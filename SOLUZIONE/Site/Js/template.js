let navBar = `
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
            <a class="nav-link" href="./booking.php">Prenotazione</a>
        </li>
    </ul>
    <button class="btn btn-danger" onclick="window.location.href='../logout.php'">Logout</button>
</div>
`;

function generateNavBar() {
    $("nav").attr("class", "navbar navbar-expand-lg navbar-light bg-light");
    $("nav").html(navBar);
}