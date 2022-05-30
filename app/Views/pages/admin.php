<div class="container-lg">

    <div class="page-header">
        <h2>Administrator</h2>
    </div>

    <div id="admin-response" class="alert alert-success" role="alert" style="display:none;"></div>

    <div id="user-list-loader" class="spinner-wrap" style="display:flex;">
        <div class="spinner-bg">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="spinner-text">
            Loading...
        </div>
    </div>

    <div id="page-body" class="page-body"></div>

</div>


<!-- Modal -->

<div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserLabel">Add New User</h5>
            </div>

            <div id="addUser-response" class="message holrequest" style="display:none;"></div>
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="new-user-email">Email Address</label>
                    <input type="email" class="form-control form-control-sm" id="new-user-email" aria-describedby="emailHelp">
                </div>
                <div class="form-group">
                    <label for="new-user-username">Username</label>
                    <input type="text" class="form-control form-control-sm" id="new-user-username" aria-describedby="emailHelp">
                </div>
                <div class="form-group">
                    <label for="new-user-fname">Forename</label>
                    <input type="text" class="form-control form-control-sm" id="new-user-fname" aria-describedby="emailHelp">
                </div>
                <div class="form-group">
                    <label for="new-user-sname">Surname</label>
                    <input type="text" class="form-control form-control-sm" id="new-user-sname" aria-describedby="emailHelp">
                </div>
                <div class="form-group">
                    <label for="new-user-password">Password</label>
                    <input type="password" class="form-control" id="new-user-password">
                </div>
                <div class="form-group form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" id="new-user-admin" value='no'>
                    <label class="form-check-label" for="new-user-admin">Admin</label>
                </div>
            </div>
            
            <div id="user-new-btn-wrap" class="modal-footer">
                <button type="button" data-dismiss="modal">Cancel</button>
                <button id="new-user-submit" type="button" >Add User</button>
            </div>

            <div id="user-new-loader" class="spinner-wrap" style="display:none;">
                <div class="spinner-bg">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="spinner-text">
                    Please Wait...
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editUser" tabindex="-1" aria-labelledby="editUserLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="edit-user-ajax"></div>
    
            <div id="user-edit-loader" class="spinner-wrap" style="display:flex;">
                <div class="spinner-bg">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="spinner-text">
                    Please Wait...
                </div>
            </div>

        </div>
    </div>
</div>