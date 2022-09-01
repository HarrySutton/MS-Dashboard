$(document).ready(function(){
function AjaxProcess(url, data){
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/";

    return $.ajax({
    type: "POST",
    async: true,
    dataType: "json",
    url: baseUrl+url,
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
// WHEN DATE INPUT IS CHANGED, LOADS DATA FOR NEW DATE AND CLEAR THE PREVIOUS BOOKING MESSAGE
$("#date-book").change(() => {
    bookingLoadDate();
    $("#booking-message").html("")
});

// GET REQUIRED DATA FOR NEW DATE AND DISPLAY IT 
function bookingLoadDate(){
    $.ajax({
        type: "GET",
        async: true,
        dataType: "json",
        url: "OfficeBooking/changeDate",
        headers: {"X-Requested-With": "xmlhttprequest"},
        data:{
            "date": $("#date-book").val()
        },
        success: bookingDisplay
    })
};

// DISPLAY THE DATA FETCHED BY bookingLoadDate
function bookingDisplay(data){
    $("#cancel-panel").html("");
    $("#weekcontainer").html("");
    $("#date-heading").html("Bookings for " + data.date);

    if (data.isPast){
        $("#daycontainer").html("<h5>This date is in the past, please select today or a date in the future</h5>");

    }else if (data.isWeekend){
        $("#daycontainer").html("<h5>This date is on a weekend, please select a weekday or select 'Whole Week' to book all of the following week</h5>")
        $("#weekcontainer").html(data.weekhtml);

    }else{
        $("#daycontainer").html(data.dayhtml);
        $("#weekcontainer").html(data.weekhtml);

        if (data.userBookings){
            $("#cancel-panel").html(data.cancelhtml);
            for (booking of data.cancelData){
                $("#cancel-" + booking.bookingID).click(bookingCancel)
            }
        }
    };
    $("#radio-morn").prop("checked", false);
    $("#radio-aftn").prop("checked", false);
    $("#radio-aldy").prop("checked", false);
    $("#radio-week").prop("checked", false);

    $("#radio-morn").prop("disabled", data.mornFull);
    $("#radio-aftn").prop("disabled", data.aftnFull);
    $("#radio-aldy").prop("disabled", data.aldyFull);
    $("#radio-week").prop("disabled", data.isPast);
    $("#submit").prop("disabled", data.isPast);
};

function bookingCancel(event){
    $.ajax({
        type: "GET",
        async: true,
        dataType: "json",
        url: "OfficeBooking/cancel",
        headers: {"X-Requested-With": "xmlhttprequest"},
        data:{
            "ID": event.target.getAttribute("cancel")
        },
        success: (data) => {
            bookingLoadDate();
            $("#cancel-message").html(data.cancelMessage);
            $("#booking-message").html("")
        }
    })
};

// ADDS EVENT LISTENER TO SUBMIT BUTTON TO CREATE BOOKING
$("#submit").click((event) => {
    let form = event.target.form;
    $.ajax({
        type: "GET",
        async: true,
        dataType: "json",
        url: "OfficeBooking/book",
        headers: {"X-Requested-With": "xmlhttprequest"},
        data:{
            "date": form.datebook.value,
            "time": form.radiotime.value,
            "name": form.textname.value,
            "email": form.textemail.value,
            "behalf": form.checkbox.checked
        },
        success: (data) => {
            $("#booking-message").html(data.bookingMessage);
            console.log(data.email1);
            console.log(data.email2);
            bookingLoadDate();
        }
    })
});

// ADDS EVENT LISTENER TO FREELANCER CHECKBOX TO SHOW OR HIDE THE TEXT INPUTS WHEN CLICKED
$("#checkbox").click((event) => {
    $("#freelancer-container").css("display", (event.target.checked)? "block": "none")
})
// Render Holiday Allowance
function getAllowance() {
    $("#allowance-loader").css("display", "flex");
    $('#selectyear').prop('disabled', true);
    var data = { allowYear: $("#allow_year").val() };

    AjaxProcess("holiday/getAllowance", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                $("#allowance-loader").css("display", "none");
                $("#allowance-wrap").html(data.html);
                $('#selectyear').prop('disabled', false);
            }
            else if (data.error.ErrorStatus == 401) {
                window.location.href = "/logout";
            }
            else if (data.error.ErrorStatus == 1) {
                $("#allowance-loader").css("display", "none");
                $("#allowance-wrap").html("Error");
                $('#selectyear').prop('disabled', false);
            }
        })
}
$("#allow_year").change(getAllowance);

// Render Calendar
function getCalendar() {
    $("#calendar-loader").css("display", "flex");
    var data = {
        employee: $("#employee").val(),
        month: $("#calMonth").val(),
        year: $("#calYear").val()
    }

    AjaxProcess("holiday/getCalendar", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                $("#calendar-loader").css("display", "none");
                $("#calendar-wrap").html(data.html);
            }
            else if (data.error.ErrorStatus == 401) {
                window.location.href = "/logout";
            }
            else if (data.error.ErrorStatus == 1) {
                $("#calendar-loader").css("display", "none");
                $("#calendar-wrap").html("Hello");
            }
        })
}

// Render Calendar when Dropdowns change
$("#employee").change(getCalendar);
$("#calMonth").change(getCalendar);
$("#calYear").change(getCalendar);

const formatVal = (val, len) => val.toString().padStart(len, "0")

// Previous Month Button
$("#calendar-wrap").delegate('#prevMonth', 'click', function () {
    var month = Number.parseInt($('#calMonth').val());

    if (month == 1) {
        $("#calMonth").val("12");

        let year = Number.parseInt($('#calYear').val());
        $('#calYear').val(formatVal(year - 1, 4));

    }else{
        $("#calMonth").val(formatVal(month - 1, 2));
    }

    getCalendar();
});

// Next Month Button
$("#calendar-wrap").delegate('#nextMonth', 'click', function () {
    var month = Number.parseInt($('#calMonth').val());

    if (month == 12) {
        $("#calMonth").val("01");

        let year = Number.parseInt($('#calYear').val());
        $('#calYear').val(formatVal(year + 1, 4));

    }else{
        $("#calMonth").val(formatVal(month + 1, 2))
    }

    getCalendar();
});

// Get holidays for selected date
function getSelectedHols(date) {
    //$("#holidays-loader").css("display","flex");
    var data = { date: date }
    AjaxProcess("holiday/getSelectedHols", data)
        .done(function (data) {
            console.log(data)
            if (data.error.ErrorStatus == 0) {
                $("#hol-table-loader").css("display", "none");
                $("#viewHolModal-body").html(data.html);
            }
            else if (data.error.ErrorStatus == 401) {
                window.location.href = "/logout";
            }
            else if (data.error.ErrorStatus == 1) {
                $("#hol-table-loader").css("display", "none");
                $("#hol-table-wrap").html("Error");
            }
        })
}

// Render Holiday Table
function getHolidays() {
    $("#holidays-loader").css("display", "flex");
    var data = null
    AjaxProcess("holiday/getHolidays", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                $("#hol-table-loader").css("display", "none");
                $("#hol-table-wrap").html(data.html);
            }
            else if (data.error.ErrorStatus == 401) {
                window.location.href = "/logout";
            }
            else if (data.error.ErrorStatus == 5) {
                $("#hol-table-loader").css("display", "none");
            }
            else if (data.error.ErrorStatus == 1) {
                $("#hol-table-loader").css("display", "none");
                $("#hol-table-wrap").html("Error");
            }
        })
}

