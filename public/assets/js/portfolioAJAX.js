$(document).ready(function() {
    $('#add-object').submit(function(event) {
        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
        var payload = {
            fieldInfo: $('input[name=info]').val()
        };
        var errorField = document.getElementById("title-error");

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify(payload), // our data object
            dataType: 'json' // what type of data do we expect back from the server
        })
            .done(function(data) {
                errorField.innerText = "";
                errorField.classList.remove("error");
                window.location.href = data['url'];
                console.log(data['url']);
            })
            .fail(function(error) {
                console.log(error)
                errorField.innerText = "";
                errorField.classList.remove("error");
                if (error.responseJSON.url) {
                    window.location.href = error.responseJSON.url;
                }
                if (error.responseJSON.error) {
                    errorField.classList.add("error");
                    errorField.append(error.responseJSON.error);
                }

            });
    });

    $('.delete-object').submit(function(event) {
        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
        var payload = {
            id: $(this).attr('name')
        };

        $.ajax({
            type: 'DELETE',
            url: $(this).attr('action'),
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify(payload), // our data object
            dataType: 'json' // what type of data do we expect back from the server
        })
            .done(function(data) {
                window.location.href = data['url'];
                console.log(data);
            })
            .fail(function(error) {
                console.log(error);
            });
    });

});