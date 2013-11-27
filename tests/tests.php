<?php

error_reporting(E_ALL);

/**
 * @author Nikolai Tschacher <admin@incolumitas.com>
 * 
 * Generates cryptographically secure random numbers including the range $start to $stop with 
 * good performance (especiall for ranges from 0-255)!
 * Calls to openssl_random_pseudo_bytes() are cached in a array $LUT. 
 * For instance, you need only around 2 calls to openssl_random_pseudo_bytes in order to obtain 
 * 1000 random values between 0 and 200. This ensures good performance!
 *
 * Both parameters need to be positive. If you need a negative random value, just pass positiv values
 * to the function and then make the return value negative on your own.
 *
 * If the function returns False, something went wrong.
 * Always check for false with "===" operator, otherwise a fail might shadow a valid
 * random value: zero. You can pass the boolean parameter $secure. If it is true, the random value is
 * cryptographically secure, else it was generated with rand().
 * 
 * @staticvar array $LUT A lookup table to store bytes from calls to secure_random_number
 * @param int $start The bottom border of the range.
 * @param int $stop The top border of the range.
 * @param in bool $secure Whether the call to openssl_random_pseudo_bytes was made securely.
 * @param int $calls The number of calls already made.
 * @return int A random integer within the range (including the edges).
 * @throws InvalidArgumentException Thrown if the input range is invalid.
 * @throws UnexpectedValueException Thrown if openssl_random_pseudo_bytes was called unsecurely.
 * @throws ErrorException Thrown if unpack fails.
 */
function secure_rand($start, $stop, &$secure = "True", $calls = 0) {
    if ($start < 0 || $stop < 0 || $stop < $start) {
        throw new InvalidArgumentException("Either stop<start or negative input parameters. Arguments: start=$start, stop=$stop");
    }
    static $LUT; // Lookup table that holds always the last bytes as received by openssl_random_pseudo_bytes.
    /* Before we do anything, lets see if we have a random value in the LUT */
    if (is_array($LUT) && !empty($LUT)) {
        foreach ($LUT as $key => $value) {
            if ($value >= $start && $value <= $stop) {
                $secure = True;
                unset($LUT[$key]); // Next run, next value, as my dad always said!
                return $value;
            }
        }
    }
    $num_bytes = 1024;

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

    if ($crypto_strong == False) {
        throw new UnexpectedValueException("openssl_random_bytes cannot access secure PRNG");
    }

    /* unpack data into previously determined format */
    $data = unpack($format . '*', $binary);
    if ($data == False) {
        throw new ErrorException("unpack() failed.");
    }

    //Update lookup-table
    $LUT = $data;

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
    } else {/* If we could't locate integer in the range, try again as long as we do not try more than 50 times. */
        return secure_rand($start, $stop, $secure, $calls);
    }
}

/*
 * $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * $                 Some tests. Ignore.                 $
 * $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 */


function test($start, $stop) {
    static $num_called = 1;
    $val = secure_rand($start, $stop, $secure);
    echo "Random Value #$num_called is: ";
    var_dump($val);
    echo "Generated securely: " . (($secure == True) ? "yes" : "no") . "<br />";
    $num_called++;
}

function performance() {
    // My appraoch
    $start = microtime(true);
    // That is also very fast!
    foreach (range(0, 20000) as $i) {
        secure_rand(0, 200);
    }
    $stop = microtime(true);
    printf("Elapsed time with secure_random_number(): %.6f seconds<br />", $stop - $start);
    
    // With rand()
    $start = microtime(true);
    foreach (range(0, 20000) as $i) {
        rand(0, 200);
    }
    $stop = microtime(true);
    printf("Elapsed time with rand(): %.6f seconds<br />", $stop - $start);
}

test(100000000, 100100000); // Critical call. There's a high probability that the function will fail.
test(500, 2000000);
test(0, 60);

performance();