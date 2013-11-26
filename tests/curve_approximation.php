<?php

$curve = array(new Point(100, 100), new Point(300, 400), new Point(600, 340), new Point(700, 460));

$line = _approximate_bezier($curve);

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
    
  <g transform="translate(0 , 300)">
     <path
       style="fill:none;stroke:#ff0000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
       d="%2$s"
  </g>
</svg>
EOD;

$c1 = "M {$curve[0]->x} {$curve[0]->y} C";
unset($curve[0]);
foreach ($curve as $p) {
    $c1 .= " $p->x $p->y";
}

$c2 = "M {$line[0][0]->x} {$line[0][0]->y} L";
unset($line[0]);
$cnt = 0;
foreach ($line as $line) {
    foreach ($line as $p) {
        $cnt++;
        if ($cnt == 2) {
            $c2 .= " L";
            continue;
        } $c2 .= " $p->x $p->y";
    }
}
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

function _approximate_bezier($curve, $nlines = False) {
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
            for ($i = 0; $i < $nlines; $i++) {
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
            for ($i = 0; $i < $nlines; $i++) {
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