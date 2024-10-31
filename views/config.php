<?php if ("Save Changes" == $_POST["submit"]) {
    # Should go to configuration file
    $url = POSTBEYOND_PLUGIN_API_HOST . '/auth/sign-in?token=' . $_POST["pb_token"];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
     
    $result = curl_exec($ch);

    $httpCode = curl_getinfo ($ch, CURLINFO_HTTP_CODE);

    curl_close ($ch);
}
?>
<h2>PostBeyond Plugin Settings</h2>
<form name="postbeyond_token_config" action="<?php echo admin_url( 'options-general.php' ).'?page=postbeyond-token-config'; ?>" method="POST">
    <p>
        Please request API token from PostBeyond. For more info contact <a href="mailto:info@postbeyond.com">info@postbeyond.com</a>
    </p>
    <?php

    if (200 == $httpCode) {
        // received a valid token, lets save it
        update_option('postbeyond_token', $_POST["pb_token"]);
        echo '<p><label style="color: green;">Token validated, now you can proceed with using PostBeyond plugin. </label></p>';
    }
    else {
        echo '<p><label style="color: red;">Token update failed, please provide valid token to proceed. </label></p>';
    }

    ?>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row">
                    <label for="pb_token">API Token</label>
                </th>
                <td>
                    <input type="text" size="100" name="pb_token" value="<?php echo get_option('postbeyond_token', '');?>"/>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'postbeyond');?>">
    </p>    
</form>