<style type="text/css">
    .post-content {
        padding-top: 10px;
        padding-left: 10px;
    }

    .fa-facebook {
        color: #3B5998;
    }

    .fa-linkedin {
        color: #4875B4;
    }
    .fa-twitter {
        color: #00A0D1;
    }
    .post-networks i {
        margin: 0 15px 0 0;
    }
    .col {
        float: left;
        width: 49%;
        margin-left: 2%;
    }
    .col:first-child {
        margin-left: 0;
    }
    .row {
        padding-top: 10px;
    }
    .text-input {
        border: 1px solid #c6c6c6;
        border-top-color: #bbb;
        border-radius: 3px;
    }
    .field-label {
        display: block;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .pb_plugin_image_container {
        position: relative;
    }

    .pb_plugin_image_container .fa {
        position: absolute;
        display: none;
        padding: 10px;
        color: #fff;
        background: rgba(0,0,0,0.6);
        top: 50%;
        font-size:20px;
        margin-top: -10px;
    }

    .pb_plugin_image_container .fa-chevron-left {
            
        left: 0;
    }

    .pb_plugin_image_container .fa-chevron-right {
        
        right: 0;
    }

    #pb_plugin_image {
        width: 100%;
    }
</style>
<div>
    <label id="pb_plugin_validation_failed" style="display: none; color: red;">PostBeyond validation failed, please create post title and select at least one network and one group</label>

    <div class="row">
        <input type="checkbox" id="pb_plugin_publish_to_pb" name="pb_plugin_publish_to_pb" checked>Publish to PostBeyond</input>
    </div>

    <div class="row">
        <label for="myplugin_new_field" class="field-label"><?php echo _e( 'Category', 'pb_plugin_text_domain' );?></label>
        <select id="pb_plugin_category" name="pb_plugin_category" size="1">
            <?php foreach ($categories_result->{objects} as $value) { ?>
                <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="row post-networks">
        <label for="myplugin_new_field" class="field-label"><?php echo _e( 'Networks', 'pb_plugin_text_domain' );?></label>
        <input type="checkbox" id="pb_plugin_twitter" name="pb_plugin_twitter" checked></input><i class="fa fa-twitter"></i>
        <input type="checkbox" id="pb_plugin_facebook" name="pb_plugin_facebook" checked></input><i class="fa fa-facebook"></i>
        <input type="checkbox" id="pb_plugin_linkedin" name="pb_plugin_linkedin" checked></input><i class="fa fa-linkedin"></i>
    </div>

    <div class="row">
        <input type="checkbox" id="pb_plugin_editable" name="pb_plugin_editable">Editable by user</input></br>
    </div>

    <div class="row">
        <label for="pb_plugin_groups" class="field-label">Groups</label>
        <div class="ui-widget">
            <select id="pb_plugin_groups" name="pb_plugin_groups" multiple="true" class="chosen"/>
                <?php foreach ($groups_result->{objects} as $value) { ?>
                    <option value="<?php echo $value->id;?>" <?php echo ($value->name == "All" ? 'selected="selected"' : '');?>"><?php echo $value->name;?></option>
                <?php } ?>
            <input type="hidden" id="pb_plugin_group_ids" name="pb_plugin_group_ids"/>
        </div>
    </div>

    <div class="row">
            <label for="pb_plugin_start" class="field-label"><?php echo _e( 'Start', 'pb_plugin_text_domain' );?></label>
            <input type="text" name="pb_plugin_start" id="datepicker_start" class="text-input"/>
    </div>
    <div class="row">
            <label for="pb_plugin_expiration" class="field-label"><?php echo _e('Expiration', 'pb_plugin_text_domain');?></label>
            <input type="text" name="pb_plugin_expiration" id="datepicker_expiration" class="text-input"/></br>
    </div>

    <div class="row pb_plugin_image_container">       
        <label for="pb_plugin_start" class="field-label"><?php echo _e( 'Media', 'pb_plugin_text_domain' );?></label> 
        <img src="" id="pb_plugin_image"/>
        <a href="#"><i class="fa fa-chevron-left"></i></a>
        <a href="#"><i class="fa fa-chevron-right"></i></a>
    </div>  
    <!-- <div class="row" id="pb_plugin_image_selector_button_container"></div> -->
    <input type="hidden" id="pb_plugin_selected_image_id" name="pb_plugin_selected_image_id"/>
</div>