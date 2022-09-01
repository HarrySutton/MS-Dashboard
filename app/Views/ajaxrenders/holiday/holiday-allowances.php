<div class="row">
    <div class="col">
        <table class="table" style="width:100%;">

            <tr>
                <th>Employee</th>
                <th>Allowance</th>
                <th>Used</th>
                <th>Remaining</th>
            </tr>

            <?php foreach($allowances as $row){ ?>

                <tr>
                    <td><?php echo $row['employee']; ?></td>
                    <td><?php echo $row['allowance']; ?>
                    <td><?php echo $row['used']; ?>
                    <td><?php echo $row['remaining']; ?></td>
                </tr>

            <?php } ?>
        </table>
    </div>
</div>