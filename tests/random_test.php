<?php

error_reporting(E_ALL);

echo "[";
foreach (range(0, 10000) as $i) {
    echo "(".secure_rand(0, 255).", ".secure_rand(0, 255)."), ";
}
echo "]";



function secure_rand($start, $stop, &$secure = "True", $calls = 0) {
    if ($start < 0 || $stop < 0 || $stop < $start) {
        throw new InvalidArgumentException("Either stop<start or negative input parameters. Arguments: start=$start, stop=$stop");
    }
    static $LUT; // Lookup table that holds always the last bytes as received by openssl_random_pseudo_bytes.
    static $last_lu;
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
    
    /* Before we do anything, lets see if we have a random value in the LUT within our range */
    if (is_array($LUT) && !empty($LUT) && $last_lu === $format) {
        foreach ($LUT as $key => $value) {
            if ($value >= $start && $value <= $stop) {
                $secure = True;
                unset($LUT[$key]); // Next run, next value, as my dad always said!
                return $value;
            }
        }
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
    $last_lu = $format;

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