<div class="hol-table-wrap">
    <div class="row">
            <div class="col">
                <h3>Upcoming</h3>
                <table class="table" style="width:100%;">
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Days Used</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th></th>
                    </tr>

                    <?php foreach($upcoming as $row){ 
                        if($row->holStatus == 'cancelled' || $row->holStatus == 'rejected'){$style="style='color:#9c9c9c;'";}
                        else{$style="";}
                        ?>

                        <tr <?php echo $style; ?>>
                            <td><?php echo $row->holFrom; ?></td>
                            <td><?php echo $row->holTo; ?></td>
                            <td><?php echo $row->holDays; ?></td>
                            <td><?php echo $row->holRef; ?></td>
                            <td><?php echo ucfirst($row->holStatus) ?></td>
                            <?php if($row->holStatus != 'cancelled' || $row->holStatus != 'rejected'){ ?>
                                <td id="cancelHol" data-holreqid='<?php echo $row->holreqID; ?>' data-toggle='modal' data-target='#cancelHolModal'>Cancel</td>
                            <?php }else{echo "<td></td>";} ?>
                        </tr>

                    <?php } ?>
                </table>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col">
                <h3>Past</h3>

                <table class="table" style="width:100%;">

                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Days Used</th>
                        <th>Reference</th>
                        <th>Status</th>
                    </tr>

                    <?php foreach($past as $row){ ?>

                        <tr>
                            <td><?php echo $row->holFrom; ?> <?php echo $row->holFromTime; ?></td>
                            <td><?php echo $row->holTo; ?> <?php echo $row->holToTime; ?></td>
                            <td><?php echo $row->holDays; ?></td>
                            <td><?php echo $row->holRef; ?></td>
                            <td><?php echo $row->holStatus ?></td>
                        </tr>

                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>