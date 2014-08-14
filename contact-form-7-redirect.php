<?php
/*
  Plugin Name: Contact Form 7 Redirect
  Plugin URI: http://wordpress.org/plugins/contact-form-7-addonce/
  Description: This plugin is a addonce of Contact Form 7. After Submittions its make functionality to go a thank you page.
  Author: Anup Biswas @illusivedesign
  Version: 1.6
  Author URI: http://illusivedesign.ca
 */

add_action('wpcf7_admin_after_form', 'wpcf7_addonce_redirect_metabox', 2, 100);
add_action('wpcf7_save_contact_form', 'wpcf7_addonce_redirect_after_save', 2, 100);

function wpcf7_addonce_redirect_metabox($wpcf7) {
    $post_id = $wpcf7->id();
    $redirect_page_id = get_post_meta($post_id, 'wpcf7_' . $post_id . '_redirect', TRUE);
    //echo '<pre>'; print_r($redirect_page_id);echo '</pre>';
    ?>
    <div id="wpcf7_redirect_metabox" class="metabox-holder meta-box-sortables">
        <div class="postbox">
            <div title="Click to toggle" class="handlediv"><br></div>
            <h3 class="hndle"><span>On Successful Submit Redirect</span></h3>
            <div class="inside">                
                <div class="mail-field">
                    <label for="wpcf7-field-redirect-page">Select Page: </label>
                    <select id="wpcf7-field-redirect-page" name="redirect_page">
                        <option value="0">--Select--</option>
                        <?php
                        $pages = get_pages();
                        foreach ($pages as $page) {
                            if ($page->ID == intval($redirect_page_id)) {
                                echo '<option value="' . $page->ID . '" selected="">' . $page->post_title . '</option>';
                            } else {
                                echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
                            }
                        }
                        ?>
                    </select>  

                </div>                
            </div>
        </div>
    </div>    
    <?php
}

function wpcf7_addonce_redirect_after_save($wpcf7) {
    $redirect_page = intval($_POST['redirect_page']);
    $post_id = $wpcf7->id();
    $settings['redirect']['page_id'] = $redirect_page;
    $redirect_page_id = get_post_meta($post_id, 'wpcf7_' . $post_id . '_redirect', TRUE);    
    $properties = $wpcf7->get_properties();
    $adsettings = $properties['additional_settings'];
    $on_sent_ok = strpos($adsettings, 'on_sent_ok');
    if (!empty($adsettings) && isset($on_sent_ok)) {
        $old_url = get_permalink($redirect_page_id);
        $new_url = get_permalink($redirect_page);
        $adsettings = str_replace($old_url, $new_url, $adsettings);        
    } else {
        $adsettings .= 'on_sent_ok: "location = \'' . get_permalink($redirect_page) . '\';"';
    }
    $wpcf7->set_properties(array('additional_settings' => $adsettings));
    update_post_meta($post_id, 'wpcf7_' . $post_id . '_redirect', $redirect_page);
}
