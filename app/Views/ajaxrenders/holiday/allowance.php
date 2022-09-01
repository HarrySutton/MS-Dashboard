<table class="hol-allow">
    <tr>
        <th>Allowance</th>
        <td><?php echo $allowance; ?></td>
    </tr>
    <tr>
        <th>Used</th>
        <td><?php echo $used; ?></td>
    </tr>
    <tr>
        <th>Remaining</th>
        <td><?php echo $remaining; ?></td>
    </tr>
</table>

<button type="button" data-toggle="modal" data-target="#formModal">
    Request Holiday
</button>

<?php if($manager){ ?>
    <button onclick="window.location.href='/holiday/manager'" style="margin-top:20px;">
        Holiday Approval
    </button>
<?php } ?>
