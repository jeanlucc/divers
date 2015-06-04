<?php

$map = array(
    '1' => 'toto_2',
    '2' => 'toto_3',
    '3' => 'toto_4',
    '4' => 'toto_5',
    '5' => 'toto_6',
    '6' => 'toto_7',
    '7' => 'toto_8',
    '8' => 'toto_9',
    '9' => 'toto_10',
    '10' => 'toto_11',
    '11' => 'toto_12',
    '12' => 'toto_13',
    '13' => 'toto_14',
    '14' => 'toto_15',
    '15' => 'toto_16',
    '16' => 'toto_17',
    '17' => 'toto_18',
    '18' => 'toto_19',
    '19' => 'toto_20',
    '20' => 'toto_21',
    '21' => 'toto_22',
    '22' => 'toto_23',
    '23' => 'toto_24',
    '24' => 'toto_25',
    '25' => 'toto_26',
    '26' => 'toto_27',
    '27' => 'toto_28',
    '28' => 'toto_29',
    '29' => 'toto_30',
    '30' => 'toto_31',
    '31' => 'toto_32',
    '32' => 'toto_33',
    '33' => 'toto_34',
    '34' => 'toto_35',
    '35' => 'toto_36',
    '36' => 'toto_37',
    '37' => 'toto_38',
    '38' => 'toto_39',
    '39' => 'toto_40',
    '40' => 'toto_41',
    '41' => 'toto_42',
    '42' => 'toto_43',
    '43' => 'toto_44',
    '44' => 'toto_45',
    '45' => 'toto_46',
    '46' => 'toto_47',
    '47' => 'toto_48',
    '48' => 'toto_49',
    '49' => 'toto_50',
    '50' => 'toto_51',
    '51' => 'toto_52',
    '52' => 'toto_53',
    '53' => 'toto_54',
    '54' => 'toto_55',
    '55' => 'toto_56',
    '56' => 'toto_57',
    '57' => 'toto_58',
    '58' => 'toto_59',
    '59' => 'toto_60',
    '60' => 'toto_61',
    '61' => 'toto_62',
    '62' => 'toto_63',
    '63' => 'toto_64',
    '64' => 'toto_65',
    '65' => 'toto_66',
    '66' => 'toto_67',
    '67' => 'toto_68',
    '68' => 'toto_69',
    '69' => 'toto_70',
    '70' => 'toto_71',
    '71' => 'toto_72',
    '72' => 'toto_73',
    '73' => 'toto_74',
    '74' => 'toto_75',
    '75' => 'toto_76',
    '76' => 'toto_77',
    '77' => 'toto_78',
    '78' => 'toto_79',
    '79' => 'toto_80',
    '80' => 'toto_81',
    '81' => 'toto_82',
    '82' => 'toto_83',
    '83' => 'toto_84',
    '84' => 'toto_85',
    '85' => 'toto_86',
    '86' => 'toto_87',
    '87' => 'toto_88',
    '88' => 'toto_89',
    '89' => 'toto_90',
    '90' => 'toto_91',
    '91' => 'toto_92',
    '92' => 'toto_93',
    '93' => 'toto_94',
    '94' => 'toto_95',
    '95' => 'toto_96',
    '96' => 'toto_97',
    '97' => 'toto_98',
    '98' => 'toto_99',
    '99' => 'toto_100',
);

$value = '${1} ${2} ${3} ${4} ${5} ${6} ${7} ${8} ${9} ${10} ${11} ${12} ${13} ${14} ${15} ${16} ${17} ${18} ${19} ${20} ${21} ${22} ${23} ${24} ${25} ${26} ${27} ${28} ${29} ${30} ${31} ${32} ${33} ${34} ${35} ${36} ${37} ${38} ${39} ${40} ${41} ${42} ${43} ${44} ${45} ${46} ${47} ${48} ${49} ${50} ${51} ${52} ${53} ${54} ${55} ${56} ${57} ${58} ${59} ${60} ${61} ${62} ${63} ${64} ${65} ${66} ${67} ${68} ${69} ${70} ${71} ${72} ${73} ${74} ${75} ${76} ${77} ${78} ${79} ${80} ${81} ${82} ${83} ${84} ${85} ${86} ${87} ${88} ${89} ${90} ${91} ${92} ${93} ${94} ${95} ${96} ${97} ${98} ${99}';

$keys = array_map(function($val) {return '${' . $val . '}';}, array_keys($map));
$values = array_values($map);

function f1_str($value, $keys, $values) {
    for($i=0; $i<100000; ++$i) {
        $new_string = str_replace($keys, $values, $value);
    }
    return $new_string;
}

function f1_preg($value, $map) {
    for($i=0; $i<100000; ++$i) {
        $new_string = preg_replace_callback(
            '/\${(.*?)}/',
            function($matches) use ($map) {
                return $map[$matches[1]];
            },
            $value
        );
    }
    return $new_string;
}

$start = microtime(true);
$str = f1_str($value, $keys, $values);
$stop = microtime(true);
$time_str = $stop - $start;

$start = microtime(true);
$preg = f1_preg($value, $map);
$stop = microtime(true);
$time_preg = $stop - $start;

echo 'time str  : ' . $time_str  . "\tresult :" . $str  . "\n";
echo 'time preg : ' . $time_preg . "\tresult :" . $preg . "\n";
