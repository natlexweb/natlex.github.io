$(function() {
  $("#feedback").submit(function(e){
      e.preventDefault(); //prevent default action
      proceed = true;

      $($(this).find("input[data-required=true], textarea[data-required=true]")).each(function(){
              if(!$.trim($(this).val())){ //if this field is empty
                  $(this).css('border-color','red'); //change border color to red
                  proceed = false; //set do not proceed flag
              }
              // check invalid email
              var email_reg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
              if($(this).attr("type")=="email" && !email_reg.test($.trim($(this).val()))){
                  $(this).css('border-color','red'); //change border color to red
                  proceed = false; //set do not proceed flag
              }
      }).on("input", function(){ //change border color to original
           $(this).css('border-color', border_color);
      });

      if(proceed){
          var post_url = $(this).attr("action"); //get form action url
          var request_method = $(this).attr("method"); //get form GET/POST method
          var form_data = new FormData(this); //Creates new FormData object

          $.ajax({ //ajax form submit
              url : "../feedbck.php",
              type: request_method,
              data : form_data,
              dataType : "json",
              contentType: false,
              cache: false,
              processData:false
          }).done(function(res){ //fetch server "json" messages when done
              if(res.type == "error"){
                  $("div").closest(".feedback-error").removeClass('uk-hidden');
                  $("div").closest(".feedback-error").addClass('uk-visible');
                  $("div").closest(".feedback-error").html('<span>'+ res.text +"</span>");
              }

              if(res.type == "done"){
                  $("div").closest(".feedback-results").removeClass('uk-hidden');
                  $("div").closest(".feedback-results").addClass('uk-padding-large uk-label-success');
                  $("div").closest(".feedback").addClass('uk-hidden');
                  $("div").closest(".success-message").html('<span>'+ res.text +"</span>");
                  $('#trainee-apply .uk-close').click(function() {
    location.reload();
});
              }
          });
      }
    });
});
