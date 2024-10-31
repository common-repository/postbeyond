<?php
/**
 * @package PostBeyond
 */
/*
Plugin Name: PostBeyond
Plugin URI: https://postbeyond.com
Description: Official PostBeyond Employee Advocacy platform integration plugin. Connect your PostBeyond account and push content to your hub right from WordPress
Version: 1.0.0
Author: PostBeyond
Author URI: https://postbeyond.com
License: GPLv2 or later
Text Domain: postbeyond
*/

define("POSTBEYOND_PLUGIN_API_HOST", "https://app.postbeyond.com/api/v1");
define("POSTBEYOND_PLUGIN_SETTINGS_PB_TOKEN", "postbeyond_token");

add_action("publish_post", "postbeyond_plugin_create_post", 10, 2);
add_action("admin_init", "postbeyond_plugin_register_mysettings");
add_action("admin_menu", "postbeyond_plugin_register_settings_menu");
add_action("admin_notices", "postbeyond_plugin_display_notice");
add_action("add_meta_boxes", "postbeyond_plugin_add_meta_box");
add_action("admin_enqueue_scripts", "postbeyond_plugin_enqueue_scripts_and_css");

add_filter("plugin_action_links_" . plugin_basename( plugin_dir_path( __FILE__ ) . "postbeyond_content_creator.php"), "postbeyond_plugin_add_plugin_settings_link");

function postbeyond_plugin_configure_pb_token() {
    include plugin_dir_path( __FILE__ ) . "views/config.php";
};

function postbeyond_plugin_register_settings_menu() {
    add_options_page(__("PostBeyond Settings", "pb-plugin"), "PostBeyond", "manage_options", "postbeyond-token-config", "postbeyond_plugin_configure_pb_token");
};

function postbeyond_plugin_add_plugin_settings_link($links) {
    $url = admin_url( "options-general.php" ) . "?page=postbeyond-token-config";
    $settings_link = "<a href=\"" . esc_url($url) . "\">" . __("Settings", "pb-plugin") . "</a>";
    array_unshift($links, $settings_link); 
    return $links;
};

function postbeyond_plugin_display_notice() {
    global $hook_suffix;

    if ("plugins.php" == $hook_suffix) {
        $token = get_option(POSTBEYOND_PLUGIN_SETTINGS_PB_TOKEN, NULL);
        if (NULL == $token) {
            include plugin_dir_path( __FILE__ ) . "views/notice.php";
        }
    }    
}

function postbeyond_plugin_register_mysettings() {
    register_setting("postbeyond-settings-group", POSTBEYOND_PLUGIN_SETTINGS_PB_TOKEN);
}

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function postbeyond_plugin_add_meta_box($hook) {

    $screens = array("post", "page");

    foreach ($screens as $screen) {

        add_meta_box(
            "pb_plugin_postbeyond_options_metabox",
            __("PostBeyond Post Content", "pb_plugin_text_domain"),
            "postbeyond_plugin_render_meta_box_content",
            $screen,
            "side",
            "high"
        );
    }
}

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function postbeyond_plugin_render_meta_box_content($post) {

    $post_status = get_post_status(get_the_ID());
    
    if ($post_status == "publish") {
        $value = get_post_meta(get_the_ID(), "pb_plugin_post_created", true);
        if ("" == $value) {
            echo "<label style=\"color: red;\">This post was not created in PostBeyond.</label>";
        }
        else {
            echo "<label style=\"color: green;\">This post was created in PostBeyond as well.</label>";
        }
        return;
    }

    # Our new data
    $token = get_option(POSTBEYOND_PLUGIN_SETTINGS_PB_TOKEN);
    if (NULL == $token) {
        echo "<label style=\"color: red;\">Please provide valid token in order to proceed with PostBeyond post creation.</label> ";
        $url = admin_url( "options-general.php" ) . "?page=postbeyond-token-config";
        echo "<a href=\"" . esc_url($url) . "\">" . __("Settings", "pb-plugin") . "</a>";
        return;
    }
    $url = POSTBEYOND_PLUGIN_API_HOST . "/category?token=" . $token;
     
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
    $json_categories_result = curl_exec($ch);
    $categories_result = json_decode($json_categories_result);

    $url = POSTBEYOND_PLUGIN_API_HOST . "/group?token=" . $token;
     
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
    $json_groups_result = curl_exec($ch);
    $groups_result = json_decode($json_groups_result);

    include plugin_dir_path(__FILE__) . "views/post.php";

}

function postbeyond_plugin_create_post($ID, $post) {
    // Check that Publish to PostBeyond checkbox is checked, if not do not post to postbeyond
    if ("on" != $_POST["pb_plugin_publish_to_pb"]) {
        return;
    }

    $image_id_regex = '.wp-image-\d+.';

    $image_id = NULL;
    if (preg_match($image_id_regex, $_POST["pb_plugin_selected_image_id"], $matchers)) {
        $image_id = preg_replace("/wp-image-/", "", $matchers[0]);
    }

    $post_info = array(
        "title" => $post->post_title, 
        "content" => $post->post_title . " " . get_permalink($ID),
        "media" => $image_id ? wp_get_attachment_image_src($image_id, "full")[0] : NULL,
        "category" => $_POST["pb_plugin_category"],
        "facebook" => "on" == $_POST["pb_plugin_facebook"],
        "linkedin" => "on" == $_POST["pb_plugin_linkedin"],
        "twitter" => "on" == $_POST["pb_plugin_twitter"],
        "editable" => "on" == $_POST["pb_plugin_editable"],
        "groups" => array_map(function ($id) { return array("id" => $id); }, json_decode($_POST["pb_plugin_group_ids"]))
    );

    if ($_POST["pb_plugin_start"]) {
        $post_info["start"] = $_POST["pb_plugin_start"];
    }

    if ($_POST["pb_plugin_expiration"]) {
        $post_info["expiration"] = $_POST["pb_plugin_expiration"];
    }

    # Should go to configuration file
    $url = POSTBEYOND_PLUGIN_API_HOST . "/post?token=" . get_option(POSTBEYOND_PLUGIN_SETTINGS_PB_TOKEN);

    $data_string = json_encode($post_info);   
     
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Content-Length: " . strlen($data_string))
    );
     
    $result = curl_exec($ch);

    $httpCode = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);

    if (200 == $httpCode) {
        // save this to post data
        update_post_meta($ID, "pb_plugin_post_created", true);
    }
}

function postbeyond_plugin_enqueue_scripts_and_css($hook) {
    // filter based on post status rather than on the page that we are at
    $post_status = get_post_status(get_the_ID());
    if (!in_array($post_status, array("draft", "pending", "auto-draft", "future"))) {
        return;
    }

    wp_enqueue_style("jquery-ui",       plugin_dir_url( __FILE__ ) . "css/jquery-ui.css");
    wp_enqueue_style("font-awesome",    plugin_dir_url( __FILE__ ) . "css/font-awesome.min.css");
    wp_enqueue_style("chosen-css",      plugin_dir_url( __FILE__ ) . "css/chosen.css");
    wp_enqueue_script("chosen-script",  plugin_dir_url( __FILE__ ) . "scripts/chosen.jquery.min.js");
    wp_enqueue_script("pb_script",      plugin_dir_url( __FILE__ ) . "scripts/pb_scripts.js", array("jquery-ui-datepicker", "jquery-ui-autocomplete"));
}

?>