 <?php 
 
$file_data = file_get_contents( __DIR__ . '/data/kladr.json' );
$kladr = json_decode($file_data); 

$res = [];

$_GET['type'] = htmlspecialchars($_GET['type']);

if($_GET['type']=='cities') {	
	foreach ($kladr as $city) {
		$res[]=Array(
			'name' => $city->city->name,
			'id' => $city->city->id,
			'classifierId' => $city->city->classifierId
		);
	}	 
};

if($_GET['type']=='streets' && $_GET['city']) {	
	$_GET['city'] = htmlspecialchars($_GET['city']);
	$_GET['key'] = mb_strtoupper  (htmlspecialchars($_GET['key']));

	foreach ($kladr as $city) {
		if($city->city->name===$_GET['city'])		
			foreach ($city->streets as $street) {
				
				if(!isset($_GET['key']) || stripos (mb_strtoupper  ($street->name), $_GET['key'])!==false) {
					
					if($_GET['format']==='short') {
						$res[]= $street->name;	
					}else {
						$res[]=Array(
							'name' => $street->name,
							'id' => $street->id,
							'classifierId' => $street->classifierId
						);
					}
					
				}
			}
	}	 
};
 

echo json_encode($res, JSON_UNESCAPED_UNICODE); 
 