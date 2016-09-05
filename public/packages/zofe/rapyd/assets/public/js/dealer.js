
function str_rand() {
    var result = '';
    var words = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    var max_position = words.length - 1;
    for (i = 0; i < 7; ++i) {
        position = Math.floor(Math.random() * max_position);
        result = result + words.substring(position, position + 1);
    }
    return result;
}

$(document).ready(function () {
    $("#div_generate_password input").click(function () {

        $("#password").val(str_rand());

    });
    $("#password").val("");

});

