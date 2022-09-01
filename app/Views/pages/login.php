<div class="login-box">

  <h2>Login</h2>

  <div class="form-group">
    <label for="InputUsername">Username</label>
    <input type="text" class="form-control" id="InputUsername">
  </div>
  
  <div class="form-group">
    <label for="InputPassword">Password</label>
    <input type="password" class="form-control" id="InputPassword">
  </div>

  <div id="login-loader" class="spinner-wrap" style="display:none;">
    <div class="spinner-bg">
      <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    <div class="spinner-text">
      Loading Details...
    </div>
  </div>
  

  <button id="loginBtn" type="button">Submit</button>

  <div id="loginMessage"></div>

</div>