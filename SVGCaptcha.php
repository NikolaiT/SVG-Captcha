<?php

/**
 * This is SVGCaptcha!
 * 
 * @author Nikolai Tschacher <admin@incolumitas.com>
 * @copyright (c) 2013, Nikolai Tschacher, incolumitas.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.0
 * 
 * It follows a completely different approach then CunningCaptcha. Its goal is to generate
 * SVG captchas (That means: vector graphics) that depict a captcha. This is a nice idea and I found it myself
 * (I am really proud now), but it seems to be already done: https://bitbucket.org/scriptoid/svgcaptcha/
 * 
 * After a quick glance into this repo, I found it to not suffice my purposes. There's no good reason why I should use it, because
 * it looks utterly unsecure (Text is directly written, position of the chars is determined by (x,y) coordinates and can thus be very
 * easily parsed). 
 * 
 * My implementation will draw the captcha text with help of a own font, that consits of Bezier curvatures and straight lines.
 * Then it will apply some mathematical functions on the raw curvature data such that parsing the SVG files and cracking the unerlying pattern becomes 
 * difficult (I can't say that it will be hard enough - It's definitely easier to do as to regognize chars from bitmap formats such as .jpb or .png).
 * 
 * Therefore the very first step is: 
 * How can I harden parsing attempts? What does the attacker need to to in order to recognize letters? How should my algorithm look like?
 * 
 * There are several ideas that come to my mind immediately:
 *  - The points that constitute the curves (that constitute the Glyphs) should be sheared/rotated/translated with affine transformations and 
 *    random parameters.
 *  - Every captcha should be defined by one single path element. Thus it becomes impossible to differentiate between single glyphs, because all glyphs
 *    are drawn with one "stroke".
 * 
 * The SVG path specification in short:
 * Commands: M = moveto, L = lineto, C = curveto,  Z = closepath 
 * Relative versions of commands: Uppercase means absoute, lowercase means relative. All coordinate values are relative to the point at the start of the command.
 * Alternate versions of lineto are available in case of horizontal and vertical lines:
 *    H/h draws a horizontal line from the current point (cpx, cpy) to (x, cpy). Multiple x values can be provided (which doesen't make sense, but it might be a neat idea!)
 *    V/v draws a vertical line fro the current point (cpx, cpy) to (cpx, y). Multiple y values can be provided.
 * Alternate versions of curve are available where some control points on the current segment can be determined from control points of the previous segment:
 * 
 * Commands:
 * moveto: M/m, establishes a new current point. A path data segment must begin with a moveto. Subsequent moveto commands represent the start of a new subpath.
 * closepath: Z/z, ends the current subpath and causes an automatic straight line to be drawn from the current point to the initial point of the current subpath.
 * At the end of the command, the new current point is set to the initial point of the current subpath. Z and z have identical effect.
 * lineto: L/l, draws a straight line from the current point to a new point, that becomes the new current point.
 * cubic curveto: C/c, parameters: (x1, y1, x2, y2, x, y)+. Draws a cubic Bezier curve from the current point to x,y using two control points.
 * smooth cubic curveto: S/s, parameters: (x2, y2, x, y)+. Draws a cubic Bezier curve from the current point to x,y. The first control point is assumed to be the reflection
 * of the second control point on the previous command relative to the current point.
 * quadratic curveto: Q/q, parameters: (x1, y1, x, y)+. Draws a quadratic Bezier curve from the current point to (x, y) using (x1, y1) as the control point.
 * smooth quadratic curveto: T/t, parameters: (x, y)+. Draws a quadratic Bezier curve from the current point to (x,y). The control point is assumed to be the reflection of
 * the control point of the previous command relative to the current point.
 * 
 */
$obj = SVGCaptcha::getInstance(5, 700, 300, $difficulty = SVGCaptcha::MEDIUM);
$obj->generate();

class SVGCaptcha {

    private static $instance = NULL;
    private $svg_data = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
    "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
            
<svg 
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   width="{{width}}"
   height="{{height}}"
   id="svgCaptcha"
   version="1.1"
   style="background:#00a">
  <title>SVGCaptcha</title>
  <g>
    <path
       style="fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
       d="{{pathdata}}"
       id="captcha" />
  </g>
</svg>
EOD;
    private $numchars = 0;
    private $width = 0;
    private $height = 0;
    private $difficulty;
    // Multidimensional array holding the difficulty settings
    // The boolean value at index 0 indicates whether to use the function.
    // p indicates the probability 1/p
    private $dsettings = array(
        'transformations' => array('apply' => False, 'rotate' => False, 'skew' => False, 'scale' => False, 'shear' => False, 'translate' => False),
        'approx_shapes' => array('apply' => False, 'p' => 3, 'r_al_num_lines' => NUll),
        'change_degree' => array('apply' => False, 'p' => 5),
        'split_curve' => array('apply' => False, 'p' => 5),
        'shapeify' => array('apply' => False, 'r_num_shapes' => NULL, 'r_num_gp' => NULL)
    );

