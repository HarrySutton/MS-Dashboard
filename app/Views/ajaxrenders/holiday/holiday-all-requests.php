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

            <?php foreach($requests as $row){ ?>

                <tr>
                    <td><?php echo $row['employee']; ?></td>
                    <td><?php echo $row['holFrom']; ?> <?php echo $row['holFromTime']; ?></td>
                    <td><?php echo $row['holTo']; ?> <?php echo $row['holToTime']; ?></td>
                    <td><?php echo $row['holDays']; ?></td>
                    <td><?php echo $row['holStatus']; ?></td>
                </tr>

            <?php } ?>
        </table>
    </div>
</div>