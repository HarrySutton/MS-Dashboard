<div class="row">
    <div class="col">
        <table class="table" style="width:100%;">

            <tr>
                <th>Employee</th>
                <th>From</th>
                <th>To</th>
                <th>Days Used</th>
                <th>Status</th>
            </tr>

            <?php foreach($rows as $row){$user = $row['user'];?>
              <?php foreach($row['requests'] as $request){ ?>
                <tr class="requestRow" id="<?php echo $request->holreqID; ?>" data-toggle="modal" data-target="#reviewHolModal">
                  <td><?php echo $user->userForename ." ". $user->userSurname; ?></td>
                  <td><?php echo date('d M Y', strtotime($request->holFrom)) ." ". $request->holFromTime; ?></td>
                  <td><?php echo date('d M Y', strtotime($request->holTo)) ." ". $request->holToTime; ?></td>
                  <td><?php echo $request->holDays; ?></td>
                </tr>
              <?php } ?>
            <?php } ?>
        </table>
    </div>
</div>
