// Render Users Table
function getUsers(tab="user-tab"){
    $("#user-list-loader").css("display","flex");
    var data = {tab:tab}
    AjaxProcess("admin/ajaxGetUsers", data)
        .done(function(data){
            if(data.error.ErrorStatus == 0){
                $("#user-list-loader").css("display","none");
                $(".page-body").html(data.html);
            }
            else if(data.error.ErrorStatus == 403){
                window.location.href = "/login/logout";
            }
            else{
                $("#user-list-loader").css("display","none");
                $(".page-body").html(data.error.ErrorMessage);
            }
        })
}

// Create User
function newUser(){
    $("#addUser-response").html("");
    $("#addUser-response").css("display","none");
    $("#user-new-btn-wrap").css("display","none");
    $("#user-new-loader").css("display","flex");
    
    var data = {
        userName:       $("#new-user-username").val(),
        userForename:   $("#new-user-fname").val(),
        userSurname:    $("#new-user-sname").val(),
        userEmail:      $("#new-user-email").val(),
        userPassword:   $("#new-user-password").val(),
        userAdmin:      $("#new-user-admin:checked").length,
    }

    AjaxProcess("admin/ajaxAddUser", data)
        .done(function(data){
            if(data.error.ErrorStatus == 0){
                $("#user-new-loader").css("display","none");
                $('#addUser').modal('hide');
                getUsers();
                $("#user-new-btn-wrap").css("display","block");
                $("#new-user-email").val("");
                $("#new-user-username").val("");
                $("#new-user-fname").val("");
                $("#new-user-sname").val("");
                $("#new-user-password").val("");
            }
            else if(data.error.ErrorStatus == 403){
                window.location.href = "/login/logout";
            }
            else{
                $("#user-new-loader").css("display","none");
                $("#addUser-response").html(data.error.ErrorMessage);
                $("#addUser-response").append(data.error.formerror);
                $("#addUser-response").css("display","block");
                $("#user-new-btn-wrap").css("display","flex");
            }
        })
}


// Get User
function getUser(id){
    $("#user-edit-loader").css("display","flex");
    var data = {userID: id,}

    AjaxProcess("admin/ajaxGetUser", data)
        .done(function(data){
            if(data.error.ErrorStatus == 0){
                $("#edit-user-ajax").html(data.html);
                $("#user-edit-loader").css("display","none");
            }
            else if(data.error.ErrorStatus == 403){
                window.location.href = "/login/logout";
            }
            else{
                $("#user-list-loader").css("display","none");
                $(".page-body").html(data.error.ErrorMessage);
            }
        })
}

// Edit User
function editUser(){

    if($("#edit-user-admin").is(':checked')){
        admin = 2;
    }
    else{admin = 1;}

    if($("#edit-user-enabled").is(':checked')){
        enabled = 1;
    }
    else{enabled = 0;}

    // Prepare Data
    var data = new Array();
    data = {
        userID:         $('#userID').val(),
        username:       $('#edit-user-username').val(),
        email:          $('#edit-user-email').val(),
        forename:       $('#edit-user-fname').val(),
        surname:        $('#edit-user-sname').val(),
        password:       $('#edit-user-password').val(),
        admin:          admin,
        enabled:        enabled
    }

    // Send Data to Backend
    AjaxProcess("admin/ajaxEditUser", data)
        .done(function(data){
            if(data.error.ErrorStatus == 0){
                $('#editUser').modal('hide');
                getUsers();
            }
            else if(data.error.ErrorStatus == 403){
                window.location.href = "/login/logout";
            }
        })
}


// Save holiday changes
function holSave(){
    $("#admin-response").css("display","none");  
    $("#page-body").css("display","none");
    $("#user-list-loader").css("display","flex");

    // Prepare Data
    var data = new Array();
    var count = 0;
    $("#holidaySettings").children('tr').each(function(){
        data[count] = {
            userID:         $(this).attr('id'),
            holallowthis:   $(this).find('#holallowthis').val(),
            holallownext:   $(this).find('#holallownext').val(),
            linemanager:    $(this).find('#line').val(),
            director:       $(this).find('#dir').val(),
            role:           $(this).find('#role').val()
        }
        count++;
    }); 

    // Send Data to Backend
    AjaxProcess("admin/ajaxSaveHoliday", data)
        .done(function(data){
            if(data.error.ErrorStatus == 0){
                
                $("#admin-response").removeClass().addClass("alert alert-success");
                $("#admin-response").html("Saved successfully.");
                
                getUsers('holiday-tab');

                $("#user-list-loader").css("display","none");
                $("#admin-response").css("display","block");  
                $("#page-body").css("display","block");  
            }
            else if(data.error.ErrorStatus == 403){
                window.location.href = "/login/logout";
            }
        })
}


// On Admin Page Load
if(document.URL.indexOf("admin") >= 0){
    getUsers();
    $('#new-user-submit').on('click', function(){newUser();});
    $("body").delegate('#edit-user-submit', 'click', function(){editUser();});
    $("#page-body").delegate('.userRow', 'click', function(){getUser($(this).attr('id'));});
    $("#page-body").delegate('#saveHol', 'click', function(){holSave();});
}