$(document).ready(function(){function e(e,o){var a=window.location,r=a.protocol+"//"+a.host+"/";return $.ajax({type:"POST",async:!0,dataType:"json",url:r+e,headers:{"X-Requested-With":"XMLHttpRequest"},data:{data:o}})}if($(".nav-side-tog").click(function(){$(".sidenav-open").css("width","250px"),$("body").css("background-color","rgba(0,0,0,0.4)")}),$(".sidenav-open .closebtn").click(function(){$(".sidenav-open").css("width","0px"),$("body").css("background-color","white")}),$.urlParam=function(e){return new RegExp("[?&]"+e+"=([^&#]*)").exec(window.location.href)[1]||0},document.URL.indexOf("login")>=0){function o(){""!=$("#InputUsername").val()&&""!=$("#InputPassword").val()?($("#loginMessage").html(""),$("#loginBtn").css("display","none"),$("#login-loader").css("display","flex"),e("login/ajaxLogin",{username:$("#InputUsername").val(),password:$("#InputPassword").val()}).done(function(e){0==e.error.ErrorStatus?window.location.href="/":401==e.error.ErrorStatus?window.location.href="/logout":3==e.error.ErrorStatus&&($("#loginMessage").html("Some of your details were incorrect..."),$("#loginBtn").css("display","block"),$("#login-loader").css("display","none"))})):$("#loginMessage").html("Please provide your username and password...")}$(".login-box").on("click","#loginBtn",function(){o()}),$(".login-box").keypress(function(e){13==e.which&&o()})}function a(o="user-tab"){$("#user-list-loader").css("display","flex"),e("admin/ajaxGetUsers",{tab:o}).done(function(e){0==e.error.ErrorStatus?($("#user-list-loader").css("display","none"),$(".page-body").html(e.html)):403==e.error.ErrorStatus?window.location.href="/login/logout":($("#user-list-loader").css("display","none"),$(".page-body").html(e.error.ErrorMessage))})}function r(){$.ajax({type:"GET",async:!0,dataType:"json",url:"OfficeBooking/changeDate",headers:{"X-Requested-With":"xmlhttprequest"},data:{date:$("#date-book").val()},success:l})}function l(e){if($("#cancel-panel").html(""),$("#weekcontainer").html(""),$("#date-heading").html("Bookings for "+e.date),e.isPast)$("#daycontainer").html("<h5>This date is in the past, please select today or a date in the future</h5>");else if(e.isWeekend)$("#daycontainer").html("<h5>This date is on a weekend, please select a weekday or select 'Whole Week' to book all of the following week</h5>"),$("#weekcontainer").html(e.weekhtml);else if($("#daycontainer").html(e.dayhtml),$("#weekcontainer").html(e.weekhtml),e.userBookings)for(booking of($("#cancel-panel").html(e.cancelhtml),e.cancelData))$("#cancel-"+booking.bookingID).click(s);$("#radio-morn").prop("checked",!1),$("#radio-aftn").prop("checked",!1),$("#radio-aldy").prop("checked",!1),$("#radio-week").prop("checked",!1),$("#radio-morn").prop("disabled",e.mornFull),$("#radio-aftn").prop("disabled",e.aftnFull),$("#radio-aldy").prop("disabled",e.aldyFull),$("#radio-week").prop("disabled",e.isPast),$("#submit").prop("disabled",e.isPast)}function s(e){$.ajax({type:"GET",async:!0,dataType:"json",url:"OfficeBooking/cancel",headers:{"X-Requested-With":"xmlhttprequest"},data:{ID:e.target.getAttribute("cancel")},success:e=>{r(),$("#cancel-message").html(e.cancelMessage),$("#booking-message").html("")}})}function n(){$("#allowance-loader").css("display","flex"),$("#selectyear").prop("disabled",!0),e("holiday/getAllowance",{allowYear:$("#allow_year").val()}).done(function(e){0==e.error.ErrorStatus?($("#allowance-loader").css("display","none"),$("#allowance-wrap").html(e.html),$("#selectyear").prop("disabled",!1)):401==e.error.ErrorStatus?window.location.href="/logout":1==e.error.ErrorStatus&&($("#allowance-loader").css("display","none"),$("#allowance-wrap").html("Error"),$("#selectyear").prop("disabled",!1))})}function t(){$("#calendar-loader").css("display","flex"),e("holiday/getCalendar",{employee:$("#employee").val(),month:$("#calMonth").val(),year:$("#calYear").val()}).done(function(e){0==e.error.ErrorStatus?($("#calendar-loader").css("display","none"),$("#calendar-wrap").html(e.html)):401==e.error.ErrorStatus?window.location.href="/logout":1==e.error.ErrorStatus&&($("#calendar-loader").css("display","none"),$("#calendar-wrap").html("Hello"))})}document.URL.indexOf("admin")>=0&&(a(),$("#new-user-submit").on("click",function(){$("#addUser-response").html(""),$("#addUser-response").css("display","none"),$("#user-new-btn-wrap").css("display","none"),$("#user-new-loader").css("display","flex"),e("admin/ajaxAddUser",{userName:$("#new-user-username").val(),userForename:$("#new-user-fname").val(),userSurname:$("#new-user-sname").val(),userEmail:$("#new-user-email").val(),userPassword:$("#new-user-password").val(),userAdmin:$("#new-user-admin:checked").length}).done(function(e){0==e.error.ErrorStatus?($("#user-new-loader").css("display","none"),$("#addUser").modal("hide"),a(),$("#user-new-btn-wrap").css("display","block"),$("#new-user-email").val(""),$("#new-user-username").val(""),$("#new-user-fname").val(""),$("#new-user-sname").val(""),$("#new-user-password").val("")):403==e.error.ErrorStatus?window.location.href="/login/logout":($("#user-new-loader").css("display","none"),$("#addUser-response").html(e.error.ErrorMessage),$("#addUser-response").append(e.error.formerror),$("#addUser-response").css("display","block"),$("#user-new-btn-wrap").css("display","flex"))})}),$("body").delegate("#edit-user-submit","click",function(){$("#edit-user-admin").is(":checked")?admin=2:admin=1,$("#edit-user-enabled").is(":checked")?enabled=1:enabled=0,new Array,e("admin/ajaxEditUser",{userID:$("#userID").val(),username:$("#edit-user-username").val(),email:$("#edit-user-email").val(),forename:$("#edit-user-fname").val(),surname:$("#edit-user-sname").val(),password:$("#edit-user-password").val(),admin:admin,enabled:enabled}).done(function(e){0==e.error.ErrorStatus?($("#editUser").modal("hide"),a()):403==e.error.ErrorStatus&&(window.location.href="/login/logout")})}),$("#page-body").delegate(".userRow","click",function(){var o;o=$(this).attr("id"),$("#user-edit-loader").css("display","flex"),e("admin/ajaxGetUser",{userID:o}).done(function(e){0==e.error.ErrorStatus?($("#edit-user-ajax").html(e.html),$("#user-edit-loader").css("display","none")):403==e.error.ErrorStatus?window.location.href="/login/logout":($("#user-list-loader").css("display","none"),$(".page-body").html(e.error.ErrorMessage))})}),$("#page-body").delegate("#saveHol","click",function(){!function(){$("#admin-response").css("display","none"),$("#page-body").css("display","none"),$("#user-list-loader").css("display","flex");var o=new Array,r=0;$("#holidaySettings").children("tr").each(function(){o[r]={userID:$(this).attr("id"),holallowthis:$(this).find("#holallowthis").val(),holallownext:$(this).find("#holallownext").val(),linemanager:$(this).find("#line").val(),director:$(this).find("#dir").val(),role:$(this).find("#role").val()},r++}),e("admin/ajaxSaveHoliday",o).done(function(e){0==e.error.ErrorStatus?($("#admin-response").removeClass().addClass("alert alert-success"),$("#admin-response").html("Saved successfully."),a("holiday-tab"),$("#user-list-loader").css("display","none"),$("#admin-response").css("display","block"),$("#page-body").css("display","block")):403==e.error.ErrorStatus&&(window.location.href="/login/logout")})}()})),$("#date-book").change(()=>{r(),$("#booking-message").html("")}),$("#submit").click(e=>{let o=e.target.form;$.ajax({type:"GET",async:!0,dataType:"json",url:"OfficeBooking/book",headers:{"X-Requested-With":"xmlhttprequest"},data:{date:o.datebook.value,time:o.radiotime.value,name:o.textname.value,email:o.textemail.value,behalf:o.checkbox.checked},success:e=>{$("#booking-message").html(e.bookingMessage),console.log(e.email1),console.log(e.email2),r()}})}),$("#checkbox").click(e=>{$("#freelancer-container").css("display",e.target.checked?"block":"none")}),$("#allow_year").change(n),$("#employee").change(t),$("#calMonth").change(t),$("#calYear").change(t);const d=(e,o)=>e.toString().padStart(o,"0");function i(){$("#holidays-loader").css("display","flex");e("holiday/getHolidays",null).done(function(e){0==e.error.ErrorStatus?($("#hol-table-loader").css("display","none"),$("#hol-table-wrap").html(e.html)):401==e.error.ErrorStatus?window.location.href="/logout":5==e.error.ErrorStatus?$("#hol-table-loader").css("display","none"):1==e.error.ErrorStatus&&($("#hol-table-loader").css("display","none"),$("#hol-table-wrap").html("Error"))})}$("#calendar-wrap").delegate("#prevMonth","click",function(){var e=Number.parseInt($("#calMonth").val());if(1==e){$("#calMonth").val("12");let e=Number.parseInt($("#calYear").val());$("#calYear").val(d(e-1,4))}else $("#calMonth").val(d(e-1,2));t()}),$("#calendar-wrap").delegate("#nextMonth","click",function(){var e=Number.parseInt($("#calMonth").val());if(12==e){$("#calMonth").val("01");let e=Number.parseInt($("#calYear").val());$("#calYear").val(d(e+1,4))}else $("#calMonth").val(d(e+1,2));t()});var c=!1;function u(){$("#page-loader").css("display","flex");e("holiday/HolsPending",null).done(function(e){0==e.error.ErrorStatus?($("#page-loader").css("display","none"),$("#pending-requests-wrap").html(e.html)):403==e.error.ErrorStatus?window.location.href="/logout":5==e.error.ErrorStatus&&($("#page-loader").css("display","none"),$("#pending-review-wrap").html("No holidays pending review."))})}function h(){$("#page-loader").css("display","flex"),e("holiday/getAllowances",{year:$("#alow_selected_year").val()}).done(function(e){if(0==e.error.ErrorStatus){var o=(new Date).toLocaleString();$("#allowances-wrap").html("Last updated: "+o),$("#allowances-wrap").append(e.html),$("#page-loader").css("display","none")}else 403==e.error.ErrorStatus?window.location.href="/logout":($("#page-loader").css("display","none"),$("#allowances-wrap").html("Error"))})}function p(){$("#page-loader").css("display","flex"),e("holiday/getAllRequests",{user:$("#selected_employee").val(),year:$("#selected_year").val()}).done(function(e){0==e.error.ErrorStatus?($("#all-requests-wrap").html(e.html),$("#page-loader").css("display","none")):403==e.error.ErrorStatus?window.location.href="/logout":($("#page-loader").css("display","none"),$("#all-requests-wrap").html("Error"))})}$("#formModal").on("click","#submitRequest",function(){$("#req-response").css("display","none"),$("#submitRequest").css("display","none"),$("#req-loader").css("display","flex"),e("holiday/submitHolRequest",{holFrom:$("#holFrom").val(),holFromTime:$("#holFromTime").val(),holTo:$("#multi_day").prop("checked")?$("#holTo").val():$("#holFrom").val(),holToTime:$("#multi_day").prop("checked")?$("#holToTime").val():$("#holFromTime").val(),holRef:$("#holRef").val(),holHalfDays:$("#holHalfDays").val(),forceSubmit:c}).done(function(e){0==e.error.ErrorStatus?($("#holFrom").val(""),$("#holTo").val(""),$("#holFromTime").val("AD"),$("#holToTime").val("AD"),$("#req-response").html("Request Submitted."),$("#req-response").css("background-color","#28a745"),$("#req-response").css("display","block"),$("#req-loader").css("display","none"),$("#submitRequest").css("display","block"),n(),t(),i()):403==e.error.ErrorStatus?window.location.href="/logout":5==e.error.ErrorStatus||50==e.error.ErrorStatus?(50==e.error.ErrorStatus&&(c=!0),$("#req-response").html(e.message),$("#req-response").css("color",""),$("#req-response").css("display","block"),$("#req-loader").css("display","none"),$("#submitRequest").css("display","block")):($("#req-loader").css("display","none"),$("#submitRequest").css("display","block"))})}),$("#formModal").change(function(){$("#from_halfday").prop("checked")?$("#from-time-select").css("display","block"):($("#from-time-select").css("display","none"),$("#holFromTime").val("AD")),$("#multi_day").prop("checked")?($("#from-group").css("display","block"),$("#to-from").html("From:")):($("#from-group").css("display","none"),$("#to-from").html("Date:")),$("#to_halfday").prop("checked")?$("#to-time-select").css("display","block"):($("#to-time-select").css("display","none"),$("#holToTime").val("AD"))}),document.URL.indexOf("holiday/manager")>=0?(u(),h(),p(),$("#content-wrap").delegate(".requestRow","click",function(){var o;o=$(this).attr("id"),$(".modal-body").html(""),$("#responsemsg").css("display","none"),e("holiday/holViewRequest",{reqID:o}).done(function(e){0==e.error.ErrorStatus?($("#page-loader").css("display","none"),$(".modal-body").html(e.html)):403==e.error.ErrorStatus?window.location.href="/logout":($("#page-loader").css("display","none"),$(".modal-body").html("Error"))})}),$(".modal-body").delegate("#respondReq","click",function(){var o,a;o=$(this).attr("data-action"),a=$(this).attr("data-reqid"),e("holiday/actionRequest",{action:o,reqID:a}).done(function(e){0==e.error.ErrorStatus?($("#reviewHolModal").modal("hide"),$("#responsemsg").html("Successfully saved."),$("#responsemsg").css("display","block"),u()):403==e.error.ErrorStatus?window.location.href="/logout":($("#req-loader").css("display","none"),$("#submitRequest").css("display","block"))})}),$("#selected_employee").change(p),$("#selected_year").change(p),$("#alow_selected_year").change(h)):document.URL.indexOf("holiday")>=0&&($(".hol-table-wrap").delegate("#cancelHol","click",function(){var e=$(this).data("holreqid");$("#cancelHolConfirm").data("holreqid",e),console.log($("#cancelHolConfirm").data("holreqid"))}),$("#calendar-wrap").delegate(".active","click",function(){e("holiday/getSelectedHols",{date:$(this).attr("data-date")}).done(function(e){console.log(e),0==e.error.ErrorStatus?($("#hol-table-loader").css("display","none"),$("#viewHolModal-body").html(e.html)):401==e.error.ErrorStatus?window.location.href="/logout":1==e.error.ErrorStatus&&($("#hol-table-loader").css("display","none"),$("#hol-table-wrap").html("Error"))})}),$("#cancelHolModal").on("click","#cancelHolConfirm",function(){console.log("test"),e("holiday/cancelRequest",{holreqID:$("#cancelHolConfirm").data("holreqid")}).done(function(e){0==e.error.ErrorStatus?($("#cancelHolModal").modal("hide"),i(),n(),t()):401==e.error.ErrorStatus?window.location.href="/logout":1==e.error.ErrorStatus&&($("#hol-table-loader").css("display","none"),$("#hol-table-wrap").html("Error"))})}),$("#calMonth").val(d((new Date).getMonth()+1,2)),n(),t(),i())});