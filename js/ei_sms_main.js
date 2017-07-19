jQuery(document).ready(function ($) {
    $('#mainform').submit(function () {
        var response = 'yes';
        var twilioEnabled = $('#wc_twilio_estatic_enabled').prop('checked');//$('#wc_twilio_estatic_enabled').val();
        var twilio_account_id = $('#wc_twilio_estatic_account_id').val();
        var twilio_auth_token = $('#wc_twilio_estatic_auth_token').val();
        var twilio_from_number = $('#wc_twilio_estatic_from_number').val();

        var plivoResponse = 'yes';
        var plivoEnabled = $('#wc_plivo_estatic_enabled').prop('checked');//$('#wc_plivo_estatic_enabled').val();
        var plivo_account_id = $('#wc_plivo_estatic_account_id').val();
        var plivo_auth_token = $('#wc_plivo_estatic_auth_token').val();
        var plivo_from_number = $('#wc_plivo_estatic_from_number').val();


        var error = $('p.error').html();
        /*
         if (twilioEnabled == false) {
         if (error == '' || typeof (error) == 'undefined') {
         $("#wc_twilio_estatic_enabled").after("<p class='error'>Enable Twilio SMS</p>");
         $("html, body").animate({scrollTop: 0}, 1000);
         } else {
         $("html, body").animate({scrollTop: 0}, 1000);
         }
         response = 'no';
         } else {
         $("#wc_twilio_estatic_enabled").next("p.error").remove();
         }
         */
        if (twilioEnabled == true && twilio_account_id == '') {
            if (error == '' || typeof (error) == 'undefined') {
                $("#wc_twilio_estatic_account_id").after("<p class='error'>Account SID is required</p>");
                $("html, body").animate({scrollTop: 0}, 1000);
            } else {
                $("html, body").animate({scrollTop: 0}, 1000);
            }
            response = 'no';
        } else {
            $("#wc_twilio_estatic_account_id").next("p.error").remove();
        }
        if (twilioEnabled == true && twilio_auth_token == '') {
            if (error == '' || typeof (error) == 'undefined') {
                $("#wc_twilio_estatic_auth_token").after("<p class='error'>Auth Token is required</p>");
                $("html, body").animate({scrollTop: 0}, 1000);
            } else {
                $("html, body").animate({scrollTop: 0}, 1000);
            }
            response = 'no';
        } else {
            $("#wc_twilio_estatic_auth_token").next("p.error").remove();
        }

        if (twilioEnabled == true && twilio_from_number == '') {
            if (error == '' || typeof (error) == 'undefined') {
                if (error != 'From Number is required') {
                    $("#wc_twilio_estatic_from_number").after("<p class='error'>From Number is required</p>");
                }
                $("html, body").animate({scrollTop: 0}, 1000);
            } else {
                $("html, body").animate({scrollTop: 0}, 1000);
            }
            response = 'no';
        } else {
            $("#wc_twilio_estatic_from_number").next("p.error").remove();
        }
        var error = '';
        if (response == 'no') {
            return false;
        }


        var error = $('p.error').html();
        /*
         if (plivoEnabled == false) {
         if (error == '' || typeof (error) == 'undefined') {
         $("#wc_plivo_estatic_enabled").after("<p class='error'>Enable Twilio SMS</p>");
         $("html, body").animate({scrollTop: 0}, 1000);
         } else {
         $("html, body").animate({scrollTop: 0}, 1000);
         }
         response = 'no';
         } else {
         $("#wc_plivo_estatic_enabled").next("p.error").remove();
         }
         */
        if (plivoEnabled == true && plivo_account_id == '') {
            if (error == '' || typeof (error) == 'undefined') {
                $("#wc_plivo_estatic_account_id").after("<p class='error'>Account SID is required</p>");
                $("html, body").animate({scrollTop: 0}, 1000);
            } else {
                $("html, body").animate({scrollTop: 0}, 1000);
            }
            plivoResponse = 'no';
        } else {
            $("#wc_plivo_estatic_account_id").next("p.error").remove();
        }
        if (plivoEnabled == true && plivo_auth_token == '') {
            if (error == '' || typeof (error) == 'undefined') {
                $("#wc_plivo_estatic_auth_token").after("<p class='error'>Auth Token is required</p>");
                $("html, body").animate({scrollTop: 0}, 1000);
            } else {
                $("html, body").animate({scrollTop: 0}, 1000);
            }
            plivoResponse = 'no';
        } else {
            $("#wc_plivo_estatic_auth_token").next("p.error").remove();
        }

        if (plivoEnabled == true && plivo_from_number == '') {
            if (error == '' || typeof (error) == 'undefined') {
                if (error != 'From Number is required') {
                    $("#wc_plivo_estatic_from_number").after("<p class='error'>From Number is required</p>");
                }
                $("html, body").animate({scrollTop: 0}, 1000);
            } else {
                $("html, body").animate({scrollTop: 0}, 1000);
            }
            plivoResponse = 'no';
        } else {
            $("#wc_plivo_estatic_from_number").next("p.error").remove();
        }
        var error = '';
        if (plivoResponse == 'no') {
            return false;
        }
    });
});