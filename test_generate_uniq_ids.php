<?php

/**
 * This file allows to bench several method to generate random strings.
 *
 * Disclaimer this is not a scientific review, everything should be challenged
 * for your needs and I may have make mistakes even for this use case (this may
 * well be because I lack of knowledge on random behavior in php and any other
 * language)
 *
 * The need is to avoid collisions and be able to create a folder tree with
 * three levels of around 1000 elements at each level, this allows to store
 * the file even on "old" file systems that does not handle well a big number
 * of files or directories. This choice (1000 elements on 4 levels (3
 * directories, on level of files) should be overkill for the vast majority of
 * web projects.
 *
 * TLDR; I chose generateRandomByteStringAndCustom23vs22and13 function.
 *
 * I calculated the number of files with some hypothesis:
 * - the number of upload per second is 3500 (this is the limit of AWS S3
 *   without prefixes if each upload takes less than a second) it could not be
 *   enough on peak but on average it should be more than needed
 * - the application leaves 10 years
 * - the collision probability is approximated to k^2/2N where k is the number
 *   of files and N is the number of total possible random names
 * - the probability should be below e-10 (in ten years at this high rate of
 *   uploads), not perfect but AWS S3 durability on one year for one file is
 *   e-9 so it should be more than enough in many cases
 *
 * - the used file system could be case insensitive which brings down the
 *   possible different characters of base 64 to 38 and the probability being
 *   doubled for letters the real random is even lower than that, I won't
 *   calculate it and say it is equivalent to 32 different charaters (5 bits)
 *   for simplicity. (If you store this amount of data on an insensitive case
 *   file system you might be worried but again e-10 on 10 years means the
 *   problem will come from elsewhere)
 *
 * This gives:
 * - 3500*86400*365*10 = 1 103 760 000 000 files in 10 years at constant 3500
 *   uploads per second
 * - (3500*86400*365*10)^2/2/32^23 = 1.47e-11 probability of collision with 23
 *   base 32 (or 64 as approximated above) characters
 * - (3500*86400*365*10)^2/2/16^29 = 7.33e-12 probability of collision with 29
 *   base 16 characters
 * - (3500*86400*365*10)^2/2/32^20/26^3 = 2.73e-11 probability of collision
 *   with 20 base 32 (or 64 as approximated above) characters and 3 letters
 *
 * Disclaimer the part where the folder are count is not a scientific approach
 * nevertheless it helps to grasp a practical behavior. The distribution could
 * be calculated but is not necessary for our purpose.
 *
 * Example of output:
 *
 * time for genereteUniqid22and13                                       :2.0179588794708         strrev(uniqid()).str_replace('.','',uniqid('', true))
 * time for generateRandomByteString28vs22and13                         :2.3407080173492         base64_encode(random_bytes(21))
 * time for generateRandomByteStringAndCustom23vs22and13                :2.5433180332184         (function() { $name = base64_encode(random_bytes(15)); return 'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 0, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 1, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 2); })()
 *
 * genereteUniqid22and13                        first  folder level: smallest folder has: 202 elements, largest folder has: 283 elements, there are total different folders: 4096
 * genereteUniqid22and13                        second folder level; smallest folder has: 108 elements, largest folder has: 2048 elements, there are total different folders: 498
 * genereteUniqid22and13                        third  folder level; smallest folder has: 472618 elements, largest folder has: 527382 elements, there are total different folders: 2
 * generateRandomByteString28vs22and13          first  folder level: smallest folder has: 196 elements, largest folder has: 307 elements, there are total different folders: 4096
 * generateRandomByteString28vs22and13          second folder level; smallest folder has: 190 elements, largest folder has: 303 elements, there are total different folders: 4096
 * generateRandomByteString28vs22and13          third  folder level; smallest folder has: 191 elements, largest folder has: 293 elements, there are total different folders: 4096
 * generateRandomByteStringAndCustom23vs22and13 first  folder level: smallest folder has: 529 elements, largest folder has: 690 elements, there are total different folders: 1664
 * generateRandomByteStringAndCustom23vs22and13 second folder level; smallest folder has: 509 elements, largest folder has: 673 elements, there are total different folders: 1664
 * generateRandomByteStringAndCustom23vs22and13 third  folder level; smallest folder has: 528 elements, largest folder has: 698 elements, there are total different folders: 1664
 *
 * Finally I chose to generate the file name with a custom mix of a base64 of
 * random_bytes(15) which gives 20 base 64 characters. Each 3 bytes gives 4
 * base 64 characters (64^4 = 256^3). This allow to get with the three first
 * groups of two characters 26*64 = 1664 folders which is significantly closer
 * to the arbitrary number of 1000 folders than 64*64=4096. On an insensitive
 * case file system it becomes 26*(64-26) = 988 not evenly distributed folders.
 *
 *
 * It is interesting to see that the use of uniqid even if faster is a poor
 * choice considering the hypothesese (again should be overkill on most
 * situations). If you use the first two base 16 characters you have only 256
 * folders this could be enough except uniqid is purely time based so these
 * two characters change only once in 6 month in the mean time there could be
 * lots of files in the folder, more than 10000 is quite easy (1 file every 26
 * minutes). You could use three but again the time is against you, you would
 * use 16 folders every six month, it is far from well distributed.
 *
 * For this reason we used strrev on uniqid, it puts the "random" part (well
 * fast changing part is more appropriate) at the beginning. But on a 3 level
 * of folder with 3 chars (16*16*16=4096 elements) the second level does not
 * changes fast. You can see this in the 1 000 000 loop there are only 500
 * folder. Nevertheless at a 3500 file per second this method should make a
 * good enough distribution. For the third folder again the distribution is
 * poorly random but might be enough for our purpose.
 */

