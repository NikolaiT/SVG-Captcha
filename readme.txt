=== SVG-Captcha ===
Contributors: incolumitas
Donate link: http://incolumitas.com/svgcaptcha/
Tags: captcha, match captcha, protect, text captcha, SVG, svg, performance, fast spam, antispam, login, registration, comment, lost password, capcha, catcha, captha
Requires at least: 3.0.1
Tested up to: 3.7.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin protects your blog from spam with captchas made with SVG. It is standalone and very fast.

== Description ==

SVG-Captcha is a very fast and secure captcha implementation, that creates captchas using SVG. It was designed in a long development phase which insures its security and itegrity. SVG-Captcha makes
use of the modern and up to date < href="https://github.com/NikolaiT/SVG-Captcha" title="The library">SVGCaptcha library</a>. A few points why you should use SVG-Captcha:

= Reasons to use =

* SVG-Captcha is utterly fast: It doesn't manipulate bitmap graphics because it makes a captcha that is interpreted on the client side (A SVG file). This means the client has to render the captcha completely, which relieves the server from load.
* SVG-Captcha is very secure and adaptable: There are multiple difficulty levels for the captcha. In the rare case that a spammer can crack a easy captcha, you can harden your captcha image in the admin settings!
* The plugin works on the comment-section, registration form and many more places.
* The plugin is absolutely free and will never have a PRO version.
* The captcha library will be continually developed further to adapt to possible threats.


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `plugin.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Plugin settings are located in 'Settings', 'SVGCaptcha'.

== Frequently Asked Questions ==

= How can I specify my custom captcha? =

Go to Settings -> SVG-Captcha and then enable the option "Enable user defined captchas ". Then the captcha will automatically have the properties defined by the custom captcha options (The options below the checkbox you just clicked). If you want to go 
back to predefined captchas (e.g. easy-medium-hard), then just disable the checkbox "Enable user defined captchas " and change your prefered difficulty.

= What about the security of SVG-Captcha, is it really secure? =

I'd say it is pretty secure (100% security is not possible anyway). If you want to learn more about SVGCaptcha (And thus aboout its inherent security), visit the <a href="http://incolumitas.com/svgcaptcha/">plugin page</a>.

== Screenshots ==

Will be added when access to the svn repo is granted.


== Changelog ==

Will be added when access to the svn repo is granted.