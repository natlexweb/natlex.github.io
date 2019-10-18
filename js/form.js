var allowed_file_size = "10485760";
var allowed_files = [
    'image/png',
    'image/gif',
    'image/jpeg',
    'image/pjpeg',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'text/plain',
    'application/vnd.oasis.opendocument.text'];
var border_color = "#C2C2C2"; //initial input border color

$(function() {

    $('.job-type-pm').click(function() {
        $("#jobtype").val('Project-manager');
    });
    $('.job-type-tester').click(function () {
        $("#jobtype").val('QA-engineer');
    });
    $('.job-type-devops').click(function () {
        $("#jobtype").val('Devops-engineer');
    });
    $('.job-type-backend').click(function () {
        $("#jobtype").val('Backend-developer');
    });
    $(".contactus-form").submit(function(e){
        e.preventDefault(); //prevent default action
        proceed = true;

        //simple input validation
        $($(this).find("input[data-required=true], textarea[data-required=true]")).each(function(){
                if(!$.trim($(this).val())){ //if this field is empty
                    $(this).css('border-color','red'); //change border color to red
                    proceed = false; //set do not proceed flag
                }
        }).on("input", function(){ //change border color to original
             $(this).css('border-color', border_color);
        });

        //check file size and type before upload, works in modern browsers
        if(window.File && window.FileReader && window.FileList && window.Blob){
            var total_files_size = 0;
            $(this.elements['file_attach[]'].files).each(function(i, ifile){
                if(ifile.value !== ""){ //continue only if file(s) are selected
                    if(allowed_files.indexOf(ifile.type) === -1){ //check unsupported file
                        alert( ifile.name + " - не поддерживаем такой формат, извините!");
                        proceed = false;
                    }
                 total_files_size = total_files_size + ifile.size; //add file size to total size
                }
            });
//            $('.filesize').html(total_files_size + 'bytes');
           if(total_files_size > allowed_file_size){
                alert( "Пожалуйста, убедитесь, что размер загруженного файла меньше 10 Мегабайт.");
                proceed = false;
            }
        }

        //if everything's ok, continue with Ajax form submit
        if(proceed){
            var post_url = $(this).attr("action"); //get form action url
            var request_method = $(this).attr("method"); //get form GET/POST method
            var form_data = new FormData(this); //Creates new FormData object

            $.ajax({ //ajax form submit
                url : "../contact_me.php",
                type: request_method,
                data : form_data,
                dataType : "json",
                contentType: false,
                cache: false,
                processData:false
            }).done(function(res){ //fetch server "json" messages when done
                if(res.type == "error"){
                    $(".contactus-error").removeClass('uk-hidden');
                    $(".contactus-error").addClass('uk-visible');
                    $(".contactus-error").html('<span>'+ res.text +"</span>");
                }

                if(res.type == "done"){
                    $(".contactus-form").addClass('uk-hidden');
                    $(".contactus-results").removeClass('uk-hidden');
                    $(".contactus-results").addClass('uk-padding-large uk-label-success');
                    $(".contactus-results").html('<span>'+ res.text +"</span>");
                }
            });
        }
    });
});
