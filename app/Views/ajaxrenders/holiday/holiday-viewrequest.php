<?php  //roffy_pre($user); roffy_pre($weekreq); ?>

<h5>Requested Holiday</h5>

<table style="width:100%">

    <tbody>

        <tr>
            <td>Employee:</td>
            <td><?php echo $user->userForename . " " . $user->userSurname; ?></td>
        </tr>
        <tr>
            <td>From:</td>
            <td><?php echo date('d M Y', strtotime($request->holFrom)) . " " . $request->holToTime; ?></td>
        </tr>
        <tr>
            <td>To:</td>
            <td><?php echo date('d M Y', strtotime($request->holTo)) . " " . $request->holFromTime; ?></td>
        </tr>
        <tr>
            <td>Days remaining:<br><sup>(Including this request)</sup></td>
            <td><?php echo $allowance['remaining']; ?></td>
        </tr>


    </tbody>
  
</table>
<p>Request ID: <?php echo $request->holreqID; ?></p>



<h5>Holidays in the week</h5>
<table style="width:100%">

    <thead>
        <tr>
            <th>Employee</th>
            <th>From</th>
            <th>To</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach($weekreq as $req){ ?>

            <tr>
                <td><?php echo $req->user->userForename; ?></td>
                <td><?php echo $req->holFrom; ?></td>
                <td><?php echo $req->holTo; ?></td>
                <td><?php echo $req->holStatus; ?></td>
            </tr>

        <?php } ?>

    </tbody>
  
</table>


<button id="respondReq" type="button" data-action="1" data-reqid="<?php echo $request->holreqID; ?>">Approve</button>
<button id="respondReq" type="button" data-action="0" data-reqid="<?php echo $request->holreqID; ?>">Reject</button>