function request(type, url, data) {
    return new Promise(function(resolve) {
        $.ajax({
            type: type,
            url: url,
            data: data,
            success: function(data) {
                resolve(data);
            },
            error: function(data) {
                resolve(data);
            }
        });
    });
}