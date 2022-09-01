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

        <?php foreach($weekreq as $req){ 
                if($req->holStatus == 'cancelled'){$style="style='color:#9c9c9c;'";}
                else{$style="";}
            ?>
            

            <tr <?php echo $style; ?>>
                <td><?php echo $req->user->userForename." ".$req->user->userSurname; ?></td>
                <td><?php echo $req->holFrom; ?></td>
                <td><?php echo $req->holTo; ?></td>
                <td><?php echo ucfirst($req->holStatus); ?></td>
            </tr>

        <?php } ?>

    </tbody>
  
</table>
