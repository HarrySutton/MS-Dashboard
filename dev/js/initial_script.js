function AjaxProcess(url, data){
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/";

    return $.ajax({
    type: "POST",
    async: true,
    dataType: "json",
    url: baseUrl+url,
    headers: {'X-Requested-With': 'XMLHttpRequest'},
    
    data:{
        data:data,
    }
    });
}

// Open Side Nav
$(".nav-side-tog").click(function(){
    $(".sidenav-open").css("width", "250px");
    //$(".content").css("margin-left", "250px");
    $("body").css("background-color", "rgba(0,0,0,0.4)");
});

// Close Side Nav
$(".sidenav-open .closebtn").click(function(){
    $(".sidenav-open").css("width", "0px");
    //$(".content").css("margin-left", "160px");
    $("body").css("background-color", "white");
});

$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results[1] || 0;
};

// Login Page Only
if(document.URL.indexOf("login") >= 0){ 

    // Login User
    function loginUser(){
        if($("#InputUsername").val() != '' && $("#InputPassword").val() != ''){

            $("#loginMessage").html("");
            $("#loginBtn").css("display","none");
            $("#login-loader").css("display","flex");
            
            var data = {
                username: $("#InputUsername").val(),
                password: $("#InputPassword").val()
            }

            AjaxProcess("login/ajaxLogin", data)
                .done(function(data){
                    if(data.error.ErrorStatus == 0){
                        window.location.href = "/";
                    }
                    else if(data.error.ErrorStatus == 401){
                        window.location.href = "/logout";
                    }
                    else if(data.error.ErrorStatus == 3){
                        $("#loginMessage").html("Some of your details were incorrect...");
                        $("#loginBtn").css("display","block");
                        $("#login-loader").css("display","none");
                    }
                })

        }
        else{
            $("#loginMessage").html("Please provide your username and password...");
        }
    }

    $('.login-box').on('click','#loginBtn',function(){loginUser();});
    $(".login-box").keypress(function(e){if(e.which == 13){loginUser();}});
}