<?php

/**
 * Plugin Name: SVGCaptcha
 * Plugin URI: http://incolumitas.com/SVGCaptcha
 * Description: This plugin generates SVG captchas and displays them on the login form and comment forms. You can customize the inner working of SVGCaptcha exhaustively and you can react to spamming attacks with a increased difficulty for instance.
 * Version: 0.2
 * Author: Nikolai Tschacher
 * Author URI: http://incolumitas.com/about
 * License: A "Slug" license name e.g. GPL2
 */
/*  Copyright 2013  Nikolai Tschacher  (email : admin *[at]* incolumitas.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class SVGCaptchaWordpressPlugin {

    /**
     * The current administration menu options.
     */
    private $captcha_options;

    /**
     * The SVGCaptcha instance.
     */
    private $svgCaptcha;

    /**
     * SVGCaptcha data (SVG string)
     */
    private $svg_output;

    /**
     * SVGCaptcha answer;
     */
    private $captcha_answer;

    /**
     * Disable that if you use this plugin on your own server.
     * Own my site for example, I need to style the form for the horizontal bootstrap 3 form 
     */
    const CUSTOM_FORM_STYLE = true;

    /**
     *  Should we consider the case of the captcha? 
     */
    private $case_sensitive = true;

    /**
     * Start up.
     */
    public function __construct() {
        session_start();

        if (!class_exists('SVGCaptcha')) {
            include_once("SVGCaptcha.php"); // include the captcha lib
        }

        // Define the default settings:
        // Settings are divided in sections. Each section
        // consists of fields. Eeach session field consists of a key and two values: 
        // 0: The default value,
        // 1: all possible values (Can be an array, if no default value exists/makes sense this element must be null)
        // 2: and the field title
        // 3: and which callback to use. The callback then receives the whole field array as arg.
        $this->dsettings = Array(
            'general_settings' => array(
                'description' => 'SVGCaptcha basic settings',
                'sd' => array('captcha_difficulty' => array('easy', array('easy', 'medium', 'hard'), "Captcha difficulty", 'select_callback'), // The SVGCaptcha difficulty
                    'captcha_length' => array(4, 5, "Length of the captcha", 'text_callback'), // The captcha length in chars.
                    'captcha_border' => array(4, 'border: 1px solid 0f0;', "CSS code for the captcha border", 'text_callback'), // The captcha border. CSS property.
                    'captcha_width' => array(300, null, 'The width of the captcha in pixels', 'text_callback'),
                    'captcha_height' => array(130, null, 'The height of the captcha in pixels', 'text_callback'),
                    'enable_captcha_on_comments' => array(True, null, "Whether to enable the captcha on comment form", 'checkbox_callback'),
                    'enable_captcha_on_login' => array(True, null, "Whether to enable the captcha on the login form", 'checkbox_callback'),
                    'captcha_case_sensitive' => array(False, null, 'If the captcha is case sensitive (AbCd != ABcD)', 'checkbox_callback'),
                    'captcha_preview' => array(True, null, "<hr><h3><strong>Preview</strong> for the current settings.</h3><hr>" . $this->svgc_reload_link(), 'captcha_preview_callback'))),
            'custom_captcha_settings' => array(
                'description' => 'Use a custom captcha instead of a predefined (This overwrites the difficulty level set above)',
                'sd' => array(
                    'custom_captcha' => array(False, null, "<strong>Enable</strong> user defined captchas by the settings below. The easy/medium/hard difficulty levels are disabled!", 'checkbox_callback'), // If set to true, SVGCaptcha will use a user defined captcha.
                    // This values are the parameters for the user defined captcha image.
                    'cg_glyph_offsetting' => array(False, null, "If the captcha should use glyph offsetting as a obfuscation technique", 'checkbox_callback'),
                    'cg_glyph_fragments' => array(False, null, "If the captcha should use glyph fragments to distort the image", 'checkbox_callback'),
                    'cg_transformations' => array(False, null, "If affine transformationsn should be enabled", 'checkbox_callback'),
                    'cg_approx_shapes' => array(False, null, "Whether to approximate shapes", 'checkbox_callback'),
                    'cg_change_degree' => array(False, null, "If the captcha library should change the degree of splines.", 'checkbox_callback'),
                    'cg_split_curve' => array(False, null, "Whether to split curves as a distortion technique", 'checkbox_callback'),
                    'cg_shapeify' => array(False, null, "Inject randomly generated shapes into the captcha", 'checkbox_callback')))
        );
        // Lableify all descriptions :/
        foreach ($this->dsettings as $key => $value) {
            foreach ($value["sd"] as $id => $option) {
                $this->dsettings[$key]["sd"][$id][2] = '<label for="' . $id . '">' . $option[2] . '</label>';
            }
        }

        $this->captcha_options = get_option('svgc_options');
        $this->case_sensitive = $this->captcha_options["captcha_case_sensitive"];

        $this->hook2wp();
    }

    private function hook2wp() {
        // Enqueue and register ajax script for reload captcha capability
        wp_register_script('captcha-reload', plugin_dir_url(__FILE__) . 'js/reload_captcha.js', array('jquery'));
        wp_enqueue_script('captcha-reload');
        // code to declare the URL to the file handling the AJAX request
        wp_localize_script('captcha-reload', 'myAjaxObject', array('ajaxurl' => admin_url('admin-ajax.php')));

        // Add ajax reload link handler
        add_action('wp_ajax_nopriv_svgc_captcha_reload', array($this, 'svgc_captcha_reload'));
        add_action('wp_ajax_svgc_captcha_reload', array($this, 'svgc_captcha_reload'));


        add_filter('comment_form_defaults', array($this, 'svgc_comment_form_defaults'));
        // Add a filter to verify if the captch in the comment section was correct.
        add_filter('preprocess_comment', array($this, 'svgc_validate_comment_captcha'));

        // Ad custom captcha field to login form
        add_action('login_form', array($this, 'svgc_login_form_defaults'));
        // Validate captcha in login form.
        add_filter('authenticate', array($this, 'svgc_validate_login_captcha'), 30, 3);

        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Create the captcha and store the encrypted solution in a hidden field.
     * Alternatives:
     * Use a session variable (Bad: Uses files = slow). Maybe the best.
     * Use add_option() [Using a database] (Bad: Needs to be written to = slow). Maybe usable.
     * Use a encrypted hidden field. Bad, unsecure.
     * Use a cookie. (Bad: Can't make it working with wordpress.)
     * 
     * @param type $default
     * @return string
     */
    public function svgc_comment_form_defaults($default) {
        if (!is_admin()) {
            $this->svgc_get_captcha();
            if (self::CUSTOM_FORM_STYLE === True) {
                $default['fields']['email'] .=
                        '<div class="form-group">
                        <label class="col-sm-2 control-label" for="svgc_answer">' . __('Captcha', 'SVGCaptcha') . '<span class="required"> *</span></label>
                        <div class="col-sm-10">
                                <input id="svgc_answer" class="form-control" name="svgc_answer" size="30" type="text" />
                                <div id="SVGCaptchaContainer">' . $this->svg_output . '</div>
                                ' . $this->svgc_reload_link() . '
                        </div>
                    </div>';
            } else {
                $default['fields']['email'] .=
                        '<p class="comment-form-captcha"><label for="svgc_answer">' . __('Captcha', 'SVGCaptcha') . '</label>
                    <span class="required">*</span>
                    <input id="svgc_answer" name="svgc_answer" size="30" type="text" />
                    <div id="SVGCaptchaContainer">' . $this->svg_output . '</div>
                    ' . $this->svgc_reload_link();
            }
        }
        return $default;
    }

    public function svgc_validate_comment_captcha($commentdata) {
        if (!is_admin()) { /* Admins excluded. They should't be prevented from spamming... */
            if (empty($_POST['svgc_answer']))
                wp_die(__('Error: You need to enter the captcha.', 'SVGCaptcha'));

            $answer = strip_tags($_POST['svgc_answer']);

            if (!$this->svgc_check($answer))/* Case insensitive comparing */
                wp_die(__('Error: Your supplied captcha is incorrect.', 'SVGCaptcha'));
        }
        return $commentdata;
    }

    public function svgc_login_form_defaults() {
        if (!is_admin()) {
            $this->svgc_get_captcha();
            //Get and set any values already sent
            $user_captcha = ( isset($_POST['svgc_answer']) ) ? $_POST['svgc_answer'] : '';
            ?>

            <div class="form-group">
                <div id="SVGCaptchaContainer"><?php echo $this->svg_output ?></div>
                <?php echo $this->svgc_reload_link(); ?>
                <label for="svgc_answer" class="col-sm-2 control-label"><?php _e('Captcha', 'SVGCaptcha') ?><span class="required"> *</span></label>
                <input type="text" name="svgc_answer" id="svgc_answer" class="form-control" value="<?php echo esc_attr(stripslashes($user_captcha)); ?>" size="25" />
            </div>

            <?php
        }
    }

    public function svgc_validate_login_captcha($user, $username, $password) {
        if (!is_admin()) { /* Whenever a admin tries to login -.- */
            if (empty($_POST['svgc_answer'])) {
                return new WP_Error('invalid_captcha', __("You need to enter a captcha in order to login.", 'SVGCaptcha'));
            } else {
                $answer = strip_tags($_POST['svgc_answer']);

                if (!$this->svgc_check($answer, $solution)) {/* Case insensitive comparing */
                    return new WP_Error('invalid_captcha', __("Your supplied captcha is incorrect.", 'SVGCaptcha'));
                } else {
                    return $user;
                }
            }
        }
    }

    /**
     * Returns html that provides a capability to reload the current captcha via ajax.
     * 
     * https://codex.wordpress.org/AJAX_in_Plugins
     */
    public function svgc_reload_link() {
        return '<a id="svgc-reload" style="cursor: pointer">reload captcha</a>';
    }

    public function svgc_captcha_reload() {
        if ($_REQUEST["reload"] == "reload") {
            $this->svgc_get_captcha();
            echo $this->svg_output;
        }
        die();
    }

    /**
     * Checks whether the user provided answer is correct.
     */
    public function svgc_check($answer) {
        $this->captcha_answer = $_SESSION['svgc_solution'];

        if ($this->case_sensitive) {
            return (strcmp($answer, $this->captcha_answer) == 0) ? True : False;
        } else {
            return (strcasecmp($answer, $this->captcha_answer) == 0) ? True : False;
        }
    }

    /**
     * Choses a random captcha from the pool and returns the corresponding image path.
     * Sets global variable $captcha_value to the value (The solution the user has to enter)
     * of the captcha.
     */
    public function svgc_get_captcha() {
        // and immediately create a instance determined by the specified settings (if given) or else by the default variables.

        $lu = array('easy' => SVGCaptcha::EASY, 'medium' => SVGCaptcha::MEDIUM, 'hard' => SVGCaptcha::HARD);

        if (!isset($this->captcha_options) || empty($this->captcha_options)) {
            // Use default settings
            $this->svgCaptcha = SVGCaptcha::getInstance(
                            $this->dsettings["general_settings"]["sd"]["captcha_length"][0], $width = $this->dsettings["general_settings"]["sd"]["captcha_width"][0], $height = $this->dsettings["general_settings"]["sd"]["captcha_height"][0], $difficulty = $lu[$this->dsettings["general_settings"]["sd"]["captcha_difficulty"][0]]
            );
        } else {
            // Check if we have a custom specified captcha or a predefined one (easy/medium/hard)
            if ($this->captcha_options["custom_captcha"] == True) {
                $custom_settings = array(
                    'glyph_offsetting' => array('apply' => False, 'h' => 1, 'v' => 0.5, 'mh' => 8), // Needs to be anabled by default
                    'glyph_fragments' => array('apply' => False, 'r_num_frag' => range(0, 6), 'frag_factor' => 2),
                    'transformations' => array('apply' => False, 'rotate' => True, 'skew' => True, 'scale' => True, 'shear' => False, 'translate' => True),
                    'approx_shapes' => array('apply' => False, 'p' => 3, 'r_al_num_lines' => range(10, 30)),
                    'change_degree' => array('apply' => False, 'p' => 5),
                    'split_curve' => array('apply' => False, 'p' => 5),
                    'shapeify' => array('apply' => False, 'r_num_shapes' => range(0, 6), 'r_num_gp' => range(4, 10))
                );
                foreach ($custom_settings as $key => $value) {
                    $custom_settings[$key]["apply"] = $this->captcha_options["cg_" . $key];
                }

                $this->svgCaptcha = SVGCaptcha::getInstance(
                                $this->captcha_options['captcha_length'], $width = $this->captcha_options['captcha_width'], $height = $this->captcha_options['captcha_height'], $difficulty = $custom_settings
                );
            } else {
                $this->svgCaptcha = SVGCaptcha::getInstance(
                                $this->captcha_options['captcha_length'], $width = $this->captcha_options['captcha_width'], $height = $this->captcha_options['captcha_height'], $difficulty = $lu[$this->captcha_options['captcha_difficulty']]
                );
            }
        }

        list($this->captcha_answer, $this->svg_output) = $this->svgCaptcha->getSVGCaptcha();
        
        $this->captcha_answer = ($this->captcha_options['captcha_case_sensitive'] == True) ? $this->captcha_answer : strtolower($this->captcha_answer);

        $_SESSION['svgc_solution'] = $this->captcha_answer;
    }

    /**
     * Get random pseudo bytes for encryption.
     */
    public function svgc_random_hex_bytes($length = 32) {
        $cstrong = False;
        $bytes = openssl_random_pseudo_bytes($length, $cstrong);
        if ($cstrong == False)
            return False;
        else
            return $bytes;
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        $hook_suffix = add_options_page(
                __('SVGCaptcha Options', 'SVGCaptcha'), __('SVGCaptcha Options', 'SVGCaptcha'), 'manage_options', 'svgc_submenu', array($this, 'create_admin_page')
        );

        // Add javascript to the admin page
        add_action("load-" . $hook_suffix, array($this, "svgc_load_submenu_js"));
    }

    public function svgc_load_submenu_js() {
        add_action("admin_enqueue_scripts", array($this, 'svgc_captcha_reload_admin'));
        add_action("admin_enqueue_scripts", array($this, 'svgc_custom_captcha_toggle_admin'));
    }

    public function svgc_captcha_reload_admin() {
        wp_register_script('captcha-reload-admin', plugin_dir_url(__FILE__) . 'js/reload_captcha_admin.js', array('jquery'));
        wp_enqueue_script('captcha-reload-admin');
    }

    public function svgc_custom_captcha_toggle_admin() {
        wp_register_script('toggle-custom-captcha', plugin_dir_url(__FILE__) . 'js/toggle-custom-captcha.js', array('jquery'));
        wp_enqueue_script('toggle-custom-captcha');
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->captcha_options = get_option('svgc_options');
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>SVGCaptcha settings</h2>        
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                submit_button();
                settings_fields('svgc_options_group');
                do_settings_sections('svgc_submenu');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings.
     */
    public function page_init() {
        register_setting(
                'svgc_options_group', // Option group
                'svgc_options', // Option name
                array($this, 'sanitize') // Sanitize
        );

        // Add settings sections
        foreach ($this->dsettings as $key => $value) {
            add_settings_section(
                    $key, // ID
                    $value['description'], // Title
                    array($this, 'print_section_info'), // Callback
                    'svgc_submenu' // Page
            );
        }


        // Add settings fields
        foreach ($this->dsettings as $section => $value) {
            foreach ($value['sd'] as $key => $value) {
                add_settings_field(
                        $key, // ID
                        $value[2], // Title
                        array($this, $value[3]), // Callback
                        'svgc_submenu', // Page
                        $section, // Section
                        array_merge((array) $key, $value)
                );
            }
        }
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input) {
        $new_input = array();
        foreach ($input as $key => $value) {
            $new_input[$key] = sanitize_text_field($value);
        }

        return $new_input;
    }

    public function text_callback($d) {
        printf(
                '<input type="text" id="%2$s" name="svgc_options[%2$s]" value="%1$s" />', isset($this->captcha_options[$d[0]]) ? esc_attr($this->captcha_options[$d[0]]) : esc_attr($d[1]), esc_attr($d[0])
        );
    }

    /*
     * d[0] = the key of the field.
     * d[1] = the default value for the field.
     * d[2] = field title
     */

    public function select_callback($d) {
        if (!is_array($d[2]) || $d[2] == null)
            wp_die(__('Invalid value for all possible values', 'SVGCaptcha'));

        // Build select options
        foreach ($d[2] as $option) {
            $select_html .= '<option value="' . $option . '" ' . selected($this->captcha_options[$d[0]], $option, false) . ' >' . strtoupper(substr($option, 0, 1)) . substr($option, 1, strlen(option));
        }

        printf(
                '<select id="%1$s" name="svgc_options[%1$s]">%2$s</select>', esc_attr($d[0]), $select_html
        );
    }

    public function checkbox_callback($d) {
        $checked = isset($this->captcha_options[$d[0]]) ? checked($this->captcha_options[$d[0]], True, False) : checked($d[1], True, False);
        printf(
                '<input type="checkbox" id="%1$s" name="svgc_options[%1$s]" value="1" ' . $checked . ' />', esc_attr($d[0])
        );
    }

    public function captcha_preview_callback($d) {
        $this->svgc_get_captcha();
        print '<figure id="SVGCaptchaPreviewContainer"' . $this->svg_output . '<figcaption>The solution for the generated captcha is <strong style="color:red">' . $this->captcha_answer . '</strong></figcaption></div>';
    }

    public function custom_captcha_preview_callback($d) {
        $this->svgc_get_captcha();
        print '<figure id="SVGCaptchaPreviewContainer"' . $this->svg_output . '<figcaption>The solution for the generated captcha is <strong style="color:red">' . $this->captcha_answer . '</strong></figcaption></div>';
    }

    /**
     * Print the Section text
     */
    public function print_section_info() {
        print 'Enter your settings below: ';
    }

    /**
     * Encrypt data using AES_256 with CBC mode. Prepends IV on ciphertext.
     * 
     */
    public function svgc_encrypt($plaintext) {
        if (false == ($key = get_option('ccpatcha_encryption_key')))
            wp_die(__('Encryption error: could not retrieve encryption key from options database.', 'SVGCaptcha'));

        $key = base64_decode($key); /* Get binary key */

        if (32 != ($key_size = strlen($key)))
            wp_die(__('Encryption error: Invalid keysize.', 'SVGCaptcha'));

        # Create random IV to use with CBC mode.
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plaintext, MCRYPT_MODE_CBC, $iv);

        # Prepend the IV on the ciphertext for decryption (Must not be confidential).
        $ciphertext = $iv . $ciphertext;

        # Encode such that it can be represented as astring.
        return base64_encode($ciphertext);
    }

    /**
     * Decrypt using AES_256 with the IV prepended on base64_encoded ciphertext.
     */
    public function svgc_decrypt($ciphertext) {
        if (false == ($key = get_option('ccpatcha_encryption_key')))
            wp_die(__('Decryption error: could not retrieve encryption key from options database.', 'SVGCaptcha'));

        $key = base64_decode($key); /* Get binary key */

        $ciphertext = base64_decode($ciphertext);

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv = substr($ciphertext, 0, $iv_size);
        $ciphertext = substr($ciphertext, $iv_size);

        $plaintext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $ciphertext, MCRYPT_MODE_CBC, $iv);
        return $plaintext;
    }

}

$dummy = new SVGCaptchaWordpressPlugin();
