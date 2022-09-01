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