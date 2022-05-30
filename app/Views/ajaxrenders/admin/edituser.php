
<div class="modal-header">
    <h5 class="modal-title" id="editUserLabel">Edit User: <?php echo $user->userForename; ?></h5>
</div>

<div class="modal-body">
    <input type="hidden" id="userID" name="userID" value="<?php echo $user->userID; ?>">
    <div class="form-group">
        <label for="edit-user-email">Email Address</label>
        <input type="email" class="form-control form-control-sm" id="edit-user-email" aria-describedby="emailHelp" value="<?php echo $user->userEmail; ?>">
    </div>
    <div class="form-group">
        <label for="edit-user-username">Username</label>
        <input type="text" class="form-control form-control-sm" id="edit-user-username" aria-describedby="emailHelp" value="<?php echo $user->userName; ?>">
    </div>
    <div class="form-group">
        <label for="edit-user-fname">Forename</label>
        <input type="text" class="form-control form-control-sm" id="edit-user-fname" aria-describedby="emailHelp" value="<?php echo $user->userForename; ?>">
    </div>
    <div class="form-group">
        <label for="edit-user-sname">Surname</label>
        <input type="text" class="form-control form-control-sm" id="edit-user-sname" aria-describedby="emailHelp" value="<?php echo $user->userSurname; ?>">
    </div>
    <div class="form-group">
        <label for="edit-user-password">Change Password</label>
        <input type="password" class="form-control" id="edit-user-password">
    </div>
    <div class="form-group form-check form-check-inline">
        <?php if($user->userPermiss == 2){$checked = "checked";}else{$checked = "";}?>
        <input type="checkbox" class="form-check-input" id="edit-user-admin" <?php echo $checked; ?>>
        <label class="form-check-label" for="edit-user-admin">Admin</label>
    </div>
    <div class="form-group form-check form-check-inline">
        <?php if($user->userActive){$checked = "checked";}else{$checked = "";}?>
        <input type="checkbox" class="form-check-input" id="edit-user-enabled" <?php echo $checked; ?>>
        <label class="form-check-label" for="edit-user-enabled">Enabled</label>
    </div>

</div>

<div id="user-edit-btn-wrap" class="modal-footer">
    <button type="button" data-dismiss="modal">Close</button>
    <button id="edit-user-submit" type="button">Save Changes</button>
</div>