// Submit holiday request
var forceSubmit = false;
function requestHoliday() {
    $("#req-response").css("display", "none");
    $("#submitRequest").css("display", "none");
    $("#req-loader").css("display", "flex");

    var data = {
        holFrom:        $("#holFrom").val(),
        holFromTime:    $("#holFromTime").val(),
        holTo:          $('#multi_day').prop("checked")? $("#holTo").val() :     $("#holFrom").val(),
        holToTime:      $('#multi_day').prop("checked")? $("#holToTime").val() : $("#holFromTime").val(),
        holRef:         $("#holRef").val(),
        holHalfDays:    $("#holHalfDays").val(),
        forceSubmit:    forceSubmit
    }

    AjaxProcess("holiday/submitHolRequest", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                $("#holFrom").val("");
                $("#holTo").val("");
                $("#holFromTime").val("AD");
                $("#holToTime").val("AD");

                $("#req-response").html("Request Submitted.");
                $("#req-response").css("background-color", "#28a745");
                $("#req-response").css("display", "block");
                $("#req-loader").css("display", "none");
                $("#submitRequest").css("display", "block");

                getAllowance();
                getCalendar();
                getHolidays();

            }
            else if (data.error.ErrorStatus == 403) {
                window.location.href = "/logout";
            }
            else if (data.error.ErrorStatus == 5 || data.error.ErrorStatus == 50) {

                if (data.error.ErrorStatus == 50) {
                    forceSubmit = true;
                }
                $("#req-response").html(data.message);
                $("#req-response").css("color", "");
                $("#req-response").css("display", "block");
                $("#req-loader").css("display", "none");
                $("#submitRequest").css("display", "block");
            }
            else {

                $("#req-loader").css("display", "none");
                $("#submitRequest").css("display", "block");
            }
        })
}
$('#formModal').on('click', '#submitRequest', requestHoliday);


// Half day check button for holiday request
$("#formModal").change(function () {

    if ($('#from_halfday').prop("checked")) {
        $("#from-time-select").css("display", "block");
    }
    else {
        $("#from-time-select").css("display", "none");
        $("#holFromTime").val("AD");
    }

    if ($('#multi_day').prop("checked")) {
        $("#from-group").css("display", "block");
        $("#to-from").html("From:")
    }
    else {
        $("#from-group").css("display", "none");
        $("#to-from").html("Date:")
    }

    if ($('#to_halfday').prop("checked")) {
        $("#to-time-select").css("display", "block");
    }
    else {
        $("#to-time-select").css("display", "none");
        $("#holToTime").val("AD")
    }
});

