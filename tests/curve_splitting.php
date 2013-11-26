<?php

$curve = array(new Point(100, 100), new Point(300, 400), new Point(600, 340), new Point(700, 460));
$left = array();
$right = array();

split_curve($curve, 0.5, $left, $right);

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
       d="%2$s" />
       
     <path
       style="fill:none;stroke:#0000ff;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
       d="%3$s" />
  </g>
</svg>
EOD;

$c1 = "M {$curve[0]->x} {$curve[0]->y} C"; unset($curve[0]); foreach ($curve as $p) { $c1 .= " $p->x $p->y"; }

$c2 = "M {$left[0]->x} {$left[0]->y} C"; unset($left[0]); foreach ($left as $p) { $c2 .= " $p->x $p->y"; }

$right = array_reverse($right);
$c3 = "M {$right[0]->x} {$right[0]->y} C"; unset($right[0]); foreach ($right as $p) { $c3 .= " $p->x $p->y"; }

printf($svg, $c1, $c2, $c3);


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

function split_curve($curve, $t, &$left, &$right) {
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
        split_curve($newpoints, $t, $left, $right);
    }
}