    const DEBUG = FALSE;
    const VERBOSE = FALSE;
    // Difficulty constants
    const EASY = 1;
    const MEDIUM = 2;
    const HARD = 3;

    // The answer to the generated captcha.
    private $captcha_answer = "";

    public function getInstance($numchars, $width, $height, $difficulty = SVGCaptcha::MEDIUM) {
        if (!isset(self::$instance))
            self::$instance = new SVGCaptcha($numchars, $width, $height, $difficulty);

        return self::$instance;
    }

    private function __construct($numchars, $width, $height, $difficulty) {
        $this->numchars = $numchars;
        $this->width = $width;
        $this->height = $height;
        $this->difficulty = $difficulty;

        // Set the parameters for the algorithms according to the user 
        // supplied difficulty.
        if ($this->difficulty == self::EASY) {
            
        } else if ($this->difficulty == self::MEDIUM) {
            
        } else if ($this->difficulty == self::HARD) {
            
        }
    }

    /**
     * This function generates a SVG d attribute of a path element. The d attribute represents the path data.
     * 
     * The function takes an parameter $clength as input and modifies the appearance of the $clength randomly chosen
     * glyphs of the alphabet, such that viewers can still recognize the original glyph but programs fail so (or do very bad compared to humans).
     * 
     * The following random distortion mechanisms are the backbone of the captchs's security strength:
     * - All glpyhs are packed into a single d attribute.
     * - Point representation changes from absolute to relative on a random base.
     * - Cubic Bezier curves are converted to quadratic splines and vice versa.
     * - Bezier curves are approximated by lines. Lines are represented as bezier curves.
     * - All input points undergo affine transformation matrices (Rotation/Skewing/Translation/Scaling).
     * - Random "components", such as holes or misformations (mandelbrot shapes for instance) are randomly injected into the shape definitions.
     * - The definition of the components (Which consists of geometrical primitives) that constitute each glyph, are arranged randomly.
     *   More precise: The imaginal pen jumps from glyph to glyph with the Moveto (M/m) command in a unpredictable manner.
     * - In order to make analyses as hard as possible, we need to connect each glyph in a matther that makes it unfeasible to distinguish
     *   the glyph entities. For instance: If every glyph was drawn in a separate subpath in the d attribute, it'd be very easy to recognize the single glyphs.
     *   Furthermore there must be some countermeasures to make out the glyphs by their coordinate values. Hence they need to overlap to a certain degree that makes it 
     *   hard to assign geometrical primitives to a certain glyph entity.
     * 
     * Note: The majority of the above methods try to hinder cracking attempts that try to match the distorted path
     *       elements against the original path data (Which of course are public). 
     *       This means that there remains the traditional cracking attempt: Common OCR techniques on a SVG captcha, that is converted to a bitmap format.
     *       Hence, some more blurring techniques, especially for traditinoal attacks, are applied:
     * - Especially to prevent OCR techniques, independent random shapes are injected into the d attribute.
     * - Colorous background noise is not an option (That's just a css defintion in SVG).
     */
    public function generate() {
        require "glyphs.php";
        /* Start by choosing $clength random glyphs from the alphabet and store them in $selected */
        $chars_alphabet = array_keys($alphabet);
        for ($i = 0; $i < $this->numchars; $i++) {
            $selected[] = $chars_alphabet[secure_random_number(0, count($chars_alphabet) - 1)];
        }

        /* Now delete all other glyphs in the array such that we can work with $alphabet */
        foreach ($alphabet as $key => $value) {
            if (in_array($key, $selected)) {
                continue;
            } else {
                unset($alphabet[$key]);
            }
        }

        /* Pack all shape types together for every remaining glyph. I am sure there are more elegant ways. */
        foreach ($alphabet as $key => $value) {
            $packed[$key]["width"] = $alphabet[$key]["width"];
            $packed[$key]["height"] = $alphabet[$key]["height"];
            foreach ($value['glyph_data'] as $shapetype) {
                foreach ($shapetype as $shape) {
                    $packed[$key]['glyph_data'][] = $shape;
                }
            }
        }

        /*
         * First of all, the glyphs need to be scaled such that the biggest glyph becomes half the 
         * height of the overall height.
         */
        $this->_scale_by_largest_glyph($packed);

        /*
         * By now, each glyph is randomly aligned but still in their predefined geometrical form. It's time to give them some new shape
         * with affine transformations. It is imported to call this function before the glyphs become aligned, in order for
         * the affine transformations to relate to a constant coordinate system.
         */
        if ($this->dsettings['transformations']['apply']) {
            $this->_apply_affine_transformations($packed);
        }

        /*
         * Now every glyph has a unique size (as defined by their typeface) and they overlap all more or less if we would draw them directly.
         * Therefore we need to align them horizontally/vertically such that the (n+1)-th glyph overlaps not more than to the horizontal mid of the n-th glyph.
         * 
         * In order to do so, we need to know the widths/heights of the glyphs. It is assumed that this information is held in the alphabet array.
         */
        $this->_align_randomly($packed);

        /*
         * Finally, we generate a single array of shapes, and then shuffle it. Therefore we cannot 
         * longer distinguish which shape belongs to which glyph.
         * 
         */
        foreach ($packed as $char => $value) {
            foreach ($value["glyph_data"] as $shape) {
                $shapearray[] = $shape;
            }
        }
        /* Shuffle Mhuffle it!
         * 
         * I think a call to shuffle() is not really unsecure in this case. Maybe this needs to be
         * done with a secure PRNG in the future. 
         */
        shuffle($shapearray);

        /* Insert some randomly generated shapes in the shapearray */
        if ($this->dsettings['shapeify']['apply']) {
            $shapearray = $this->_shapeify($shapearray);
        }

        /*
         * Here is the part where the rest of the magic happens!
         * 
         * Let's modify the shapes. It is perfectly possible that a single 
         * shape get's downgraded and afterwards approximated with lines wich 
         * somehow neutralizes the downgrade. But this happens rarily.
         * 
         * Any of the following methods may change the input array! So they cannot be run 
         * in a for loop, because keys will ge messed up.
         */

        // Executes an curve downgrade/upgrade from times to times :P
        if ($this->dsettings['change_degree']['apply']) {
            $this->_maybe_change_curvature_degree($shapearray);
        }

        // Maybe split a single curve into two subcurves
        if ($this->dsettings['split_curve']['apply']) {
            $shapearray = $this->_maybe_split_curve($shapearray);
        }

        // Approximates a curve with lines on a random base or the other way around.
        if ($this->dsettings['approx_shapes']['apply']) {
            $shapearray = $this->_maybe_approximate_xor_make_curvaceous($shapearray);
        }

        // Shuffle once more
        shuffle($shapearray);

        /* Now write the SVG file! */
        $path_str = "";
        $begin_path = True;
        foreach ($shapearray as $key => &$shape) {
            if ($begin_path) {
                $path_str .= "M {$shape[0]->x} {$shape[0]->y}";
                $begin_path = False;
            }

            $path_str .= $this->_shape2_cmd($shape, True, True);
        }

        $this->_write_SVG($path_str);
    }

