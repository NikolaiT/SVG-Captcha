<?php

/**
 * Plugin Name: SVGCaptcha
 * Plugin URI: http://incolumitas.com/SVGCaptcha
 * Description: This plugin generates SVG captchas and displays them on the login form and comment forms. You can customize the inner working of SVGCaptcha exhaustively and you can react to spamming attacks with a increased difficulty for instance.
 * Version: 0.1
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

/**
 * If this plugin get's started the very first time, add a base64 encoded ecryption key to the WP options database.
 */
if (false == get_option('ccpatcha_encryption_key')) {
    if (false == ($key = ccaptcha_random_hex_bytes($length = 32)))
        wp_die(__('Error: Failed generating encryption key.', 'SVGCaptcha'));

    add_option('ccpatcha_encryption_key', base64_encode($key));
}

include_once("SVGCaptcha.php"); // include the lib
/* 
 * Disable that if you use this plugin on your own server.
 * Own my site for example, I need to style the form for the horizontal bootstrap 3 form 
 */
define('CUSTOM_FORM_STYLE', True);

/* Should we consider the case of the captcha? */
define('CASE_SENSITIVE', True);

// Set the solution cookie
//add_action('init', 'svgc_set_solution_cookie');
// Set a filter to add additional input fields for the comment.
add_filter('comment_form_defaults', 'svgc_comment_form_defaults');
// Add a filter to verify if the captch in the comment section was correct.
add_filter('preprocess_comment', 'svgc_validate_comment_captcha');

// Ad custom captcha field to login form
add_action('login_form', 'svgc_login_form_defaults');
// Validate captcha in login form.
add_filter('authenticate', 'svgc_validate_login_captcha', 30, 3);

/*
 * Create the captcha and store the encrypted solution in a hidden field.
 * Alternatives:
 * Use a encrypted session variable (Bad: Uses files = slow)
 * Use add_option() [Using a database] (Bad: Needs to be written to = slow)
 * Use a encrypted hidden field. That's what I do :/
 * Use a cookie. (Bad: Can't make it working with wordpress.)
 */

function svgc_comment_form_defaults($default) {
    if (!is_admin()) {

        $out = svgc_get_captcha();
        
        if (CUSTOM_FORM_STYLE === True) {
            $default['fields']['email'] .=
                    '<div class="form-group">
                        <label class="col-sm-2 control-label" for="svgc_answer">' . __('Captcha', 'SVGCaptcha') . '<span class="required"> *</span></label>
                        <div class="col-sm-10">
                                <input id="svgc_answer" class="form-control" name="svgc_answer" size="30" type="text" />
                                <div id="SVGCaptchaContainer">'.$out["captcha_data"].'</div>
                                <input name="svgc_solution" type="hidden" value="' . $out["captcha_solution"] . '" />
                        </div>
                    </div>';
        } else {
            $default['fields']['email'] .=
                    '<p class="comment-form-captcha"><label for="svgc_answer">' . __('Captcha', 'SVGCaptcha') . '</label>
                    <span class="required">*</span>
                    <input id="svgc_answer" name="svgc_answer" size="30" type="text" />
                    <input name="svgc_solution" type="hidden" value="' . $out["captcha_solution"] . '" />
                    <div id="SVGCaptchaContainer">'.$out["captcha_data"].'</div>';
        }
    }
    return $default;
}

function svgc_validate_comment_captcha($commentdata) {
    if (!is_admin()) { /* Admins excluded. They should't be prevented from spamming... */
        if (empty($_POST['svgc_answer']))
            wp_die(__('Error: You need to enter the captcha.', 'SVGCaptcha'));

        $answer = strip_tags($_POST['svgc_answer']);

        $solution = trim(svgc_decrypt($_POST["svgc_solution"]));

        if (!svgc_check($answer, $solution))/* Case insensitive comparing */
            wp_die(__('Error: Your supplied captcha is incorrect.', 'SVGCaptcha'));
    }
    return $commentdata;
}

function svgc_login_form_defaults() {

    //Get and set any values already sent
    $user_captcha = ( isset($_POST['svgc_answer']) ) ? $_POST['svgc_answer'] : '';

    $out = svgc_get_captcha();
    
    ?>
    
    <div class="form-group">
        <div id="SVGCaptchaContainer"><?php echo $out["captcha_data"] ?></div>
        <label for="svgc_answer" class="col-sm-2 control-label"><?php _e('Captcha', 'SVGCaptcha') ?><span class="required"> *</span></label>
        <input type="text" name="svgc_answer" id="svgc_answer" class="form-control" value="<?php echo esc_attr(stripslashes($user_captcha)); ?>" size="25" />
        <input type="hidden" name="svgc_solution" value="<?php echo $out["captcha_solution"] ?>" >
    </div>
    
    <?php
}

function svgc_validate_login_captcha($user, $username, $password) {
    if (!is_admin()) { /* Whenever a admin tries to login -.- */
        if (empty($_POST['svgc_answer'])) {
            return new WP_Error('invalid_captcha', __("You need to enter a fucking captcha.", 'SVGCaptcha'));
        } else {
            $answer = strip_tags($_POST['svgc_answer']);
            $solution = trim(svgc_decrypt($_POST["svgc_solution"]));
            if (!svgc_check($answer, $solution)) {/* Case insensitive comparing */
                return new WP_Error('invalid_captcha', __("Your supplied captcha is incorrect.", 'SVGCaptcha'));
            } else {
                return $user;
            }
        }
    }
}

/*
 * Checks whether the user provided answer is correct.
 */

function svgc_check($answer, $solution) {
    if (CASE_SENSITIVE) {
        return (strcmp($answer, $solution) == 0) ? True : False;
    } else {
        return (strcasecmp($answer, $solution) == 0) ? True : False;
    }
}

/*
 * Choses a random captcha from the pool and returns the corresponding image path.
 * Sets global variable $captcha_value to the value (The solution the user has to enter)
 * of the captcha.
 */

function svgc_get_captcha() {
    $obj = SVGCaptcha::getInstance(4, $width = 300, $height = 130, $difficulty = SVGCaptcha::EASY);
    $c = $obj->getSVGCaptcha();

    return array("captcha_data" => $c[1], "captcha_solution" => svgc_encrypt($c[0]));
}

/**
 * Get random pseudo bytes for encryption.
 */
function svgc_random_hex_bytes($length = 32) {
    $cstrong = False;
    $bytes = openssl_random_pseudo_bytes($length, $cstrong);
    if ($cstrong == False)
        return False;
    else
        return $bytes;
}

/**
 * Encrypt data using AES_256 with CBC mode. Prepends IV on ciphertext.
 * 
 */
function svgc_encrypt($plaintext) {
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
function svgc_decrypt($ciphertext) {
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