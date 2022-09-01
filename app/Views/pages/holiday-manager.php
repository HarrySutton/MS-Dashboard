
<div class="container-lg">

    <div class="row">
        <div class="col-12">
            <h1>Holiday Approval</h1>
        </div>
        <div class="col-12">
            <button onclick="window.location.href='/holiday'" style="margin-top:20px;">
                Back
            </button>
        </div>

        
    </div>

    <hr>



    <div class="row">
      <div id="responsemsg" class="alert alert-warning" role="alert" style="display:none;">
        A simple danger alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
      </div>
    </div>

    <div id="content-wrap" >

      <div class="row">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="pending-review-wrap-tab" data-toggle="pill" href="#pending-review-wrap" role="tab" aria-controls="pending-review-wrap" aria-selected="true">Pending Review</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="allowance-wrap-tab" data-toggle="pill" href="#allowance-wrap" role="tab" aria-controls="allowance-wrap" aria-selected="true">Allowances</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">All Requests</a>
          </li>
        </ul>
      </div>


      <div class="tab-content row" id="pills-tabContent">

        <!-- PENDING REQUESTS -->
        <div class="tab-pane fade show active" style="width:100%;" id="pending-review-wrap" role="tabpanel" aria-labelledby="pending-review-wrap-tab">
          <div id="pending-requests-wrap"></div>
        </div>

        <!-- ALLOWANCES -->
        <div class="tab-pane fade" style="width:100%;" id="allowance-wrap" role="tabpanel" aria-labelledby="allowance-wrap-tab">
            <div class="row" id="all-req-filter">            
              <div class="col-12">
                <label for="year">Year:</label>
                <select name="year" id="alow_selected_year">
                  <?php
                      foreach($dates['allowanceYears'] as $year){ 
                          if($dates['today']['year'] == $year){$selected = " selected=selected";}
                          else{$selected="";}
                          echo "<option value='".$year."'".$selected.">".$year."</option>";
                      }
                  ?>
                </select>
              </div>
            </div>
          <div id="allowances-wrap"></div>
        </div>

        <!-- ALL REQUESTS -->
        <div class="tab-pane fade" style="width:100%;" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
          <div class="row" id="all-req-filter">
            <div class="col-4">
              <label for="employee">Employee:</label>
              <select name="employee" id="selected_employee">
                <option value="all">All</option>
                <?php  
                  foreach($users as $user){
                    echo "<option value='".$user->userID."'>".$user->userForename." ".substr($user->userSurname, 0, 1)."</option>";
                  } 
                ?>
              </select>
            </div>

            <div class="col-4">
              <label for="year">Year:</label>
              <select name="year" id="selected_year">
                <?php
                    foreach($dates['allowanceYears'] as $year){ 
                        if($dates['today']['year'] == $year){$selected = " selected=selected";}
                        else{$selected="";}
                        echo "<option value='".$year."'".$selected.">".$year."</option>";
                    }
                ?>
              </select>
            </div>
          </div> <!-- Request Filter Wrap -->
          
          
          <div id="all-requests-wrap">
            <!-- ALL REQUESTS -->
          </div>

        </div>
      </div>

      <div class="row">

        

      <div id="page-loader" class="spinner-wrap" style="display:flex;">
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

</div>





<div class="modal fade" id="reviewHolModal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalLabel">Review Request</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>