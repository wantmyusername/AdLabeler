<?php
/*
Plugin Name: AdLabeler - Automatically label your AdSense banners
Description: An intuitive plugin that automatically adds labels like "Ad" or "Advertising" to your AdSense banners, enhancing transparency and user experience.
Author: BunkerLATAM
Author URI: https://bunkerlatam.gumroad.com
Version: 1.0
Text Domain: adlabeler
*/

if (!defined("ABSPATH")) {
    die();
}

define("ADLABELER_PATH", plugin_dir_path(__FILE__));
define("ADLABELER_URI", plugin_dir_url(__FILE__));

// Register plugin settings
add_action("admin_menu", "adlabeler_panel");
add_action("admin_init", "adlabeler_register_settings");

// Register activation and deactivation hooks
register_activation_hook(__FILE__, "adlabeler_on_activate");
register_deactivation_hook(__FILE__, "adlabeler_on_deactivate");

// Add admin notices and inject code to the header
add_action("admin_notices", "adlabeler_show_admin_notice");
add_action("wp_head", "adlabeler_add_code_to_head");

// Define functions
function adlabeler_panel() {
    add_submenu_page(
        "options-general.php",
        "AdLabeler Settings",
        "AdLabeler",
        "manage_options",
        "AdLabeler",
        "adlabeler_settings_page"
    );
}

function adlabeler_register_settings() {
    register_setting('adlabeler_settings_group', 'adlabeler_settings_manual_ads', 'sanitize_text_field');
    register_setting('adlabeler_settings_group', 'adlabeler_settings_auto_ads', 'sanitize_text_field');
    register_setting('adlabeler_settings_group', 'adlabeler_settings_manual_text', 'sanitize_text_field');
    register_setting('adlabeler_settings_group', 'adlabeler_settings_auto_text', 'sanitize_text_field');
}

function adlabeler_settings_page() {
    if (isset($_POST['adlabeler_nonce']) && wp_verify_nonce($_POST['adlabeler_nonce'], 'adlabeler_nonce_action')) {
        $manual_ads = isset($_POST['manual_ads']) ? 'on' : 'off';
        $auto_ads = isset($_POST['auto_ads']) ? 'on' : 'off';

        update_option('adlabeler_settings_manual_ads', $manual_ads);
        update_option('adlabeler_settings_auto_ads', $auto_ads);

        if (isset($_POST['manual_text'])) {
            update_option('adlabeler_settings_manual_text', sanitize_text_field($_POST['manual_text']));
        }
        if (isset($_POST['auto_text'])) {
            update_option('adlabeler_settings_auto_text', sanitize_text_field($_POST['auto_text']));
        }

        update_option('adlabeler_configured', 'on');
    }

    $manual_ads = get_option('adlabeler_settings_manual_ads', 'off');
    $auto_ads = get_option('adlabeler_settings_auto_ads', 'off');
    $manual_text = get_option('adlabeler_settings_manual_text', 'Anuncio');
    $auto_text = get_option('adlabeler_settings_auto_text', 'Anuncio');
    ?>
    <div class="wrap">
        <h2><?php esc_html_e('AdLabeler Settings', 'adlabeler'); ?></h2>
        <form method="post" action="">
            <?php wp_nonce_field('adlabeler_nonce_action', 'adlabeler_nonce'); ?>
            <div class="dashboard-card">
                <h2><?php esc_html_e('General', 'adlabeler'); ?></h2>
                <p><?php esc_html_e('Publishers have two options for how to label AdSense ad units: You can either label the units with "Advertisements" or "Sponsored Links".', 'adlabeler'); ?> <a href="https://support.google.com/adsense/answer/4533986" target="_blank">[1]</a></p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <td>
                                <h3 class="mb-10"><?php esc_html_e('Auto + Manual Ads (Globally)', 'adlabeler'); ?></h3>
                                <p><?php esc_html_e('Status:', 'adlabeler'); ?> <?php echo ($manual_ads === 'on') ? '<b style="color:green">' . esc_html__('Enabled', 'adlabeler') . '</b>' : '<b style="color:red">' . esc_html__('Disabled', 'adlabeler') . '</b>'; ?></p>
                                <p><?php esc_html_e('This option inserts the text into all ads, whether they are automatic ads or manually inserted ads (HTML)', 'adlabeler'); ?></p>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="checkbox" name="manual_ads" value="on" <?php checked($manual_ads, 'on'); ?>>
                                    <input type="text" id="manual_text" name="manual_text" value="<?php echo esc_attr($manual_text); ?>" placeholder="<?php esc_attr_e('Add your text label here. Eg. Advertising', 'adlabeler'); ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3 class="mb-10"><?php esc_html_e('Automatic Ads Only', 'adlabeler'); ?></h3>
                                <p><?php esc_html_e('Status:', 'adlabeler'); ?> <?php echo ($auto_ads === 'on') ? '<b style="color:green">' . esc_html__('Enabled', 'adlabeler') . '</b>' : '<b style="color:red">' . esc_html__('Disabled', 'adlabeler') . '</b>'; ?></p>
                                <p><?php esc_html_e('This is only valid for manually inserted ad blocks (HTML). Text will not be added to automatic ads.', 'adlabeler'); ?></p>                    
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="checkbox" name="auto_ads" value="on" <?php checked($auto_ads, 'on'); ?>>
                                    <input type="text" id="auto_text" name="auto_text" value="<?php echo esc_attr($auto_text); ?>" placeholder="<?php esc_attr_e('Add your text label here. Eg. Advertising', 'adlabeler'); ?>">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
    <style>
        .dashboard-card {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        
        .form-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .form-table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }
        
        .form-table h3, .form-table p {
            margin: 0;
        }
        
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .input-group input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        
        .input-group input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .form-table label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .mb-10 {
            margin-bottom: 10px;
        }
        td {
            width: 50%;
        }       
    </style>
    <?php
}

