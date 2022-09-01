
<div class="container-lg">

    <div class="row">
        <div class="col">
            <h1>Holiday</h1>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-12 col-sm-5 col-md-5 hol-allow-wrap">
            <h2>Allowance</h2>

            <label for="year">Year:</label>
            <select name="year" id="allow_year">
                
                <?php
                    foreach($allowanceYears as $year){ 
                        if($today['year'] == $year){$selected = " selected=selected";}
                        else{$selected="";}
                
                        echo "<option value='".$year."'".$selected.">".$year."</option>";
                    }
                ?>

            </select>

            <div id="allowance-wrap">
                <div id="allowance-loader" class="spinner-wrap" style="display:flex;">
                    <div class="spinner-bg">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="spinner-text">
                        Retrieving Details...
                    </div>
                </div>
            </div>

        </div>
        <div class="col-12 col-sm-7 col-md-7 hol-cal-wrap">

            <h2>Calendar</h2>


            <div id="calendar-wrap">

                <div id="calendar-loader" class="spinner-wrap" style="display:flex;">
                    <div class="spinner-bg">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    </div>
                    <div class="spinner-text">
                        Rendering Calendar...
                    </div>
                </div>

            </div>

            <label for="calMonth">Month:</label>
            <select name="calMonth" id="calMonth">
                
                <?php
                    $i = 1;
                    foreach($months as $month){ 
                        if($today['month'] == $i){$selected = " selected=selected";}
                        else{$selected="";}
                        if(strlen($i) == 1){
                            $mon = "0".$i;
                        }
                        else{$mon=$i;}
                
                        echo "<option value='".$mon."'".$selected.">". $month."</option>";
                        
                        $i++;
                    }
                ?>

            </select>

            <select name="calYear" id="calYear">

                <?php
                    foreach($allowanceYears as $year){ 
                        if($today['year'] == $year){$selected = " selected=selected";}
                        else{$selected="";}
                
                        echo "<option value='".$year."'".$selected.">".$year."</option>";
                    }
                ?>

            </select>

        </div>
    </div>
    <hr>


     <div id="hol-table-wrap" class="hol-table-wrap"></div> 


    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Request Holidays</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="req-response" class="message holrequest" style="display:none;"></div>
                <div class="modal-body">


                    <div class="form-group">
                        <label id="to-from" for="InputUsername">Date:</label>
                        <input id="holFrom" type="date" class="form-control" id="InputUsername">
                    </div>
                    <div class="form-group form-check">

                        <label class="form-check-label" for="from_halfday">
                            <input id="from_halfday" type="checkbox" class="form-check-input">
                            Half day
                        </label>   

                        <br>

                        <label class="form-check-label" for="multi_day">
                            <input id="multi_day" type="checkbox" class="form-check-input">
                            Multiple days
                        </label>  

                    </div>
                    <div id="from-time-select" class="form-group" style="display:none;">
                        <label for="InputUsername">Time:</label>
                        <select id="holFromTime" class="form-control">
                            <option value="AD">All Day</option>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>

                    <hr>

                    <div id="from-group" style="display: none;">
                        <div class="form-group">
                            <label for="InputUsername">To:</label>
                            <input id="holTo" type="date" class="form-control" id="InputUsername">
                        </div>
                        <div class="form-group form-check">
                            <input id="to_halfday" type="checkbox" class="form-check-input">
                            <label class="form-check-label" for="to_halfday">Half day</label>
                        </div>
                        <div id="to-time-select" class="form-group" style="display:none;">
                            <label for="InputUsername">Time:</label>
                            <select id="holToTime" class="form-control">
                                <option value="AD">All Day</option>
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>

                        <hr>

                    </div>



                    <div class="form-group">
                        <label for="InputUsername">Reference:</label>
                        <input id="holRef" type="text" class="form-control" id="InputUsername">
                        <small id="emailHelp" class="form-text text-muted">Only you can see this.</small>
                    </div>

                    
                </div>

                <div class="modal-footer">

                    <button type="button" id="submitRequest">Submit</button>

                    <div id="req-loader" class="spinner-wrap" style="display:none;">
                        <div class="spinner-bg">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="spinner-text">
                            Please Wait...
                        </div>
                    </div>

                </div>                
            </div>
        </div>
    </div>



    <div class="modal fade" id="viewHolModal" tabindex="-1" aria-labelledby="viewHolModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewHolModalLabel">Holidays in the week</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="req-response" class="message holrequest" style="display:none;"></div>
                <div id="viewHolModal-body" class="modal-body"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelHolModal" tabindex="-1" aria-labelledby="cancelHolModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelHolModalLabel">Cancel Holiday</h5>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel the selected holiday?.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="cancelHolConfirm" data-holreqid="null" type="button" class="btn btn-primary">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>