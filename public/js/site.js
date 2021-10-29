function hideShowSSN() {
        if($('#show_hide_ssn input').attr("type") == "text"){
            $('#show_hide_ssn input').attr('type', 'password');
            $('#show_hide_ssn i').addClass( "glyphicon-eye-close" );
            $('#show_hide_ssn i').removeClass( "glyphicon-eye-open" );
        }else if($('#show_hide_ssn input').attr("type") == "password"){
            $('#show_hide_ssn input').attr('type', 'text');
            $('#show_hide_ssn i').removeClass( "glyphicon-eye-close" );
            $('#show_hide_ssn i').addClass( "glyphicon-eye-open" );
        }
}

function hideShowPwd() {
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "glyphicon-eye-close" );
            $('#show_hide_password i').removeClass( "glyphicon-eye-open" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "glyphicon-eye-close" );
            $('#show_hide_password i').addClass( "glyphicon-eye-open" );
        }
}

function hideShowPwdConf() {
    if($('#show_hide_confirmation input').attr("type") == "text"){
        $('#show_hide_confirmation input').attr('type', 'password');
        $('#show_hide_confirmation i').addClass( "glyphicon-eye-close" );
        $('#show_hide_confirmation i').removeClass( "glyphicon-eye-open" );
    }else if($('#show_hide_confirmation input').attr("type") == "password"){
        $('#show_hide_confirmation input').attr('type', 'text');
        $('#show_hide_confirmation i').removeClass( "glyphicon-eye-close" );
        $('#show_hide_confirmation i').addClass( "glyphicon-eye-open" );
    }
}