function genereteUniqid13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        strrev(uniqid());
    }
}

function genereteUniqid22($n)
{
    for ($i = 0; $i < $n; ++$i) {
        strrev(str_replace('.','',uniqid('', true)));
    }
}

function genereteUniqid13and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        strrev(uniqid()).uniqid();
    }
}

function genereteUniqid22and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        strrev(uniqid()).str_replace('.','',uniqid('', true));
    }
}

function genereteUniqid13and13and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        strrev(uniqid()).uniqid().uniqid();
    }
}

function generateManualRandomString20vs13and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        $name = '';
        for ($j = 0; $j < 20; ++$j) {
            $name .= '0123456789abcdefghijklmnopqrstuvwxyz'[\random_int(0, 35)];
        }
    }
}

function generateManualRandomString27vs22and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        $name = '';
        for ($j = 0; $j < 27; ++$j) {
            $name .= '0123456789abcdefghijklmnopqrstuvwxyz'[\random_int(0, 35)];
        }
    }
}

function generateManualRandomString30vs13and13and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        $name = '';
        for ($j = 0; $j < 30; ++$j) {
            $name .= '0123456789abcdefghijklmnopqrstuvwxyz'[\random_int(0, 35)];
        }
    }
}

function generateShuffleRandomString20vs13and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 20)), 0, 20);
    }
}

function generateShuffleRandomString27vs22and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 27)), 0, 27);
    }
}

function generateShuffleRandomString30vs13and13and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 30)), 0, 30);
    }
}

function generateRandomByteString20vs13and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        base64_encode(random_bytes(15));
    }
}

function generateRandomByteString28vs22and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        base64_encode(random_bytes(21));
    }
}

function generateRandomByteString32vs13and13and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        base64_encode(random_bytes(24));
    }
}

function generateRandomByteStringAndCustom23vs22and13($n)
{
    for ($i = 0; $i < $n; ++$i) {
        $name = strtr(base64_encode(random_bytes(15)), '+/', '-_');
        'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 0, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 1, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 2);
    }
}

$n = 100000;

$start = microtime(true);
genereteUniqid13($n);
$stop = microtime(true);
$time_genereteUniqid13 = $stop - $start;
echo 'time for genereteUniqid13                                            :'.$time_genereteUniqid13."\n";

$start = microtime(true);
genereteUniqid22($n);
$stop = microtime(true);
$time_genereteUniqid22 = $stop - $start;
echo 'time for genereteUniqid22                                            :'.$time_genereteUniqid22."\n";

$start = microtime(true);
genereteUniqid13and13($n);
$stop = microtime(true);
$time_genereteUniqid13and13 = $stop - $start;
echo 'time for genereteUniqid13and13                                       :'.$time_genereteUniqid13and13."\n";

$start = microtime(true);
genereteUniqid22and13($n);
$stop = microtime(true);
$time_genereteUniqid22and13 = $stop - $start;
echo 'time for genereteUniqid22and13                                       :'.$time_genereteUniqid22and13."\n";