    /**
     * This function replaces all parameters in the SVG skeleton with the computed
     * values and finally echoes the SVG string.
     * 
     * @param type $path_str The string holding the path data for the path d attribute.
     */
    private function _write_SVG($path_str) {
        $this->svg_data = str_replace("{{width}}", $this->width, $this->svg_data);
        $this->svg_data = str_replace("{{height}}", $this->height, $this->svg_data);
        /* Update the d path attribute */
        $this->svg_data = str_replace("{{pathdata}}", $path_str, $this->svg_data);

        echo $this->svg_data;
    }

    /**
     * Insert ranomly generated shapes into the shapearray(mandelbrot like distortios would be awesome!).
     * 
     * The idea is to replace certain basic shapes such as curves or lines with a geometrical 
     * figure that distorts the overall picture of the glyph. Such a figure could be generated randomly, the 
     * only constraints are, that the start point and end point of the replaced shape coincide with the 
     * randomly generated substitute.
     * 
     * A second approach is to add such random shapes without replacing existing ones. 
     * 
     * This function does both of the above. The purposes for this procedure is to make
     * OCR techniques more cumbersome.
     * 
     * Note: Currently, only the second construct is implemented due to the likely
     * difficulty involving the first idea.
     * 
     * @param type $shapearray array
     */
    private function _shapeify($shapearray) {
        $random_shapes = array();

        $random_shape = function() {
            $rshapes = array();
            // Bounding points that constrain the maximal shape expansion
            $min = new Point(0, 0);
            $max = new Point($this->width, $this->height);
            // Get a start point
            $previous = $startp = new Point(secure_random_number($min->x, $max->x), secure_random_number($min->y, $max->y));
            // Of how many random geometrical primitives should our random shape consist?
            $ngp = secure_random_number(4, 9);

            foreach (range(0, $ngp) as $j) {
                // Find a random endpoint for geometrical primitves
                // If there are only 4 remaining shapes to add, choose a random point that
                // is closer to the endpoint!
                $rp = new Point(secure_random_number($min->x, $max->x), secure_random_number($min->y, $max->y));
                if (($ngp - 4) <= $j) {
                    $rp = new Point(secure_random_number($min->x, $max->x), secure_random_number($min->y, $max->y));
                    // Make the component closer to the startpoint that is currently wider away
                    // This ensures that the component switches over the iterations (most likely).
                    $axis = abs($startp->x - $rp->x) > abs($startp->y - $rp->y) ? 'x' : 'y';
                    if ($axis === 'x') {
                        $rp->x += ($startp->x > $rp->x) ? abs($startp->x - $rp->x) / 4 : abs($startp->x - $rp->x) / -4;
                    } else {
                        $rp->y += ($startp->y > $rp->y) ? abs($startp->y - $rp->y) / 4 : abs($startp->y - $rp->y) / -4;
                    }
                }

                if ($j == ($ngp - 1)) { // Close the shape. With a line
                    $rshapes[] = array($previous, $startp);
                    break;
                } else if (rand(0, 1) == 1) { // Add a line
                    $rshapes[] = array($previous, $rp);
                } else { // Add quadratic bezier curve
                    $rshapes[] = array($previous, new Point($previous->x, $rp->y), $rp);
                }

                $previous = $rp;
            }
            return $rshapes;
        };
        // How many random shapes? 
        $ns = secure_random_number(0, 5);

        foreach (range(0, $ns) as $i) {
            $random_shapes = array_merge($random_shapes, $random_shape());
        }

        return array_merge($shapearray, $random_shapes);
    }

