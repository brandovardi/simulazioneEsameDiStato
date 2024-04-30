$(document).ready(function() {
    $("#loginForm").submit(function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: $("#loginForm").attr("action"),
            data: { form: $("#loginForm").serialize()},
            success: function(response) {
                if (response == "success") {
                    window.location.href = "./home.php";
                } else {
                    $("#error").html(response);
                }
            }
        });

        return false;
    });
});