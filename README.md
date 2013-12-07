# SVG-Captcha

### What is SVGCaptcha?

SVGCaptcha is a captcha library implemented in the programming language PHP, that produces Scalable Vector Graphics. You can integrate it into your own webapp or you can use it on your wordpress blog as a plugin, that protects comment forms, login forms and the like. SVGCaptcha incorporates different levels of difficulty , that makes it a breeze to react on possible spamming attacks!

### How can I integrate SVGCaptcha in my application?

```
git clone https://github.com/NikolaiT/SVG-Captcha/
cd SVG-Captcha
```
And then you should see two files, one named SVGCaptcha.php and the other glyphs.php. Just copy them both in your application folder and then you must include SVGCaptcha.php in order to use it. This is how you generate a (easy) captcha:

```php
include 'SVGCaptcha.php';
$obj = SVGCaptcha::getInstance(4, $width = 300, $height = 130, $difficulty = SVGCaptcha::EASY);
$c = $obj->getSVGCaptcha();
echo $c[0]; // prints the captcha solution
echo $c[1]; // echoes the captcha itself.
```
### Examples

Here you get some examples (Please mind that the samples are converted to PNG, which means they are a bitmap format rather than a vector graphics format):

A easy captcha (difficulty-level: easy): ![a easy captcha][easy]

A harder captcha (difficulty-level: medium): ![a medium hard captcha][medium]

A pretty hard captcha (difficulty-level: hard): ![a hard to decipher captcha][hard]

### What are the advantages of SVGCaptcha?

Because spamming is a never ending issue, there’s always need to tell humans and robots apart. Hence there’s some need for captchas. But why another library like SVGCaptcha?

Other solutions make use of bitmap graphics in order to manufacture captcha images. This is CPU heavy and involves complex operations and requires to store a pool of bitmap images on the server itself. Free and professional captcha providers like reCAPTCH offer a possibility to integrate captchas in your site using their servers, which enables you to outsource the slow captcha generation process. But there’s still a severe drawback: You need to obtain a key in order to use this captcha service. Hence the provider knows everything about your traffic, which is usually not really nice. And in this case the provider is Google.

SVG Captchas instead are plotted on the client side: The library just generates a text SVG file and sends it to the user agent which then rasterizes the shapes defined in the svg file. This is very fast, because there is no more need to generate captchas on your server involving bitmap files.

There are different difficulty levels, that allow you to adjust to possible attacks. Normally, you won’t need hard to read and really annoying captchas (like the ones from Captcha.net). They are bad because users get pissed of and leave your site.
But in the rare case of a captcha cracking attempt, you can increase the difficulty of the generated captchas, which should make it rather cumbersome to crack them. Please note that SVGCaptcha is still very new, and the security is not proven whatsoever. There still remains a lot of investigation and adjustment to possible proof of concepts of how to circumvent SVGCaptcha.

### How does SVGCaptcha work?

Font’s consist of lines and Bézier curves. There are a wide range of different font formats, but basically, the all consits of simple curves. TrueType fonts are made of line segments and quadratic Bézier curves, whereas PostScript fonts make use of cubic Bézier curves. You can learn more about Bézier curves (a lot more) here. Because SVG allows you to draw exactly these curves in it’s path element (withing the d attribute), you can also use glyphs in SVG files.

And that is exactly what SVGCaptcha is doing: First of all, the library chooses a random typeface (It will have around 5 typefaces, one of which I “designed” on my own) and then it draws all the lines and splines of some randomly picked glyhps that constitute a word to be guessed by the user (That is, the captcha) into the d path attribute. There are several mechanism, that make it hard to reverse the mapping from splines to glyphs (And hence to the obfuscated string), which represents the core security of the captchas:

+ All glpyhs are packed into a single d attribute. Therefore you cannot distinguish glyphs by their distribution in path elements.
+ Point representation changes from absolute to relative on a random base (Random usage of uppercase commands and lowercase commands. E.g: m/M, L/l, Q/q, S/s)
+ Cubic Bezier curves are converted to quadratic splines and vice verse.
+ Bezier curves are sometimes (on a random base) approximated by lines. Lines are represented as bezier curves.
+ All input points undergo affine transformation matrices (Rotation/Skewing/Translation/Scaling).
+ Random “components”, such as holes or misformations, and completely random generates shapes, are arbitrarily injected into the d addtribute.
+ The definition of the components (Which consists of geometrical primitives) that constitute each glyph, are arranged randomly.More precise: The imaginal pen jumps from glyph to glyph with the Moveto (M/m) command in a unpredictable manner.
+ In order to make analyses as hard as possible, we need to connect each glyph in a matther that makes it unfeasible to distinguish the glyph entities. For instance: If every glyph were drawn in a separate subpath in the d attribute, it’d be very easy to recognize the single glyphs. Furthermore there must be some countermeasures to make out the glyphs by their coordinate values. Hence they need to overlap to a certain degree that makes it hard to assign geometrical primitives to a certain glyph entity. This is done with overlapping and random shapes, but I feel there is some weak spot.

Note: The majority of the above methods try to hinder cracking attempts that try to match the distorted path elements against the original path data (Which of course are publicly known). This means that there remains the traditional cracking attempt: Common OCR techniques on a SVG captcha, that is converted to a bitmap format. Hence, some more blurring techniques, especially for traditional OCR attacks, are applied:

+ Especially to prevent OCR techniques, independent random shapes are injected into the d attribute.
+ Colorous background noise is not an option (That’s just a css defintion in SVG).
+ This section is in a need of better ideas :/

### Todo – Developer notes (As of 06.12.2013)

- [x] Make major improvements on the SVG path command generation algorithm (more entropy is highly desirable)
- [ ] Add 3-4 alternative typefaces to glyphs.php. Use fonttools for this.
- [x] Add wordpress plugin for the library
- [ ] Write easy understandable HOWTO and a better, exhaustive github README
- [x] Add several pictures and a live demo of the plugin on this site

### Contact
If you feel like contacting me, do so and send me a mail. You can find my contact information on my [blog][3].

### Original

This *README* is mirroring the original site about SVG-Captcha. It is situated [here][4].


[3]: http://incolumitas.com/about/contact/ "Contact with author"
[4]: http://incolumitas.com/svgcaptcha/ "Oringal site"
[easy]: assets/easy.png "Easy SVG-Captcha"
[medium]: assets/medium.png "Medium SVG-Captcha"
[hard]: assets/hard.png "Hard SVG-Captcha"

