<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Phpml\Association\Apriori;

$samples = [];
$labels = [];


$count = 250;
for($i=0; $i<$count; $i++){
	$arr_number = [];
	for($k=0; $k<4; $k++){
		$number = rand(1,9);
		if(!in_array($number,$arr_number)){
			$arr_number[$k] = $number;
		}else{
			$number = rand(1,9);
			$arr_number[$k] = $number;
		}
	}
	sort($arr_number);
	$samples[$i] = array_values($arr_number);
}

$rate_sp = 1/(
	pow(1,1)+
	pow(2,2)+
	pow(3,3)+
	pow(4,4)+
	pow(5,5)+
	pow(6,6)
);

$samples = array_values($samples);

// echo '<pre>';
// print_r($samples);
// echo '</pre>';

$rate_con = rand(1,8145060)/8145060;

$associator = new Apriori($support = 1/(250*4), $confidence = 1/(250*4));

$associator->train($samples, $labels);

$result = $associator->predict([1,2,4]);

echo '<pre>';
print_r($result);
echo '</pre>';