    /**
     * Does not change the size/keys of the input array!
     * 
     * Elevates maybe the curvature degree of a quadratic curve to a cubic curve.
     * 
     * @param type $shapearray shape array
     */
    private function _maybe_change_curvature_degree(&$shapearray) {
        foreach ($shapearray as &$shape) {
            $do_change = (bool) (secure_random_number(0, 5) == 5);
            if ($do_change && count($shape) == 3) {
                self::V("changing curvature degree");
                /*
                 * We only deal with quadratic splines. Their degree is elevated
                 * to a cubic curvature.
                 * 
                 * we pick "1/3rd start + 2/3rd control" and "2/3rd control + 1/3rd end",
                 * and now we have exactly the same curve as before, except represented
                 * as a cubic curve, rather than a quadratic curve.
                 */
                list($p1, $p2, $p3) = $shape;
                $shape = array(
                    $p1,
                    new Point(1 / 3 * $p1->x + 2 / 3 * $p2->x, 1 / 3 * $p1->y + 2 / 3 * $p2->y),
                    new Point(1 / 3 * $p3->x + 2 / 3 * $p2->x, 1 / 3 * $p3->y + 2 / 3 * $p2->y),
                    $p3
                );
            }
        }
        return True;
    }

    /**
     * Split quadratic and cubic bezier curves in two components.
     * 
     * @param array $shapearray
     * @return array
     */
    private function _maybe_split_curve($shapearray) {
        // Holding a copy preserves messing up the argument array.
        $newshapes = array();

        foreach ($shapearray as $key => $shape) {
            $do_change = (bool) (secure_random_number(0, 5) == 5);
            if ($do_change && count($shape) >= 3) {
                self::V("splitting curve");
                $left = array();
                $right = array();
                $this->_split_curve($shape, $this->_rt(), $left, $right);
                $right = array_reverse($right);

                // Now update the shapearray accordingly: Delete the old curve, append the two new ones :P
                if (!empty($left) and !empty($right)) {
                    self::D("adding left:" . count($left) . " and right:" . count($right));
                    unset($shapearray[$key]);
                    $newshapes[] = $left;
                    $newshapes[] = $right;
                }
            }
        }

        return array_merge($newshapes, $shapearray);
    }

    /**
     * Approximates maybe a curve with lines or maybe converts lines to quadratic or cubic 
     * bezier splines (With a slight curvaceous shape).
     * 
     * @param type $shapearray The array holding all shapes.
     * @param type $key The key indicating the current shape.
     */
    private function _maybe_approximate_xor_make_curvaceous($shapearray) {
        // Holding a an array of keys to delete after the loop
        $dk = array();
        $merge = array(); // Accumulating the new shapes

        foreach ($shapearray as $key => $shape) {
            $do_change = (bool) (secure_random_number(0, 3) == 3);
            if ($do_change) {
                if ((count($shape) == 3 || count($shape) == 4)) {
                    self::V("approximating curve with lines");
                    $lines = $this->_approximate_bezier($shape);
                    $dk[] = $key;
                    $merge = array_merge($merge, $lines);
                } else if (count($shape) == 2) {
                    /*
                     * This is FUN: Approximate lines with curves! There are no limits for 
                     * your imagination
                     */
                    self::V("approximating lines by curves");
                    $shapearray[$key] = $this->_approximate_line($shape);
                }
            }
        }
        // Soem array kung fu to get rid of the duplicate shapes
        return array_merge($merge, array_diff_key($shapearray, array_fill_keys($dk, 0)));
    }

