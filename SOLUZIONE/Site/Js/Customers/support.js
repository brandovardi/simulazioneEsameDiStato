$(document).ready(function () {

    popUp();
    $("#confirmAction").on("click", async function () {
        $("#confirmSupport").modal("hide");
        let response = await request("POST", "../../Controllers/Update/Customers/blockCard.php", {});
        response = JSON.parse(response);

        if (response.status == "success") {
            alert("Tessera bloccata con successo");
        } else {
            alert(response.message);
        }
    });
});

function popUp() {
    let confirmModal = `
    <div class="modal fade" id="confirmSupport" tabindex="-1" role="dialog" aria-labelledby="confirmSupport"
    aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteStation">Conferma azione</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Se hai perso la tessera ti consigliamo di bloccarla per evitare che venga utilizzata da terzi.<br>
                    Vuoi bloccare la tessera?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    `;
    $("body").append(confirmModal);
}