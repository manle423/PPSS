$(document).ready(function () {
    // Add CSRF token to AJAX requests
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $("#loginForm").on("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function (response) {
                // Check if there are any errors
                if (response.errors) {
                    var errors = response.errors;
                    $("#loginForm .is-invalid").removeClass("is-invalid");
                    $("#loginForm .invalid-feedback").remove();

                    if (errors.email) {
                        $("#email").addClass("is-invalid");
                        $("#email").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.email[0] +
                                "</strong></span>"
                        );
                        $("#password").addClass("is-invalid");
                        $("#password").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.password[0] +
                                "</strong></span>"
                        );
                    }
                    if (errors.password) {
                        
                    }
                    $("#loginModal").modal("show");
                } else {
                    // No errors, reload the page
                    location.reload();
                }
            },
            error: function (xhr) {
                // Check if there are any errors
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    $("#loginForm .is-invalid").removeClass("is-invalid");
                    $("#loginForm .invalid-feedback").remove();

                    if (errors.email) {
                        $("#login-email").addClass("is-invalid");
                        $("#login-email").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.email[0] +
                                "</strong></span>"
                        );
                        $("#login-password").addClass("is-invalid");
                        $("#login-password").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.email[0] +
                                "</strong></span>"
                        );
                    }
                    // if (errors.password) {
                    //     $("#login-password").addClass("is-invalid");
                    //     $("#login-password").after(
                    //         '<span class="invalid-feedback" role="alert"><strong>' +
                    //             errors.password[0] +
                    //             "</strong></span>"
                    //     );
                    // }
                    $("#loginModal").modal("show");
                } else {
                    // No errors, reload the page
                    location.reload();
                }
            },
        });
    });

    $("#registerForm").on("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function (response) {
                // Check if there are any errors
                if (response.errors) {
                    var errors = response.errors;
                    $("#registerForm .is-invalid").removeClass("is-invalid");
                    $("#registerForm .invalid-feedback").remove();

                    if (errors.full_name) {
                        $("#full_name").addClass("is-invalid");
                        $("#full_name").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.full_name[0] +
                                "</strong></span>"
                        );
                    }
                    if (errors.email) {
                        $("#email").addClass("is-invalid");
                        $("#email").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.email[0] +
                                "</strong></span>"
                        );
                    }
                    if (errors.password) {
                        $("#password").addClass("is-invalid");
                        $("#password").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.password[0] +
                                "</strong></span>"
                        );
                    }
                    $("#registerModal").modal("show");
                } else {
                    // No errors, reload the page
                    location.reload();
                }
            },
            error: function (xhr) {
                // Check if there are any errors
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    $("#registerForm .is-invalid").removeClass("is-invalid");
                    $("#registerForm .invalid-feedback").remove();

                    if (errors.full_name) {
                        $("#full_name").addClass("is-invalid");
                        $("#full_name").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.full_name[0] +
                                "</strong></span>"
                        );
                    }
                    if (errors.email) {
                        $("#email").addClass("is-invalid");
                        $("#email").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.email[0] +
                                "</strong></span>"
                        );
                    }
                    if (errors.password) {
                        $("#password").addClass("is-invalid");
                        $("#password").after(
                            '<span class="invalid-feedback" role="alert"><strong>' +
                                errors.password[0] +
                                "</strong></span>"
                        );
                    }
                    $("#registerModal").modal("show");
                } else {
                    // No errors, reload the page
                    location.reload();
                }
            },
        });
    });
});
