

<!-- Tab Buttons -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link<?php echo $userbtn; ?>" id="user-tab" data-toggle="tab" href="#user" role="tab" aria-controls="user" aria-selected="true">Users</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link<?php echo $holbtn; ?>" id="holiday-tab" data-toggle="tab" href="#holiday" role="tab" aria-controls="holiday" aria-selected="false">Holiday</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Settings</a>
    </li>
</ul>


<!-- Tab Content -->
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade<?php echo $usertab; ?>" id="user" role="tabpanel" aria-labelledby="user-tab">

            <button type="button" data-toggle="modal" data-target="#addUser">New User</button>
        

            <table class="table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Permissions</th>
                    </tr>
                </thead>
                <tbody>

                <?php 
                    foreach($users as $user){
                        $disabled = array();

                        //if($user['userActive']){ ?>

                            <tr id="<?php echo $user['userID']; ?>" class="userRow" data-toggle="modal" data-target="#editUser">
                                <td><?php echo $user['userName']; ?></td>
                                <td><?php echo $user['userForename'] . " " . $user['userSurname']; ?></td>
                                <td><?php 
                                    if($user['userPermiss'] == 1){echo 'User';}
                                    elseif($user['userPermiss'] == 2){echo 'Admin';}
                                ?></td>
                            </tr>
                            
                        <?php //}
                        
                        // else{
                        //     $disabled[] = $user;
                        // }
                    } 
                ?>
                
                </tbody>
            </table>


    </div>
    <div class="tab-pane fade<?php echo $holtab; ?>" id="holiday" role="tabpanel" aria-labelledby="holiday-tab">

            <button id="saveHol" type="button" >Save Changes</button>

            <table class="table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Holiday<br><?php echo date("Y"); ?></th>
                        <th>Holiday<br><?php echo date("Y", strtotime('+1 years')); ?></th>
                        <th>Line Manager</th>
                        <th>Director</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody id="holidaySettings">

                <?php 
                    foreach($users as $user){ 
                        $disabled = array();

                        //if($user['userActive']){ 
                            
                            if(!isset($user['holallow'][date("Y")])){$user['holallow'][date("Y")] = null;}
                            if(!isset($user['holallow'][date("Y", strtotime('+1 years'))])){$user['holallow'][date("Y", strtotime('+1 years'))] = null;}
                            
                            ?>

                            <tr id="<?php echo $user['userID']; ?>">
                                <td>
                                    <?php echo $user['userForename']; ?>
                                </td>
                                <td>
                                    <input class="holallowinput" type="number" id="holallowthis" name="holallow" min="20" max="50" value="<?php echo $user['holallow'][date("Y")]; ?>">
                                </td>
                                <td>
                                    <input class="holallowinput" type="number" id="holallownext" name="holallow" min="20" max="50" value="<?php echo $user['holallow'][date("Y", strtotime('+1 years'))]; ?>">
                                </td>
                                <td>
                                    <select name="line" id="line">
                                        
                                    <?php

                                        // Display who's assigned in dropdown
                                        if($user['holrole']['holLine'] != null){
                                            foreach($holmanagers as $holman){
                                                if($user['holrole']['holLine'] == $holman['userID']){
                                                    $Assigned = $holman['userID'];
                                                    echo "<option value=".$holman['userID'].">".$holman['userForename']."</option>";
                                                    echo "<option value='null'>Unassign</option>";
                                                    foreach($holmanagers as $holman){
                                                        if($Assigned == $holman['userID']){}
                                                        else{echo "<option value=".$holman['userID'].">".$holman['userForename']."</option>";}   
                                                    }
                                                    //break;
                                                }
                                            }
                                        }
                                        // Display all
                                        else{
                                            echo "<option value='null'>Unassigned</option>";
                                            foreach($holmanagers as $holman){
                                                echo "<option value=".$holman['userID'].">".$holman['userForename']."</option>";
                                            } 
                                        }


                                    ?>


                                    </select>
                                </td>
                                <td>
                                    <select name="dir" id="dir">


                                        <?php

                                            // Display who's assigned in dropdown
                                            if($user['holrole']['holDir'] != null){
                                                foreach($holmanagers as $holman){
                                                    if($user['holrole']['holDir'] == $holman['userID']){
                                                        $Assigned = $holman['userID'];
                                                        echo "<option value=".$holman['userID'].">".$holman['userForename']."</option>";
                                                        echo "<option value='null'>Unassign</option>";
                                                        foreach($holmanagers as $holman){
                                                            if($Assigned == $holman['userID']){}
                                                            else{echo "<option value=".$holman['userID'].">".$holman['userForename']."</option>";}   
                                                        }
                                                        //break;
                                                    }
                                                }
                                            }
                                            // Display all
                                            else{
                                                echo "<option value='null'>Unassigned</option>";
                                                foreach($holmanagers as $holman){
                                                    echo "<option value=".$holman['userID'].">".$holman['userForename']."</option>";
                                                } 
                                            }
                                        
                                        
                                        ?>


                                    </select>
                                </td>
                                <td>
                                    <select name="role" id="role">
                                        <?php if($user['holrole']['holRole'] == 1){ ?>
                                            <option value="0">Standard</option>
                                            <option value="1" selected>Manager</option>
                                        <?php }else{ ?>
                                            <option value="0" selected>Standard</option>
                                            <option value="1">Manager</option>
                                        <?php } ?>
                                        
                                    </select>
                                </td>
                            </tr>
                            
                        <?php //}
                        
                    } 
                ?>
                
                </tbody>
            </table>

    </div>
    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">




    </div>
</div>









<?php if(!empty($disabled)){ ?>

    <div class="accordion" id="accordionExample">
        <div class="card">
            <div class="card-header" id="headingOne">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Disabled Users
                </button>
            </h2>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
                <table class="table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Permissions</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php foreach($disabled as $user){ ?>

                        <tr>
                            <td><?php echo $user->userName; ?></td>
                            <td><?php echo $user->userForename . " " . $user->userSurname; ?></td>
                            <td><?php 
                                if($user->userPermiss == 1){echo 'User';}
                                elseif($user->userPermiss == 2){echo 'Admin';}
                            ?></td>
                        </tr>
                                
                    <?php } ?>
                    
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>

<?php } ?>