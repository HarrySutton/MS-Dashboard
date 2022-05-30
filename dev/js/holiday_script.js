    // Render Holiday Allowance
    function getAllowance(){
        $("#allowance-loader").css("display","flex");
        $('#selectyear').prop('disabled', true);
        var data = {allowYear: $("#allow_year").val()};

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
    $("#allow_year").change(function(){getAllowance();});

    // Render Calendar
    function getCalendar(){
        $("#calendar-loader").css("display","flex");
        var data = {
            employee:   $("#employee").val(),
            month:      $("#calMonth").val(),
            year:       $("#calYear").val()
        }

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

    // Get holidays for selected date
    function getSelectedHols(date){
        //$("#holidays-loader").css("display","flex");
        var data = {date:date}

        AjaxProcess("holiday/getSelectedHols", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#hol-table-loader").css("display","none");
                    $("#viewHolModal-body").html(data.html);
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

    // Submit holiday request
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
                    $("#holFrom").val("");
                    $("#holTo").val("");
                    $("#holFromTime").val("AD");
                    $("#holToTime").val("AD");
                    
                    $("#req-response").html("Request Submitted.");
                    $("#req-response").css("display","block");
                    $("#req-loader").css("display","none");
                    $("#submitRequest").css("display","block");

                    getAllowance();
                    getCalendar();
                    getHolidays();
                    
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


    // Half day check button for holiday request
    $("#formModal").change(function(){
        if($('#from_halfday').prop("checked") == true){
            $("#from-time-select").css("display","block");
        }
        else{
            $("#from-time-select").css("display","none");
            $("#holFromTime option:first").attr("selected", "selected");
        }

        if($('#to_halfday').prop("checked") == true){
            $("#to-time-select").css("display","block");
        }
        else{
            $("#to-time-select").css("display","none");
            $("#holToTime option:first").attr("selected", "selected");
        }
    });

    // Cancel Holiday
    function cancelHol(holreqid){
        var data = {holreqID:holreqid}

        AjaxProcess("holiday/cancelRequest", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $('#cancelHolModal').modal('hide');
                    getHolidays();
                    getAllowance();
                    getCalendar();
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

    // Get pending holidays for managers
    function getPendingHols(){

        $("#page-loader").css("display","flex");

        var data = null;
        AjaxProcess("holiday/HolsPending", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){

                    $("#page-loader").css("display","none");
                    $("#pending-requests-wrap").html(data.html);
                }
                else if(data.error.ErrorStatus == 403){
                    window.location.href = "/logout";
                }
                else if(data.error.ErrorStatus == 5){
                    $("#page-loader").css("display","none");   
                    $("#pending-review-wrap").html("No holidays pending review.");        
                }
            })

    }

    // Get pending holidays for managers
    function viewRequest(reqID){

        //$("#page-loader").css("display","flex");
        $(".modal-body").html("");
        $('#responsemsg').css("display","none");

        var data = {
            reqID:reqID
        }

        AjaxProcess("holiday/holViewRequest", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#page-loader").css("display","none");
                    $(".modal-body").html(data.html);
                }
                else if(data.error.ErrorStatus == 403){
                    window.location.href = "/logout";
                }
                else{
                    $("#page-loader").css("display","none");
                    $(".modal-body").html("Error"); 
                }
            })

    }

    // Get pending holidays for managers
    function actionRequest(action, reqID){

        var data = {
            action: action,
            reqID: reqID
        };

        AjaxProcess("holiday/actionRequest", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $('#reviewHolModal').modal('hide');
                    $('#responsemsg').html('Successfully saved.');
                    $('#responsemsg').css("display","block");
                    getPendingHols();
                }
                else if(data.error.ErrorStatus == 403){
                    window.location.href = "/logout";
                }
                                    
                else{
                    $("#req-loader").css("display","none");
                    $("#submitRequest").css("display","block");   
                }
            })
    }


    // On Holiday Manager Page Load
    if(document.URL.indexOf("holiday/manager") >= 0){ 
        getPendingHols();
        getAllowances()
        getAllRequests();
        $("#content-wrap").delegate('.requestRow', 'click', function(){viewRequest($(this).attr('id'));});
        $(".modal-body").delegate('#respondReq', 'click', function(){actionRequest($(this).attr('data-action'), $(this).attr('data-reqid'));});
        $("#selected_employee").change(function(){getAllRequests();});
        $("#selected_year").change(function(){getAllRequests();});
        $("#alow_selected_year").change(function(){getAllowances();});
    }

    // On Holiday Page Load
    else if(document.URL.indexOf("holiday") >= 0){ 
        
        $(".hol-table-wrap").delegate('#cancelHol', 'click', function(){
            var holreqid = $(this).data('holreqid');
            $('#cancelHolConfirm').data('holreqid', holreqid);
            console.log($('#cancelHolConfirm').data('holreqid'));
        });
        $("#calendar-wrap").delegate('.active', 'click', function(){
            getSelectedHols($(this).attr('data-date'));
        });
        $('#cancelHolModal').on('click','#cancelHolConfirm',function(){console.log('test');cancelHol($('#cancelHolConfirm').data('holreqid'));});
        getAllowance();
        getCalendar();
        getHolidays();
    }

    // Manager - View Allowances
    function getAllowances(){
        $("#page-loader").css("display","flex");

        var data = {year:$("#alow_selected_year").val()};

        AjaxProcess("holiday/getAllowances", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    var time = new Date().toLocaleString();
                    $("#allowances-wrap").html("Last updated: "+time);
                    $("#allowances-wrap").append(data.html);
                    $("#page-loader").css("display","none"); 
                }
                else if(data.error.ErrorStatus == 403){
                    window.location.href = "/logout";
                }
                else{
                    $("#page-loader").css("display","none");
                    $("#allowances-wrap").html("Error");   
                }
            })
    }

    // Manager - View All Requests
    function getAllRequests(){
        $("#page-loader").css("display","flex");

        var data = {
            user:   $("#selected_employee").val(),
            year:   $("#selected_year").val()
        };

        AjaxProcess("holiday/getAllRequests", data)
            .done(function(data){
                if(data.error.ErrorStatus == 0){
                    $("#all-requests-wrap").html(data.html);
                    $("#page-loader").css("display","none"); 
                }
                else if(data.error.ErrorStatus == 403){
                    window.location.href = "/logout";
                }
                                    
                else{
                    $("#page-loader").css("display","none");
                    $("#all-requests-wrap").html("Error");   
                }
            })
    }