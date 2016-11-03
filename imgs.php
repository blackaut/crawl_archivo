<?php
ob_start();
function url_origin( $s, $use_forwarded_host = false ) {
    $ssl      = ( !empty( $s[ 'HTTPS' ] ) && $s[ 'HTTPS' ] == 'on' );
    $sp       = strtolower( $s[ 'SERVER_PROTOCOL' ] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s[ 'SERVER_PORT' ];
    $port     = ( ( !$ssl && $port == '80' ) || ( $ssl && $port == '443' ) ) ? '' : ':' . $port;
    $host     = ( $use_forwarded_host && isset( $s[ 'HTTP_X_FORWARDED_HOST' ] ) ) ? $s[ 'HTTP_X_FORWARDED_HOST' ] : ( isset( $s[ 'HTTP_HOST' ] ) ? $s[ 'HTTP_HOST' ] : null );
    $host     = isset( $host ) ? $host : $s[ 'SERVER_NAME' ] . $port;
    return $protocol . '://' . $host;
}
function full_url( $s, $use_forwarded_host = false ) {
    return url_origin( $s, $use_forwarded_host ) . $s[ 'REQUEST_URI' ];
}
$absolute_url = full_url( $_SERVER );
$servername   = "localhost";
$username     = "root";
$password     = "root";
$dbname       = "crawl_museo";
$conn         = new mysqli( $servername, $username, $password, $dbname );
if ( $conn->connect_error ) {
    die( "Connection failed: " . $conn->connect_error );
} //$conn->connect_error
$sql    = "SELECT * FROM `imgs` where `crawl`='0' limit 0,1";
$result = $conn->query( $sql );
if ( $result->num_rows > 0 ) {
    while ( $row = $result->fetch_assoc() ) {
        echo "id: " . $row[ "id" ] . " - src: " . $row[ "src" ] . " " . $row[ "crawl" ] . "<br>";
        $sql  = "UPDATE `crawl_museo`.`imgs` SET `crawl` = '1' WHERE `imgs`.`id` = " . $row[ "id" ];
        $href = $row[ "src" ];
        if ( $conn->query( $sql ) === TRUE ) {
            echo "<hr>crawl src. $href ";
        } //$conn->query( $sql ) === TRUE
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $localFile = $_SERVER[ 'DOCUMENT_ROOT' ] . '/crawl/pics/' . $row[ "nombre" ];
        echo '<pre>';
        print_r( $row[ "src" ] );
        echo '</pre>';
        echo '<pre>';
        print_r( $localFile );
        echo '</pre>';
        if ( copy( $row[ "src" ], $localFile ) )
            doAgain();
        else
            doAgain();
    } //$row = $result->fetch_assoc()
} //$result->num_rows > 0
else {
    echo "0 results";
    doAgain();
}
function doAgain( ) {
    global $absolute_url;
    $url = $absolute_url;
    echo '
   <meta http-equiv="refresh" content="12;url=' . $url . '">
        <script type="text/javascript">
        setTimeout(function(){
            window.location.href = "' . $url . '"
        },13000);
        </script>
   ';
}
$conn->close();
ob_end_flush();