    /**
     * Transforms an array of points into its according SVG path command. Assumes that the 
     * "current point" is already existant.
     * 
     * @param type $shape The array of points to convert.
     * @param type $absolute Whether the $path command is absolute or not.
     * @param type $explicit_moveto If we should add an explicit moveto before the command.
     */
    private function _shape2_cmd($shape, $absolute = True, $explicit_moveto = False) {
        if ($explicit_moveto) {
            $prefix = "M {$shape[0]->x} {$shape[0]->y} ";
        } else {
            $prefix = "";
        }

        if (count($shape) == 2) { // Handle lines
            list($p1, $p2) = $shape;
            $cmd = "L {$p2->x} {$p2->y} ";
        } else if (count($shape) == 3) { // Handle quadratic bezier splines
            list($p1, $p2, $p3) = $shape;
            $cmd = "Q {$p2->x} {$p2->y} {$p3->x} {$p3->y} ";
        } else if (count($shape) == 4) { // Handle cubic bezier splines
            list($p1, $p2, $p3, $p4) = $shape;
            $cmd = "C {$p2->x} {$p2->y} {$p3->x} {$p3->y} {$p4->x} {$p4->y} ";
        }
        if (!$cmd)
            return False;

        return $prefix . $cmd;
    }

    /**
     * Scales all the glyphs by the glyph with the biggest height.
     * 
     * @param type $glyphs array
     */
    private function _scale_by_largest_glyph(&$glyphs) {
        // $this->width = 2 * $my*$what <=> $what = $this->width/2/$my
        $my = max(array_column($glyphs, "height"));
        $scale_factor = ($this->height / 2) / $my;

        $this->on_points(
                $glyphs, array($this, "_scale"), array($scale_factor, $scale_factor)
        );

        // And change their height/widths attributes manually
        foreach ($glyphs as &$value) {
            $value["width"] *= $scale_factor;
            $value["height"] *= $scale_factor;
        }
    }

    /**
     * Algins the glyphs horizontally and vertically in a random way.
     * 
     * @param type $glyphs The glyphs to algin.
     */
    private function _align_randomly(&$glyphs) {
        $accumulated_hoffset = 0;
        $lastxo = 0;
        $lastyo = 0;
        $overlapf = 2 / 3;
        foreach ($glyphs as &$glyph) {
            // Get a random x-offset based on the width of the previous glyph divided by two.
            $accumulated_hoffset += secure_random_number($lastxo, ($glyph["width"] > $lastxo) ? $glyph["width"] : $lastxo);
            // Get a random y-offst based on the height of the current glyph.
            $h = round($glyph['height'] * $overlapf);
            $yoffset = secure_random_number((10 > $h ? $h : 10), $h);
            // Translate all points by the calculated offset.
            $this->on_points(
                    $glyph["glyph_data"], array($this, "_translate"), array($accumulated_hoffset, $yoffset)
            );

            $lastxo = round($glyph['width'] * $overlapf);
            $lastyo = round($glyph['height'] * $overlapf);
        }
        /*
         * Reevaluate the width of the image by the accumulated offset + 
         * the width of the last glyph + a random padding of maximally the last 
         * glpyh's half size.
         * 
         */
        $this->width = $accumulated_hoffset + $glyph["width"] +
                secure_random_number($glyph["width"] * $overlapf, $glyph["width"]);
    }

    /*
     * SHAKE IT BABY!
     * 
     * This function distorts the coordinate system of every glyph on two levels:
     * First it chooses a set of affine transformations randomly. Then it distorts the coordinate
     * system by feeding the transformations random arguments.
     */

    private function _apply_affine_transformations(&$glyphs) {
        foreach ($glyphs as &$glyph) {
            foreach ($this->_get_random_transformations() as $transformation) {
                //D($transformation);
                $this->on_points($glyph["glyph_data"], $transformation[0], $transformation[1]);
            }
        }
    }

    private function _get_random_transformations() {
        // Prepare some transformations with some random arguments.
        $transformations = array(
            array(array($this, "_rotate"), array($this->_ra())),
            array(array($this, "_skew"), array($this->_ra())),
            array(array($this, "_scale"), $this->_rs()),
                //array(array($this, "_shear"), array(0.5, 0)),
                // array(array($this, "_translate"), array(5, 5))
        );

        if (empty($transformations))
            return Null;

        $n = secure_random_number(0, count($transformations) - 1);

        shuffle_assoc($transformations);

        // Delete the (random) transformations we don't want
        for ($i = 0; $i < $n; $i++) {
            unset($transformations[$i]);
        }

        return $transformations;
    }

    /**
     * Recursive function.
     * 
     * Applies the function $callback recursively on every point found in $data.
     * The $callback function needs to have a point as its first argument.
     * 
     * @param type $data An array holding point instances.
     * @param type $callback The function to call for any point.
     * @param type $args An associative array with parameter names as keys and arguments as values.
     */
    private function on_points(&$data, $callback, $args) {
        // Base step
        if ($data instanceof Point) { // Send me a letter for X-Mas!
            if (is_callable($callback)) {
                call_user_func_array($callback, array_merge(array($data), $args));
            }
        }
        // Recursive step
        if (is_array($data)) {
            foreach ($data as &$value) {
                $this->on_points($value, $callback, $args);
            }
            unset($value);
        }

        return; // Do nothing, return nothing, just return anything :P
    }

