<?php

date_default_timezone_set("Asia/Bangkok");

$data = array(); // Bundle array of result
$data[0] = getThingSpeak('https://api.thingspeak.com/channels/661302/fields/1/last.json');
$data[1] = getThingSpeak('https://api.thingspeak.com/channels/661302/fields/2/last.json');

function getThingSpeak($api) {
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $api,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
	    "cache-control: no-cache",
	    "postman-token: b65dab7c-054e-fd09-9613-024339865e52"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  $results = json_decode($response);

	  $result['created_at'] = $results->created_at; // get time
	  $result['distance'] = end($results); // get distance

	  return $result;
	}
}

$isi = 0; // isi parkiran
$countData = count($data); // total parkiran
$updated = $data[0]['created_at'];

// Check Available Parkiran
for ($i=0; $i < $countData ; $i++) {
	if ($data[$i]['distance'] != 0 && $data[$i]['distance'] != 0) {
		$isi++; // tambah status
	}

	if ($data[$i]['created_at'] > $updated) {
		// Bandingkan mana data yang paling up to date
		$updated = $data[$i]['created_at']; 
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Parking Online</title>
	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
</head>
<body>
	<div class="container">
		<div class="row my-3">
			<div class="card mx-auto" style="width: 18rem;">
			  <div class="card-body text-center">
			    <h2 class="card-title pt-3">Parkir <?php echo $isi == $countData ? 'Penuh' : 'Tersedia' ?></h2>
			    <p class="card-text text-muted small"><?php echo $isi == $countData ? 'Maaf! Parkiran penuh.' : 'Parkir di tempat yang tersedia.' ?></p>
			    <div class="row px-2">
			    	<?php foreach ($data as $key => $value): ?>
				    	<div class="col-md-6">
					    	<i class="px-4 py-5 fas fa-car fa-2x border rounded text-white <?php echo $value['distance'] != 0 ? 'bg-danger' : 'bg-success' ?>"></i>
				    		<h4 class="pt-2">P<?php echo $key+1 ?></h4>
				    		<span class="small text-muted"><?php echo $value['distance'] != 0 ? 'Terisi' : 'Kosong' ?></span>
				    	</div>
			    	<?php endforeach ?>
			    </div>
			  </div>
			  <div class="card-footer">
			    <span class="text-muted small">Updated</span>
			    <span class="text-muted small float-right"><?php echo date('d M Y H:i', strtotime($updated)) ?></span>
			  </div>
			</div>
		</div>
	</div>
</body>
</html>