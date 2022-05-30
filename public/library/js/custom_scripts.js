$(document).ready(function(){

    function AjaxProcess(url, data){
        console.log('AjaxProcess()');
        return $.ajax({
        type: "POST",
        async: true,
        dataType: "json",
        url: url,
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        data:{
            data:data,
        }
        });
    }

    // Open Side Nav
    $(".nav-side-tog").click(function(){
        $(".sidenav-open").css("width", "250px");
        //$(".content").css("margin-left", "250px");
        $("body").css("background-color", "rgba(0,0,0,0.4)");
    });

    // Close Side Nav
    $(".sidenav-open .closebtn").click(function(){
        $(".sidenav-open").css("width", "0px");
        //$(".content").css("margin-left", "160px");
        $("body").css("background-color", "white");
    });

    $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results[1] || 0;
    };

    // Login Page Only
    if(document.URL.indexOf("login") >= 0){ 

        // Login User
        function loginUser(){
            if($("#InputUsername").val() != '' && $("#InputPassword").val() != ''){

                $("#loginMessage").html("");
                $("#loginBtn").css("display","none");
                $("#login-loader").css("display","flex");
                
                var data = {
                    username: $("#InputUsername").val(),
                    password: $("#InputPassword").val()
                }

                AjaxProcess("login/ajaxLogin", data)
                    .done(function(data){
                        if(data.error.ErrorStatus == 0){
                            window.location.href = "/";
                        }
                        else if(data.error.ErrorStatus == 401){
                            window.location.href = "/logout";
                        }
                        else if(data.error.ErrorStatus == 3){
                            $("#loginMessage").html("Some of your details were incorrect...");
                            $("#loginBtn").css("display","block");
                            $("#login-loader").css("display","none");
                        }
                    })

            }
            else{
                $("#loginMessage").html("Please provide your username and password...");
            }
        }

        $('.login-box').on('click','#loginBtn',function(){loginUser();});
        $(".login-box").keypress(function(e){if(e.which == 13){loginUser();}});

    }

});
$(document).ready(function(){

    // Render Users Table
    function getUsers(){
        $("#user-list-loader").css("display","flex");
        var data = null
        AjaxProcess("admin/ajaxGetUsers", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#user-list-loader").css("display","none");
                    $(".page-body").html(data.html);
                }
                else if(data.error.ErrorStatus == 401){
                    window.location.href = "/logout";
                }
                else{
                    $("#user-list-loader").css("display","none");
                    $(".page-body").html(data.error.ErrorMessage);
                }
            })
    }

    // Create User
    function newUser(){
        $("#user-new-loader").css("display","flex");
        $("#user-new-btn-wrap").css("display","none");

        var data = {
            userName:       $("#new-user-username").val(),
            userForename:   $("#new-user-fname").val(),
            userSurname:    $("#new-user-sname").val(),
            userEmail:      $("#new-user-email").val(),
            userPassword:   $("#new-user-password").val(),
            userEnabled:    $("#new-user-enabled").val(),
            userAdmin:      $("#new-user-admin").val(),
        }

        AjaxProcess("admin/ajaxAddUser", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#user-new-loader").css("display","none");
                    $('#addUser').modal('hide');          
                }
                else if(data.error.ErrorStatus == 401){
                    window.location.href = "/logout";
                }
                else{
                    $("#user-new-loader").css("display","none");
                    $(".page-body").html(data.error.ErrorMessage);
                }
            })
    }
    //$("#new-user-submit").click(function(){console.log('new user');newUser();});
    $('#new-user-submit').on('click', function(){console.log('new user');newUser();});


    // On Admin Page Load
    if(document.URL.indexOf("admin") >= 0){ 
        console.log('admin');
        getUsers();
    }
    
});
$(document).ready(function(){
    
    // Render Holiday Allowance
    function getAllowance(){
        $("#allowance-loader").css("display","flex");
        $('#selectyear').prop('disabled', true);
        var data = null;

        AjaxProcess("holiday/getAllowance", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#allowance-loader").css("display","none");
                    $("#allowance-wrap").html(data.html);
                    $('#selectyear').prop('disabled', false);
                }
                else if(data.error.ErrorStatus == 401){
                    window.location.href = "/logout";
                }
                else if(data.error.ErrorStatus == 1){
                    $("#allowance-loader").css("display","none");
                    $("#allowance-wrap").html("Error");
                    $('#selectyear').prop('disabled', false);
                }
            })
    }

    // Render Calendar
    function getCalendar(){
        $("#calendar-loader").css("display","flex");
        var data = {
            employee:   $("#employee").val(),
            month:      $("#calMonth").val(),
            year:       $("#calYear").val()
        }
        //console.log(data);

        AjaxProcess("holiday/getCalendar", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#calendar-loader").css("display","none");
                    $("#calendar-wrap").html(data.html);
                }
                else if(data.error.ErrorStatus == 401){
                    window.location.href = "/logout";
                }
                else if(data.error.ErrorStatus == 1){
                    $("#calendar-loader").css("display","none");
                    $("#calendar-wrap").html("Error");
                }
            })
    }

    // Render Calendar when Dropdowns change
    $("#employee").change(function(){getCalendar();});
    $("#calMonth").change(function(){
        $('#calMonth option').removeAttr('selected', 'selected');
        $('#calMonth option:selected').attr('selected', 'selected');
        getCalendar();
    });
    $("#calYear").change(function(){
        $('#calYear option').removeAttr('selected', 'selected');
        getCalendar();
    });

    // Previous Month Button
    $("#calendar-wrap").delegate('#prevMonth', 'click', function(){
        //console.log($('#calMonth').val());
        var val = $('#calMonth option:selected').val();
        var force = false;

        if($('#calMonth').val() == 1){
            if($('#calYear option:first').val() != $('#calYear option:selected').val()){
                $('#calMonth option').removeAttr('selected', 'selected');
                $('#calMonth option:last').attr('selected', 'selected');
                $('#calYear option:selected').prev().attr('selected', 'selected');
                $('#calYear option:selected').nextAll().removeAttr('selected', 'selected');
                getCalendar();
            }
        }
        else{
            $('#calMonth option:selected').prev().attr('selected', 'selected');
            $('#calMonth option:selected').prevAll().removeAttr('selected', 'selected');
            $('#calMonth option:selected').nextAll().removeAttr('selected', 'selected');
            getCalendar();
            force = true;
        }

    });

    // Next Month Button
    $("#calendar-wrap").delegate('#nextMonth', 'click', function(){
        //console.log($('#calMonth').val());
        var val = $('#calMonth option:selected').val();

        if($('#calMonth').val() == 12){
            if($('#calYear option:last').val() != $('#calYear option:selected').val()){
                $('#calMonth option').removeAttr('selected', 'selected');
                $('#calMonth option:first').attr('selected', 'selected');
                $('#calYear option:selected').next().attr('selected', 'selected');
                $('#calYear option:selected').prevAll().removeAttr('selected', 'selected');
                getCalendar();
            }
        }
        else{
            $('#calMonth option:selected').next().attr('selected', 'selected');
            getCalendar();
        }
        
    });

    // Render Holiday Table
    function getHolidays(){
        $("#holidays-loader").css("display","flex");
        var data = null
        AjaxProcess("holiday/getHolidays", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#hol-table-loader").css("display","none");
                    $("#hol-table-wrap").html(data.html);
                }
                else if(data.error.ErrorStatus == 401){
                    window.location.href = "/logout";
                }
                else if(data.error.ErrorStatus == 1){
                    $("#hol-table-loader").css("display","none");
                    $("#hol-table-wrap").html("Error");
                }
            })
    }


    var forceSubmit = false;
    function requestHoliday(){
        $("#req-response").css("display","none");
        $("#submitRequest").css("display","none");
        $("#req-loader").css("display","flex");

        var data = {
            holFrom:        $("#holFrom").val(),
            holFromTime:    $("#holFromTime").val(),
            holTo:          $("#holTo").val(),
            holToTime:      $("#holToTime").val(),
            holRef:         $("#holRef").val(),
            holHalfDays:    $("#holHalfDays").val(),
            forceSubmit:    forceSubmit
        }

        AjaxProcess("holiday/submitHolRequest", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#req-response").html("Request Submitted.");
                    $("#req-response").css("display","block");
                    $("#req-loader").css("display","none");
                    $("#submitRequest").css("display","block");
                }
                else if(data.error.ErrorStatus == 403){
                    window.location.href = "/logout";
                }
                else if(data.error.ErrorStatus == 5 || data.error.ErrorStatus == 50){
                    if(data.error.ErrorStatus == 50){
                        forceSubmit = true;
                    }
                    $("#req-response").html(data.message);
                    $("#req-response").css("display","block");
                    $("#req-loader").css("display","none");
                    $("#submitRequest").css("display","block");                    
                }
                else{
                    $("#req-loader").css("display","none");
                    $("#submitRequest").css("display","block");   
                }
            })
    }

    $('#formModal').on('click','#submitRequest',function(){requestHoliday();});


    $("#formModal").change(function(){
        if($('#from_halfday').prop("checked") == true){
            // console.log('checked-from');
            $("#from-time-select").css("display","block");
        }
        else{
            // console.log('notchecked-from');
            $("#from-time-select").css("display","none");
            $("#holFromTime option:first").attr("selected", "selected");
        }

        if($('#to_halfday').prop("checked") == true){
            // console.log('checked-to');
            $("#to-time-select").css("display","block");
        }
        else{
            // console.log('notchecked-to');
            $("#to-time-select").css("display","none");
            $("#holToTime option:first").attr("selected", "selected");
        }
    });


    // On Holiday Page Load
    if(document.URL.indexOf("holiday") >= 0){ 
        console.log('holiday');
        getAllowance();
        getCalendar();
        getHolidays();
    }

});