    /**
     * Returns a random angle.
     * 
     * @return type integer.
     */
    private function _ra() {
        $n = secure_random_number(0, 6) / 10;
        if (secure_random_number(0, 1) == 1)
            $n *= -1;
        return $n;
    }

    /**
     * Returns a random scale factor.
     * 
     * @return type integer.
     */
    private function _rs() {
        $z = secure_random_number(8, 13) / 10;
        return array($z, $z); // Let's be boring here...
    }

    /**
     * Returns a random t parameter between 0-1 and 
     * if $inclusive is True, including zero and 0.
     * 
     * @param bool $inclusive Description
     * @return integer The value between 0-1
     */
    private function _rt($inclusive = True) {
        if ($inclusive)
            $z = secure_random_number(0, 1000) / 1000;
        else
            $z = secure_random_number(1, 999) / 1000;
        return $z;
    }

    /**
     * Applies a rotation matrix on a point:
     * (x, y) = cos(a)*x - sin(a)*y, sin(a)*x + cos(a)*y
     * 
     * @param type $p The point to rotate.
     * @param type $a The rotation angle.
     */
    private function _rotate($p, $a) {
        $x = $p->x;
        $y = $p->y;
        $p->x = cos($a) * $x - sin($a) * $y;
        $p->y = sin($a) * $x + cos($a) * $y;
    }

    /**
     * Applies a skew matrix on a point:
     * (x, y) = x+sin(a)*y, y
     * 
     * @param type $p The point to skew.
     * @param type $a The skew angle.
     */
    private function _skew($p, $a) {
        $x = $p->x;
        $y = $p->y;
        $p->x = $x + sin($a) * $y;
    }

    /**
     * Scales a point with $sx and $sy:
     * (x, y) = x*sx, y*sy
     * 
     * @param type $p The point to scale.
     * @param type $sx The scale factor for the x-component.
     * @param type $sy The scale factor for the y-component.
     */
    private function _scale($p, $sx = 1, $sy = 1) {
        $x = $p->x;
        $y = $p->y;
        $p->x = $x * $sx;
        $p->y = $y * $sy;
    }

    /**
     * Applies a non uniform scaling in the direction specified by $ky and $ky.
     * (x, y) = x + y*ky, y +x*kx
     *
     * @param type $p
     * @param type $kx
     * @param type $ky
     */
    private function _shear($p, $kx, $ky) {
        $x = $p->x;
        $y = $p->y;
        $p->x = $x + $y * $ky;
        $p->y = $y + $x * $kx;
    }

    /**
     * Translates the point by the given $dx and $dy.
     * (x, y) = x + dx, y + dy
     * @param type $p
     * @param type $dx
     * @param type $dy
     */
    private function _translate($p, $dx, $dy) {
        $x = $p->x;
        $y = $p->y;
        $p->x = $x + $dx;
        $p->y = $y + $dy;
    }

    /**
     * 
     * @param type $line
     * @return type
     */
    private function _approximate_line($line) {
        if (count($line) != 2 || !($line[0] instanceof Point) || !($line[1] instanceof Point)) {
            throw new InvalidArgumentException(__FUNCTION__ . ": Argument is not an array of two points");
        } else if ($line[0]->_equals($line[1])) {
            //throw new InvalidArgumentException(__FUNCTION__.": {$line[0]} and {$line[1]} are equal.");
        }
        /*
          There are several ways to make a bezier curve look like a line. We need to have a threshold
          that determines how big the distance from a particular but arbitrarily chosen control point is
          from the original line. Naturally, such a distance must be rather small...
         * 
         * General principle: The points that determine the line must be the same as the at least
         * two points of the Bezier curve. The remaining points can be anywhere on the imaginable straight line.
         * This induces that also control points can represent the lines defining points and thus the resulting
         * bezier line overlaps (The control points become interpolate with the line points).
         */

        // First choose the target curve
        $make_cubic = (intval(time()) & 1) ? True : False; // Who cares? There's enough randomness already ...
        // A closure that gets a point somewhere near the line :P
        // Somewhere near depends heavily on the length of the size itself. How do we get
        // line lengths? Yep, I actually DO remember something for once from my maths courses :/
        $d = sqrt(pow(abs($line[0]->x - $line[1]->x), 2) + pow(abs($line[0]->y - $line[1]->y), 2));
        // The control points are allowed to be maximally a 10th of the line width apart from the line distance.
        $md = $d / secure_random_number(10, 50);

        $somewhere_near_the_line = function($line, $md) {
            // Such a point must be within the bounding rectangle of the line.
            $maxx = max($line[0]->x, $line[1]->x);
            $maxy = max($line[0]->y, $line[1]->y);
            $minx = min($line[0]->x, $line[1]->x);
            $miny = min($line[0]->y, $line[1]->y);

            // Now get a point on the line. 
            // Remember: f(x) = mx + d
            // But watch out! Lines parallel to the y-axis promise trouble! Just change these a bit :P
            if (($line[1]->x - $line[0]->x) == 0) {
                $line[1]->x += 1;
            }
            // Get the coefficient m and the (0, d)-y-intersection.
            $m = ($line[1]->y - $line[0]->y) / ($line[1]->x - $line[0]->x);
            $d = ($line[1]->x * $line[0]->y - $line[0]->x * $line[1]->y) / ($line[1]->x - $line[0]->x);

            if ($maxx < 0 || $minx < 0) { // Some strange cases oO
                $ma = max(abs($maxx), abs($minx));
                $mi = min(abs($maxx), abs($minx));
                $x = - secure_random_number($mi, $ma);
            } else {
                $x = secure_random_number($minx, $maxx);
            }
            $y = $m * $x + $d;

            // And move it away by $md :P
            return new Point($x + ((rand(0, 1) == 1) ? $md : -$md), $y + ((rand(0, 1) == 1) ? $md : -$md));
        };

        if ($make_cubic) {
            $p1 = $somewhere_near_the_line($line, $md);
            $p2 = $somewhere_near_the_line($line, $md);
            $curve = array($line[0], $p1, $p2, $line[1]);
        } else {
            $p1 = $somewhere_near_the_line($line, $md);
            $curve = array($line[0], $p1, $line[1]);
        }

        return $curve;
    }