$start = microtime(true);
genereteUniqid13and13and13($n);
$stop = microtime(true);
$time_genereteUniqid13and13and13 = $stop - $start;
echo 'time for genereteUniqid13and13and13                                  :'.$time_genereteUniqid13and13and13."\n";

$start = microtime(true);
generateManualRandomString20vs13and13($n);
$stop = microtime(true);
$time_generateManualRandomString20vs13and13 = $stop - $start;
echo 'time for generateManualRandomString20vs13and13                       :'.$time_generateManualRandomString20vs13and13."\n";

$start = microtime(true);
generateManualRandomString27vs22and13($n);
$stop = microtime(true);
$time_generateManualRandomString27vs22and13 = $stop - $start;
echo 'time for generateManualRandomString27vs22and13                       :'.$time_generateManualRandomString27vs22and13."\n";

$start = microtime(true);
generateManualRandomString30vs13and13and13($n);
$stop = microtime(true);
$time_generateManualRandomString30vs13and13and13 = $stop - $start;
echo 'time for generateManualRandomString30vs13and13and13                  :'.$time_generateManualRandomString30vs13and13and13."\n";

$start = microtime(true);
generateShuffleRandomString20vs13and13($n);
$stop = microtime(true);
$time_generateShuffleRandomString20vs13and13 = $stop - $start;
echo 'time for generateShuffleRandomString20vs13and13                      :'.$time_generateShuffleRandomString20vs13and13."\n";

$start = microtime(true);
generateShuffleRandomString27vs22and13($n);
$stop = microtime(true);
$time_generateShuffleRandomString27vs22and13 = $stop - $start;
echo 'time for generateShuffleRandomString27vs22and13                      :'.$time_generateShuffleRandomString27vs22and13."\n";

$start = microtime(true);
generateShuffleRandomString30vs13and13and13($n);
$stop = microtime(true);
$time_generateShuffleRandomString30vs13and13and13 = $stop - $start;
echo 'time for generateShuffleRandomString30vs13and13and13                 :'.$time_generateShuffleRandomString30vs13and13and13."\n";

$start = microtime(true);
generateRandomByteString20vs13and13($n);
$stop = microtime(true);
$time_generateRandomByteString20vs13and13 = $stop - $start;
echo 'time for generateRandomByteString20vs13and13                         :'.$time_generateRandomByteString20vs13and13."\n";

$start = microtime(true);
generateRandomByteString28vs22and13($n);
$stop = microtime(true);
$time_generateRandomByteString28vs22and13 = $stop - $start;
echo 'time for generateRandomByteString28vs22and13                         :'.$time_generateRandomByteString28vs22and13."\n";

$start = microtime(true);
generateRandomByteString32vs13and13and13($n);
$stop = microtime(true);
$time_generateRandomByteString32vs13and13and13 = $stop - $start;
echo 'time for generateRandomByteString32vs13and13and13                    :'.$time_generateRandomByteString32vs13and13and13."\n";

$start = microtime(true);
generateRandomByteStringAndCustom23vs22and13($n);
$stop = microtime(true);
$time_generateRandomByteStringAndCustom23vs22and13 = $stop - $start;
echo 'time for generateRandomByteStringAndCustom23vs22and13                :'.$time_generateRandomByteStringAndCustom23vs22and13."\n";

echo "\n";

$firstFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $firstFolderNameList[] = substr(strrev(uniqid()).str_replace('.','',uniqid('', true)), 0, 3);
}
$firstFolderDistribution = array_count_values($firstFolderNameList);
echo 'genereteUniqid22and13                        first  folder level: smallest folder has: '.min($firstFolderDistribution).' elements, largest folder has: '.max($firstFolderDistribution).' elements, there are total different folders: '.count($firstFolderDistribution)."\n";
unset($firstFolderNameList);
unset($firstFolderDistribution);

$secondFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $secondFolderNameList[] = substr(strrev(uniqid()).str_replace('.','',uniqid('', true)), 3, 3);
}
$secondFolderDistribution = array_count_values($secondFolderNameList);
echo 'genereteUniqid22and13                        second folder level; smallest folder has: '.min($secondFolderDistribution).' elements, largest folder has: '.max($secondFolderDistribution).' elements, there are total different folders: '.count($secondFolderDistribution)."\n";
unset($secondFolderNameList);
unset($secondFolderDistribution);

$thirdFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $thirdFolderNameList[] = substr(strrev(uniqid()).str_replace('.','',uniqid('', true)), 6, 3);
}
$thirdFolderDistribution = array_count_values($thirdFolderNameList);
echo 'genereteUniqid22and13                        third  folder level; smallest folder has: '.min($thirdFolderDistribution).' elements, largest folder has: '.max($thirdFolderDistribution).' elements, there are total different folders: '.count($thirdFolderDistribution)."\n";
unset($thirdFolderNameList);
unset($thirdFolderDistribution);

$firstFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $firstFolderNameList[] = substr(strtr(base64_encode(random_bytes(21)), '+/', '-_'), 0, 2);
}
$firstFolderDistribution = array_count_values($firstFolderNameList);
echo 'generateRandomByteString28vs22and13          first  folder level: smallest folder has: '.min($firstFolderDistribution).' elements, largest folder has: '.max($firstFolderDistribution).' elements, there are total different folders: '.count($firstFolderDistribution)."\n";
unset($firstFolderNameList);
unset($firstFolderDistribution);

$secondFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $secondFolderNameList[] = substr(strtr(base64_encode(random_bytes(21)), '+/', '-_'), 2, 2);
}
$secondFolderDistribution = array_count_values($secondFolderNameList);
echo 'generateRandomByteString28vs22and13          second folder level; smallest folder has: '.min($secondFolderDistribution).' elements, largest folder has: '.max($secondFolderDistribution).' elements, there are total different folders: '.count($secondFolderDistribution)."\n";
unset($secondFolderNameList);
unset($secondFolderDistribution);

$thirdFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $thirdFolderNameList[] = substr(strtr(base64_encode(random_bytes(21)), '+/', '-_'), 4, 2);
}
$thirdFolderDistribution = array_count_values($thirdFolderNameList);
echo 'generateRandomByteString28vs22and13          third  folder level; smallest folder has: '.min($thirdFolderDistribution).' elements, largest folder has: '.max($thirdFolderDistribution).' elements, there are total different folders: '.count($thirdFolderDistribution)."\n";
unset($thirdFolderNameList);
unset($thirdFolderDistribution);

$firstFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $name = strtr(base64_encode(random_bytes(15)), '+/', '-_');
    $name = 'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 0, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 1, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 2);
    $firstFolderNameList[] = substr($name, 0, 2);
}
$firstFolderDistribution = array_count_values($firstFolderNameList);
echo 'generateRandomByteStringAndCustom23vs22and13 first  folder level: smallest folder has: '.min($firstFolderDistribution).' elements, largest folder has: '.max($firstFolderDistribution).' elements, there are total different folders: '.count($firstFolderDistribution)."\n";
unset($firstFolderNameList);
unset($firstFolderDistribution);

$secondFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $name = strtr(base64_encode(random_bytes(15)), '+/', '-_');
    $name = 'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 0, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 1, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 2);
    $secondFolderNameList[] = substr($name, 2, 2);
}
$secondFolderDistribution = array_count_values($secondFolderNameList);
echo 'generateRandomByteStringAndCustom23vs22and13 second folder level; smallest folder has: '.min($secondFolderDistribution).' elements, largest folder has: '.max($secondFolderDistribution).' elements, there are total different folders: '.count($secondFolderDistribution)."\n";
unset($secondFolderNameList);
unset($secondFolderDistribution);

$thirdFolderNameList = [];
for ($i = 0; $i < $n; $i++) {
    $name = strtr(base64_encode(random_bytes(15)), '+/', '-_');
    $name = 'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 0, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 1, 1).'abcdefghijklmnopqrstuvwxyz'[\mt_rand(0, 25)].substr($name, 2);
    $thirdFolderNameList[] = substr($name, 4, 2);
}
$thirdFolderDistribution = array_count_values($thirdFolderNameList);
echo 'generateRandomByteStringAndCustom23vs22and13 third  folder level; smallest folder has: '.min($thirdFolderDistribution).' elements, largest folder has: '.max($thirdFolderDistribution).' elements, there are total different folders: '.count($thirdFolderDistribution)."\n";
unset($thirdFolderNameList);
unset($thirdFolderDistribution);
