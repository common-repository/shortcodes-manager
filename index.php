<?php
/**
 * Plugin Name: Shortcodes Manager
 * Plugin URI: http://www.softwarehtec.com/
 * Description: Shortcodes Manager which allow you to enable or disable all or any one of shortcodes you have 
 * Version: 1.0.2
 * Author: softwarehtec.com
 * Author URI: http://www.softwarehtec.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: softwarehtec-shortcodes-manager
 */ 


add_action( 'admin_menu', "shortcodes_manager_menu");

function shortcodes_manager_menu(){
    add_submenu_page("tools.php",'Shortcodes Manager','Shortcodes Manager','administrator',"shortcodes_manager_settings",'shortcodes_manager_settings_page');
}

function shortcodes_manager_settings_page(){


    $o_shortcode_tags = unserialize(get_site_option( 'o_shortcode_tags' ));

    ksort($o_shortcode_tags);

    $result = get_site_option( 'shortcodes_manager' );
    if(empty($result)){
        $result = array();
    }else{
        $result = unserialize($result);
    }

?>
<style type="text/css">
#shortcodes-wrapper .green{
    background: green;
}
#shortcodes-wrapper .red{
    background: red;
}
</style>
    <div class="wrap">
        <h1>Shortcode Manager</h1>
        <div class="card" id="shortcodes-wrapper">
<?php
        if(count($o_shortcode_tags)){
            echo "<table style='border:1px solid black;padding:10px;width:100%;'><tr style='width:100%;display:block;' ><th style='text-align:left;width:80%;display:inline-table;'>Shortcode</th><th style='width:19%;display:inline-table;'>Action</th></tr>";
            foreach($o_shortcode_tags as $k => $s){
                $action_class = "green";
                $action_class_text = "Disable It";
                if(isset($result[$k])){
                    $action_class = "red";
                    $action_class_text = "Enable It";
                }

                echo "<tr style='border-bottom:1px solid black;width:100%;display:block;padding-top:5px;padding-bottom:5px;' ><td  style='text-align:left;width:80%;display:inline-table;' class='shortcodes-wrapper-key' >".$k."</td><td style='width:19%;display:inline-table;text-align:center;'><span title='".$action_class_text."' class='".$action_class." shortcode-actions' style='cursor:pointer;width: 20px;display: inline-block;border-radius: 15px;'  >&nbsp;</span><span style='display: inline-block;float: right;' class='loadding'></span></td></tr>";
            }
            echo "</table>";
        }
?>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery("#shortcodes-wrapper table tr td .shortcode-actions").click(function(){
        var data = "";
        var shortcode_key = jQuery(this).parent().parent().find(".shortcodes-wrapper-key").html()
        if(jQuery(this).hasClass("green")){
            jQuery(this).removeClass("green");
            jQuery(this).addClass("red");
            data = {'action': 'shortcodes_manager','key': shortcode_key,'off':1};
        }else if(jQuery(this).hasClass("red")){
            jQuery(this).removeClass("red");
            jQuery(this).addClass("green");
            data = {'action': 'shortcodes_manager','key': shortcode_key,'on':1};
        }
        var loading = jQuery(this).parent().find(".loadding");

        if(data != ""){
            loading.html("<img src='<?php echo plugins_url( '/default.gif', __FILE__ ) ?>' />");

            jQuery.post(ajaxurl, data, function(response) {
                loading.html("");
            });
        }
    });
});

</script>
        </div>
    </div>
<?php
}

add_action( 'init', 'remove_shortcodes_manager',9999);
function remove_shortcodes_manager() {

    global $shortcode_tags;

    $o_shortcode_tags = serialize($shortcode_tags);
 
    if (! add_site_option( 'o_shortcode_tags', $o_shortcode_tags ) ) {
        update_site_option('o_shortcode_tags', $o_shortcode_tags );
    }


    $result = get_site_option( 'shortcodes_manager' );
    if(empty($result)){
        $result = array();
    }else{
        $result = unserialize($result);
    }

    if(count($result) > 0){
        foreach($result as $k => $v){
            remove_shortcode( $k );
        }
    }

}

add_action( 'wp_ajax_shortcodes_manager', 'shortcodes_manager_ajax' );
function shortcodes_manager_ajax() {

    $key = preg_replace('/[^a-zA-Z0-9_-]/s', '', $_POST["key"] );
    if(empty($key)){
        wp_die();
    }


    $result = get_site_option( 'shortcodes_manager' );
    if(empty($result)){
        $result = array();
    }else{
        $result = unserialize($result);
    }
    
    if($_POST["off"] == 1){
        $result[$key] = 1;
    }
    if($_POST["on"] == 1){
        unset($result[$key]);
    }


    $result = serialize($result);
 
    if (! add_site_option( 'shortcodes_manager', $result ) ) {
        update_site_option('shortcodes_manager', $result );
    }

    wp_die();
}