    /**
     * Approximates a quadratic/cubic Bezier curves by $nlines lines. If $nlines is False or unset, a random $nlines 
     * between 10 and 20 is chosen.
     * 
     * @param type $curve An array of three or four points representing a quadratic or cubic Bezier curve.
     * @return type Returns an array of lines (array of two points).
     */
    private function _approximate_bezier($curve, $nlines = False) {
        // Check that we deal with Point arrays only.
        foreach ($curve as $point) {
            if (get_class($point) != "Point")
                throw new InvalidArgumentException("curve is not an array of points");
        }

        if (!$nlines || !isset($nlines)) {
            $nlines = secure_random_number(4, 20);
        }
        $approx_func = nUlL; // because PHP sucks!

        if (count($curve) == 3) { // Handle quadratic curves.
            $approx_func = function($curve, $nlines) {
                list($p1, $p2, $p3) = $curve;
                $last = $p1;
                $lines = array();
                for ($i = 0; $i <= $nlines; $i++) {
                    $t = $i / $nlines;
                    $t2 = $t * $t;
                    $mt = 1 - $t;
                    $mt2 = $mt * $mt;
                    $x = $p1->x * $mt2 + $p2->x * 2 * $mt * $t + $p3->x * $t2;
                    $y = $p1->y * $mt2 + $p2->y * 2 * $mt * $t + $p3->y * $t2;
                    $lines[] = array($last, new Point($x, $y));
                    $last = new Point($x, $y);
                }
                return $lines;
            };
        } else if (count($curve) == 4) { // Handle cubic curves.
            $approx_func = function($curve, $nlines) {
                list($p1, $p2, $p3, $p4) = $curve;
                $last = $p1;
                $lines = array();
                for ($i = 0; $i <= $nlines; $i++) {
                    $t = $i / $nlines;
                    $t2 = $t * $t;
                    $t3 = $t2 * $t;
                    $mt = 1 - $t;
                    $mt2 = $mt * $mt;
                    $mt3 = $mt2 * $mt;
                    $x = $p1->x * $mt3 + 3 * $p2->x * $mt2 * $t + 3 * $p3->x * $mt * $t2 + $p4->x * $t3;
                    $y = $p1->y * $mt3 + 3 * $p2->y * $mt2 * $t + 3 * $p3->y * $mt * $t2 + $p4->y * $t3;
                    $lines[] = array($last, new Point($x, $y));
                    $last = new Point($x, $y);
                }
                return $lines;
            };
        } else {
            throw new InvalidArgumentException("Can only approx. 3/4th degree curves.");
        }

        return $approx_func($curve, $nlines);
    }

