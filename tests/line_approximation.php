<?php

$line = array(new Point(100, 100), new Point(300, 400));

$curve = _approximate_line($line);
D($curve);

/* Now draw a little svg file to visualize that the original curve is exactly as a bezier path of $left and $right combined */
$svg = <<<'EOD'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
    "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
            
<svg 
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   width="800"
   height="800"
   version="1.1"
   style="background:#000">
  <title>Bezier curving splitting</title>
  <g>
    <path
       style="fill:none;stroke:#00ff00;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
       d="%1$s"
     />
  </g>
    
  <g transform="translate(0 , 0)">
     <path
       style="fill:none;stroke:#ff0000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
       d="%2$s"
  </g>
</svg>
EOD;

$kind = (count($curve) == 4) ? "C" : "Q";
$c1 = "M {$curve[0]->x} {$curve[0]->y} $kind";
unset($curve[0]);
foreach ($curve as $p) {
    $c1 .= " $p->x $p->y";
}

$c2 = "M {$line[0]->x} {$line[0]->y} L {$line[1]->x} {$line[1]->y}";
printf($svg, $c1, $c2);

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

}

function _approximate_line($line) {
    if (count($line) != 2) {
        throw new InvalidArgumentException("Argument is not an array of two points");
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
        // Remeber: f(x) = mx + d
        // But watch out! Lines parallel to the y-axis promise trouble! Just change these a bit :P
        if (($line[1]->x - $line[0]->x) == 0) {
            $line[1]->x++;
        }
        $m = ($line[1]->y - $line[0]->y) / ($line[1]->x - $line[0]->x);
        $d = ($line[1]->x * $line[0]->y - $line[0]->x * $line[1]->y) / ($line[1]->x - $line[0]->x);
        
        $x = secure_random_number($minx, $maxx);
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

function D($a) {
    print "<pre>";
    print_r($a);
    print "</pre>";
}
