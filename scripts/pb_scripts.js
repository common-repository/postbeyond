jQuery(document).ready(function($) { 

    // Datepickers - BEGIN
    $("#datepicker_start").datepicker({
        todayHighlight: true,
        minDate: 0,
        changeMonth: true,
        onClose: function( selectedDate ) {
            $( "#datepicker_expiration" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $("#datepicker_expiration").datepicker({
        defaultDate: "+1m",
        minDate: 0,
        changeMonth: true,
        onClose: function( selectedDate ) {
            $( "#datepicker_start" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
    // Datepickers - END

    // Groups Chosen - BEGIN
    var tmp_array = [], chosen_val;
    var pb_plugin_create_chosen_group_ids = function () {
        tmp_array = [];
        chosen_val = $(".chosen").val();
        if (chosen_val) {
            $.each(chosen_val, function(ind, str_group_id) {
                tmp_array.push(Number(str_group_id));
            });
        }

        $("#pb_plugin_group_ids").val(JSON.stringify(tmp_array));
    };

    $(".chosen").chosen({
        width: "75%",
        search_contains: true,
        display_selected_options: false,
        allow_single_deselect: true,
        placeholder_text_multiple: "Select Group"
    }).on("change", function(evt, params) {
        pb_plugin_create_chosen_group_ids();
    });

    pb_plugin_create_chosen_group_ids();
    // Groups Chosen - END

    // Validation section - BEGIN
    var pb_plugin_validate_inputs = function() {
        if (!$("#pb_plugin_publish_to_pb").is(":checked")) {
            $("#pb_plugin_validation_failed").hide();
        } else {
            // publish to postbeyond is checked, lets validate
            // title is not empty
            if (!$("#title").val()) {
                // DEBUG - console.log("Title");
                $("#pb_plugin_validation_failed").show();
                return;
            }
            
            // network checkboxes
            if (!($("#pb_plugin_facebook").is(":checked") || $("#pb_plugin_linkedin").is(":checked") || $("#pb_plugin_twitter").is(":checked"))) {
                // DEBUG - console.log("Networks");
                $("#pb_plugin_validation_failed").show();
                return;
            }

            $("#pb_plugin_validation_failed").hide();

            // groups
            if (!$("#pb_plugin_groups").val()) {
                // DEBUG - console.log("Groups");
                $("#pb_plugin_validation_failed").show();
                return;
            }
        }
    };
    setInterval(pb_plugin_validate_inputs, 500);
    // Validation section - END

    // Image processing - BEGIN
    var pb_plugin_images_objects = [];
    var pb_plugin_get_image_src_and_attributes = function () {
        pb_plugin_images_objects = [];
        $("#content_ifr").contents().find("img").each(function(ind, el) {
            pb_plugin_images_objects.push({src: $(this).attr("src"), cls: $(this).attr("class")});
        });
    };

    var get_current_image_index_by_src = function(){
        for(var i = 0; i < pb_plugin_images_objects.length; i++){
            if ($("#pb_plugin_image").attr("src") == pb_plugin_images_objects[i].src){
                return i;
            }
        }
    }

    $(".pb_plugin_image_container .fa-chevron-left").click(function(e){
        e.preventDefault();
        var currentIndex = get_current_image_index_by_src(),
            nextIndex = currentIndex - 1;

        if (nextIndex < 0){
            nextIndex = pb_plugin_images_objects.length - 1;
        }

        $("#pb_plugin_image").attr("src", pb_plugin_images_objects[nextIndex].src);
        $("#pb_plugin_selected_image_id").val(pb_plugin_images_objects[nextIndex].cls);
    });

    $(".pb_plugin_image_container .fa-chevron-right").click(function(e){
        e.preventDefault();
        var currentIndex = get_current_image_index_by_src(),
            nextIndex = currentIndex + 1;

        if (nextIndex > pb_plugin_images_objects.length - 1){
            nextIndex = 0;
        }

        $("#pb_plugin_image").attr("src", pb_plugin_images_objects[nextIndex].src);
        $("#pb_plugin_selected_image_id").val(pb_plugin_images_objects[nextIndex].cls);
    });



    var pb_plugin_process_content_images = function() {
        pb_plugin_get_image_src_and_attributes();

        if (pb_plugin_images_objects.length < 1) {
            $("#pb_plugin_image").attr("src", "");
            $("#pb_plugin_selected_image_id").val(null);
            $(".pb_plugin_image_container .fa").hide();
            return;
        }

        if (pb_plugin_images_objects.length > 1) {
            $(".pb_plugin_image_container .fa-chevron-left, .pb_plugin_image_container .fa-chevron-right").show();
        } else {
            $(".pb_plugin_image_container .fa-chevron-left, .pb_plugin_image_container .fa-chevron-right").hide();
        }

        $(".pb_plugin_image_container .fa-times").show();        

        if (!$("#pb_plugin_image").attr("src")) {
            $("#pb_plugin_image").attr("src", pb_plugin_images_objects[0]["src"]);
            $("#pb_plugin_selected_image_id").val(pb_plugin_images_objects[0]["cls"]);
        }
    };
    setInterval(pb_plugin_process_content_images, 1000);
    // Image processing - END

});