    /**
     * This functon splits a curve at a given point t and returns two subcurves:
     * The right and left one. Note: The right array needs to be reversed before useage.
     * 
     * @param type $curve The curve to split.
     * @param type $t The parameter t where to split the curve.
     * @param type $left The left subcurve. Passed by reference.
     * @param type $right The right subcurve. Passed by reference.
     * @throws InvalidArgumentException If an array other than full of Points is given.
     */
    private function _split_curve($curve, $t, &$left, &$right) {
        // Check that we deal with Point arrays only.
        foreach ($curve as $point) {
            if (get_class($point) != "Point")
                throw new InvalidArgumentException("curve is not an array of points");
        }

        if (count($curve) == 1) {
            $left[] = $curve[0];
            $right[] = $curve[0];
        } else {
            $newpoints = array();
            for ($i = 0; $i < count($curve) - 1; $i++) {
                if ($i == 0)
                    $left[] = $curve[$i];
                if ($i == count($curve) - 2)
                    $right[] = $curve[$i + 1];

                $x = (1 - $t) * $curve[$i]->x + $t * $curve[$i + 1]->x;
                $y = (1 - $t) * $curve[$i]->y + $t * $curve[$i + 1]->y;
                $newpoints[] = new Point($x, $y);
            }
            $this->_split_curve($newpoints, $t, $left, $right);
        }
    }

    /**
     * Debug function.
     * 
     * @param string $msg
     */
    public static function D($msg) {
        if (self::DEBUG) {
            if (ini_get('display_errors') != 'On') {
                ini_set('display_errors', 'On');
            }
            error_reporting(E_ALL);
            echo '[Debug] - ' . $msg . '<br />';
        }
    }

    /**
     * Note that some action happened.
     * 
     * @param string $msg
     */
    public static function V($msg) {
        if (self::VERBOSE) {
            echo '[i] - ' . $msg . '<br />';
        }
    }

}

/**
 * A simple class to represent points. The components are public members, since working with
 * getters and setters is too pendantic in this context.
 */
class Point {

    public $x;
    public $y;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function __toString() {
        return 'Point(x=' . $this->x . ', y=' . $this->y . ')';
    }

    public function _equals($p) {
        if ($p instanceof Point) {
            return ($this->x == $p->x && $this->y == $p->y);
        } else {
            return False;
        }
    }

}

/**
 * For the future: Maybe hold some of the bytes in memory in a LUT such that we 
 * can avoid successive calls to openssl_random_pseudo_bytes().
 * 
 * Generates cryptographically secure random numbers within the range
 *      integer $start : start of the range
 *      integer $stop : end of the range.
 *
 *      Both parameters need to be positive. If you need a negative random value, just pass positiv values
 *      to the function and then make the return value negative on your own.
 *
 *      return value: A random integer within the range (including the edges). If the function returns False, something
 *      went wrong. Always check for false with "===" operator, otherwise a fail might shadow a valid
 *      random value: zero. You can pass the boolean parameter $secure. If it is true, the random value is
 *      cryptographically secure, else it was generated with rand().
 */
function secure_random_number($start, $stop, &$secure = "True") {
    static $calls = 0;
    $num_bytes = 1024;

    if ($start < 0 || $stop < 0 || $stop < $start)
        throw new InvalidArgumentException("Either stop<start or negative input parameters. Arguments: start=$start, stop=$stop");

    /* Just look for a random value within the difference of the range */
    $range = abs($stop - $start);

    $format = '';
    if ($range < 256) {
        $format = 'C';
    } elseif ($range < 65536) {
        $format = 'S';
        $num_bytes <<= 2;
    } elseif ($range >= 65536 && $range < 4294967296) {
        $format = 'L';
        $num_bytes <<= 3;
    }

    /* Get a blob of cryptographically secure random bytes */
    $binary = openssl_random_pseudo_bytes($num_bytes, $crypto_strong);
    if ($crypto_strong == False)
        throw new UnexpectedValueException("openssl_random_bytes cannot access secure PRNG");

    /* unpack data into determined format */
    $data = unpack($format . '*', $binary);
    if ($data == False)
        throw new UnexpectedValueException("unpack() failed.");

    foreach ($data as $value) {
        $value = intval($value, $base = 10);
        if ($value <= $range) {
            $secure = True;
            return ($start + $value);
        }
    }

    $calls++;
    if ($calls >= 50) { /* Fall back to rand() if the numbers of recursive calls exceed 50 */
        $secure = False;
        return rand($start, $stop);
    } else /* If we could't locate integer in the range, try again as long as we do not try more than 50 times. */
        return secure_random_number($start, $stop, $secure);
}

/**
 * Shuffle an array while preserving key/value mappings.
 * 
 * @param type $array The array to shuffle.
 * @return boolean Whether the action was successful.
 */
function shuffle_assoc(&$array) {
    $keys = array_keys($array);

    shuffle($keys);

    foreach ($keys as $key) {
        $new[$key] = $array[$key];
    }
    $array = $new;

    return true;
}

/**
 *  Some handy debugging functions. Send me a letter for Christmas! 
 * @param type $array The array to print recursivly.
 */
function D($a) {
    print "<pre>";
    print_r($a);
    print "</pre>";
}

/**
 * This file is part of the array_column library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2013 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
if (!function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null) {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1]) && !is_float($params[1]) && !is_string($params[1]) && $params[1] !== null && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2]) && !is_int($params[2]) && !is_float($params[2]) && !is_string($params[2]) && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {

            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }

        return $resultArray;
    }

}
?>