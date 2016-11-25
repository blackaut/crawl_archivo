<?php

$directorio = "./pics/";
$ficheros1 = scandir( $directorio );
$images = array( 'status' => 'ok', 'data' => array() );
$data = array();
foreach ( $ficheros1 as $k => $v ) {
	$mediapath = $directorio . $v;

	if ( $v == '.DS_Store' || $v == '..' || $v == '.' )
	;
	#/no shit
	else {
		@$tamano = getimagesize( $mediapath );
		if ( @is_array( @$tamano ) ) {
			$d = array(
				'file' => $v,
				'data' => $tamano
			);
			array_push( $data, $d );
		}

	}
};
$images[ 'data' ] = $data;
$images[ 'numPic' ] = count( $data );
echo json_encode( $images );
?>