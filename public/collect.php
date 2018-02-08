<?php
require 'vendor/autoload.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

set_time_limit(0);
$url = 'http://vietlott.vn/vi/trung-thuong/ket-qua-trung-thuong/mega-6-45/?dayPrize=05/01/2018';
$time = new Carbon('2016/07/20');
$jp = 12000000000;
// writeExcel(getDataFromDate($time->format('d/m/Y')));
$arr_date_error = [
	'27/01/2017'
];
while(!$time->isToday()){
	if ($time->dayOfWeek === Carbon::SUNDAY || $time->dayOfWeek === Carbon::WEDNESDAY || $time->dayOfWeek === Carbon::FRIDAY) {
		if(!in_array($time->format('d/m/Y'), $arr_date_error))
		writeExcel(getDataFromDate($time->format('d/m/Y')));
	}
	$time->addDays(1);
}

echo "Done";

function getDataFromDate($date){
	$url = 'http://vietlott.vn/vi/trung-thuong/ket-qua-trung-thuong/mega-6-45/?dayPrize='.$date;
	$ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:19.0) Gecko/20100101 Firefox/19.0'));
  $content = curl_exec($ch);
  curl_close($ch);

  $html = new \DOMDocument();
  @$html->loadHTML($content);
  $html_result = new \DOMXPath($html);

  $arrReturn = [
  	'date' => $date,
  	'number'=>[],
  	'rate'=>[],
  	'count'=>[],
  	'jp'=>0,
  	'total'=>0
  ];
  for($i=0;$i<6;$i++){
  	$result_number = $html_result->query('//ul[@class="result-number"]/img')->item($i)->attributes->item(0)->nodeValue;
  	$result_number = str_replace('http://static.vietlott.vn/media/ball/','',$result_number);
  	$result_number = str_replace('.png?v=2.9','',$result_number);
  	$result_number = floatval($result_number);
  	$arrReturn['number'][$i] = $result_number;
  }

  for($i=0;$i<4;$i++){
  	$result_number = $html_result->query('//table[@class="table table-striped"]/tbody/tr')->item($i)->childNodes->item(5)->textContent;
  	// $result_number = str_replace('http://static.vietlott.vn/media/ball/','',$result_number);
  	// $result_number = str_replace('.png?v=2.9','',$result_number);
  	$result_number = floatval($result_number);
  	$arrReturn['count'][$i] = $result_number;
  }

  $new_jp = $html_result->query('//h4[@class="jackpot-value red"]/b')->item(0)->textContent;
  $new_jp = str_replace('đồng','',$new_jp);
	$new_jp = str_replace('.','',$new_jp);
	$new_jp = floatval($new_jp);
	$arrReturn['jp'] = $new_jp;

	$total = intval((($new_jp - $GLOBALS['jp'])/(55/100))/10000);

	$arrReturn['total'] = $total;
	if($arrReturn['count'][0] > 0){
		$GLOBALS['jp'] = 12000000000;
	}else{
		$GLOBALS['jp'] = $new_jp;
	}



  $arrReturn['rate'][0] = round($arrReturn['count'][0]/$total,8);
  $arrReturn['rate'][1] = round($arrReturn['count'][1]/$total,8);
  $arrReturn['rate'][2] = round($arrReturn['count'][2]/$total,8);
  $arrReturn['rate'][3] = round($arrReturn['count'][3]/$total,8);
  return $arrReturn;
}

function writeExcel($data){
	$data_path = 'data/first.csv';
	if(file_exists($data_path) == false){
		file_put_contents($data_path,'');
		$header = [
			'date',
			's1',
			's2',
			's3',
			's4',
			's5',
			's6',

			'g1',
			'g2',
			'g3',
			'g4',
			'r1',
			'r2',
			'r3',
			'r4',

			'jp',
			'total'
		];
		$out = fopen($data_path, 'a');
		fputcsv($out, $header);
		fclose($out);
	}

	$arr_data = [];
	$arr_data[0] = $data['date'];

	$arr_data[1] = $data['number'][0];
	$arr_data[2] = $data['number'][1];
	$arr_data[3] = $data['number'][2];
	$arr_data[4] = $data['number'][3];
	$arr_data[5] = $data['number'][4];
	$arr_data[6] = $data['number'][5];

	$arr_data[7] = $data['count'][0];
	$arr_data[8] = $data['count'][1];
	$arr_data[9] = $data['count'][2];
	$arr_data[10] = $data['count'][3];

	$arr_data[11] = $data['rate'][0];
	$arr_data[12] = $data['rate'][1];
	$arr_data[13] = $data['rate'][2];
	$arr_data[14] = $data['rate'][3];

	$arr_data[15] = $data['jp'];
	$arr_data[16] = $data['total'];

	$fo = fopen($data_path, 'a');
	fputcsv($fo, $arr_data);
	fclose($fo);
}

function pr($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}
// printf("Now: %s", $time->format('d/m/Y'));