function adlabeler_on_activate() {
    // Establecer los valores predeterminados al activar el plugin
    add_option('adlabeler_settings_manual_ads', 'off');
    add_option('adlabeler_settings_auto_ads', 'off');
    add_option('adlabeler_settings_manual_text', 'Anuncio');
    add_option('adlabeler_settings_auto_text', 'Anuncio');
    add_option("adlabeler_configured", "off");
}

function adlabeler_on_deactivate() {
    // Eliminar las opciones al desactivar el plugin
    delete_option('adlabeler_settings_manual_ads');
    delete_option('adlabeler_settings_auto_ads');
    delete_option('adlabeler_settings_manual_text');
    delete_option('adlabeler_settings_auto_text');
    delete_option("adlabeler_configured");
}

function adlabeler_show_admin_notice() {
    // Mostrar un aviso en el panel de administración si la configuración no está completa
    if (get_option("adlabeler_configured") !== "on" && @$_GET["page"] !== "AdLabeler") {
        echo '<div class="notice notice-error">
        <p>' . esc_html__('Hello, please finish setting up', 'adlabeler') . ' <strong>' . esc_html__('AdLabeler', 'adlabeler') . '</strong> ' . esc_html__('by', 'adlabeler') . ' <a href="options-general.php?page=AdLabeler" title="' . esc_attr__('Settings', 'adlabeler') . '">' . esc_html__('clicking here', 'adlabeler') . '</a>.</p>
        </div>';
    }
}

function adlabeler_add_code_to_head() {
    // Agregar el código al head del sitio si la configuración está completa
    $manual_ads = get_option("adlabeler_settings_manual_ads", "off");
    $auto_ads = get_option("adlabeler_settings_auto_ads", "off");
    $manual_text = get_option("adlabeler_settings_manual_text", "Anuncio");
    $auto_text = get_option("adlabeler_settings_auto_text", "Anuncio");

    echo '<!-- Code added by: AdLabeler - Automatically label your AdSense banners -->';
    echo '<style>';
    if ($manual_ads === "on") {
        echo '.adsbygoogle { margin-bottom: 50px; }';
        echo '.adsbygoogle::before { content: "' . esc_attr($manual_text) . '"; display: block; text-align: center; font-weight: bold; margin-bottom: 10px; }';
        echo 'ins.adsbygoogle[data-ad-status="unfilled"] { display: none !important; }';
    }
    if ($auto_ads === "on") {
        echo '.google-auto-placed { margin-bottom: 50px; }';
        echo '.adsbygoogle.adsbygoogle-noablate::before { content: "' . esc_attr($auto_text) . '"; display: block; text-align: center; font-weight: bold; margin-bottom: 10px; }';
    }
    echo '</style>';
    echo '<!-- // Code added by: AdLabeler - Automatically label your AdSense banners -->';
}