// Cancel Holiday
function cancelHol(holreqid) {
    var data = { holreqID: holreqid }

    AjaxProcess("holiday/cancelRequest", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                $('#cancelHolModal').modal('hide');
                getHolidays();
                getAllowance();
                getCalendar();
            }
            else if (data.error.ErrorStatus == 401) {
                window.location.href = "/logout";
            }
            else if (data.error.ErrorStatus == 1) {
                $("#hol-table-loader").css("display", "none");
                $("#hol-table-wrap").html("Error");
            }
        })
}

// Get pending holidays for managers
function getPendingHols() {

    $("#page-loader").css("display", "flex");

    var data = null;
    AjaxProcess("holiday/HolsPending", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {

                $("#page-loader").css("display", "none");
                $("#pending-requests-wrap").html(data.html);
            }
            else if (data.error.ErrorStatus == 403) {
                window.location.href = "/logout";
            }
            else if (data.error.ErrorStatus == 5) {
                $("#page-loader").css("display", "none");
                $("#pending-review-wrap").html("No holidays pending review.");
            }
        })

}

// Get pending holidays for managers
function viewRequest(reqID) {

    //$("#page-loader").css("display","flex");
    $(".modal-body").html("");
    $('#responsemsg').css("display", "none");

    var data = {
        reqID: reqID
    }

    AjaxProcess("holiday/holViewRequest", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                $("#page-loader").css("display", "none");
                $(".modal-body").html(data.html);
            }
            else if (data.error.ErrorStatus == 403) {
                window.location.href = "/logout";
            }
            else {
                $("#page-loader").css("display", "none");
                $(".modal-body").html("Error");
            }
        })

}

// Get pending holidays for managers
function actionRequest(action, reqID) {

    var data = {
        action: action,
        reqID: reqID
    };

    AjaxProcess("holiday/actionRequest", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                $('#reviewHolModal').modal('hide');
                $('#responsemsg').html('Successfully saved.');
                $('#responsemsg').css("display", "block");
                getPendingHols();
            }
            else if (data.error.ErrorStatus == 403) {
                window.location.href = "/logout";
            }

            else {
                $("#req-loader").css("display", "none");
                $("#submitRequest").css("display", "block");
            }
        })
}


// On Holiday Manager Page Load
if (document.URL.indexOf("holiday/manager") >= 0) {
    getPendingHols();
    getAllowances()
    getAllRequests();
    $("#content-wrap").delegate('.requestRow', 'click', function () { viewRequest($(this).attr('id')); });
    $(".modal-body").delegate('#respondReq', 'click', function () { actionRequest($(this).attr('data-action'), $(this).attr('data-reqid')); });
    $("#selected_employee").change(getAllRequests);
    $("#selected_year").change(getAllRequests);
    $("#alow_selected_year").change(getAllowances);
}

// On Holiday Page Load
else if (document.URL.indexOf("holiday") >= 0) {

    $(".hol-table-wrap").delegate('#cancelHol', 'click', function () {
        var holreqid = $(this).data('holreqid');
        $('#cancelHolConfirm').data('holreqid', holreqid);
        console.log($('#cancelHolConfirm').data('holreqid'));
    });
    $("#calendar-wrap").delegate('.active', 'click', function () {
        getSelectedHols($(this).attr('data-date'));
    });
    $('#cancelHolModal').on('click', '#cancelHolConfirm', function () { console.log('test'); cancelHol($('#cancelHolConfirm').data('holreqid')); });

    $("#calMonth").val(formatVal(new Date().getMonth() + 1, 2));

    getAllowance();
    getCalendar();
    getHolidays();
}

// Manager - View Allowances
function getAllowances() {
    $("#page-loader").css("display", "flex");

    var data = { year: $("#alow_selected_year").val() };

    AjaxProcess("holiday/getAllowances", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                var time = new Date().toLocaleString();
                $("#allowances-wrap").html("Last updated: " + time);
                $("#allowances-wrap").append(data.html);
                $("#page-loader").css("display", "none");
            }
            else if (data.error.ErrorStatus == 403) {
                window.location.href = "/logout";
            }
            else {
                $("#page-loader").css("display", "none");
                $("#allowances-wrap").html("Error");
            }
        })
}

// Manager - View All Requests
function getAllRequests() {
    $("#page-loader").css("display", "flex");

    var data = {
        user: $("#selected_employee").val(),
        year: $("#selected_year").val()
    };

    AjaxProcess("holiday/getAllRequests", data)
        .done(function (data) {
            if (data.error.ErrorStatus == 0) {
                $("#all-requests-wrap").html(data.html);
                $("#page-loader").css("display", "none");
            }
            else if (data.error.ErrorStatus == 403) {
                window.location.href = "/logout";
            }

            else {
                $("#page-loader").css("display", "none");
                $("#all-requests-wrap").html("Error");
            }
        })
}
});