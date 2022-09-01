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