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
function crawl_page( $url, $depth = 5 ) {
    static $seen = array( );
    static $hrefs = array( );
    static $imgs = array( );
    if ( isset( $seen[ $url ] ) || $depth === 0 ) {
        return;
    } //isset( $seen[ $url ] ) || $depth === 0
    $seen[ $url ] = true;
    $dom          = new DOMDocument( '1.0' );
    $pices_url    = explode( '/', $url );
    $basedir      = $pices_url[ count( $pices_url ) - 1 ];
    $preURL       = str_replace( $basedir, '', $url );
    @$dom->loadHTMLFile( $url );
    $anchors = $dom->getElementsByTagName( 'a' );
    $images  = $dom->getElementsByTagName( 'img' );
    foreach ( $images as $element ) {
        $src         = $element->getAttribute( 'src' );
        $pices_src   = explode( '/', $src );
        $basedir_src = $pices_src[ count( $pices_src ) - 1 ];
        $preURL_src  = str_replace( $basedir_src, '', $src );
        if ( $src == $basedir_src ) {
            $src = $preURL . $basedir_src;
        } //$src == $basedir_src
        @$tamano = getimagesize( $src );
        if ( $tamano[ 0 ] > 300 )
            array_push( $imgs, array(
                 'src' => $src,
                "tamano" => $tamano 
            ) );
    } //$images as $element
    foreach ( $anchors as $element ) {
        $flag         = false;
        $href         = $element->getAttribute( 'href' );
        $pices_href   = explode( '/', $href );
        $basedir_href = $pices_href[ count( $pices_href ) - 1 ];
        $preURL_href  = str_replace( $basedir_href, '', $href );
        if ( $href == $basedir_href ) {
            $href    = $preURL . $basedir_href;
            $findmes = array(
                 'javascript',
                'mailto:' 
            );
            $pos0    = strpos( $href, $findmes[ 0 ] );
            $pos1    = strpos( $href, $findmes[ 1 ] );
            $pos2    = strpos( $href, 'www.archivonacional.cl' );
            $pos3    = strpos( $href, '.html' );
            if ( $pos0 === false && $pos1 === false ) {
                if ( $pos2 !== false && $pos3 !== false ) {
                    $flag = true;
                } //$pos2 !== false && $pos3 !== false
            } //$pos0 === false && $pos1 === false
            if ( $flag ) {
                array_push( $hrefs, $href );
                inserto_url( $href );
            } //$flag
            $isImg     = explode( '.', $href );
            $allowImgs = array(
                 'jpg',
                'gif',
                'jpeg',
                'png' 
            );
            if ( count( $isImg ) > 1 ) {
                if ( in_array( strtolower( $isImg[ count( $isImg ) - 1 ] ), $allowImgs ) ) {
                    $src = $href;
                    @$tamano = getimagesize( $src );
                    if ( $tamano[ 0 ] > 600 )
                        array_push( $imgs, array(
                             'src' => $src,
                            "tamano" => $tamano 
                        ) );
                } //in_array( strtolower( $isImg[ count( $isImg ) - 1 ] ), $allowImgs )
            } //count( $isImg ) > 1
        } //$href == $basedir_href
    } //$anchors as $element
    inserto_url_imgs( $imgs );
}
function inserto_url_imgs( $imgs ) {
    global $conn;
    foreach ( $imgs as $i ) {
        $img    = $i[ 'src' ];
        $nombre = basename( $img );
        $sql    = "
  
   INSERT INTO `crawl_museo`.`imgs` ( `src`, `nombre` )
      SELECT * FROM (SELECT '$img', '$nombre') AS tmp
      WHERE NOT EXISTS (
          SELECT src FROM imgs WHERE src = '$img'
      ) LIMIT 1; 
   ";
        if ( $conn->query( $sql ) === TRUE ) {
            $last_id = $conn->insert_id;
            echo "<hr>New image. $img Last inserted ID is: $last_id";
            ob_flush();
        } //$conn->query( $sql ) === TRUE
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            ob_flush();
        }
    } //$imgs as $i
}
function inserto_url( $href ) {
    global $conn;
    $sql = "
   INSERT INTO `crawl_museo`.`urls` ( `url`, `crawl` )
      SELECT * FROM (SELECT '$href', '0') AS tmp
      WHERE NOT EXISTS (
          SELECT url FROM urls WHERE url = '$href'
      ) LIMIT 1;
        
   ";
    if ( $conn->query( $sql ) === TRUE ) {
        $last_id = $conn->insert_id;
        if ( $last_id !== 0 )
            echo "<hr>New url. $href Last inserted ID is: $last_id";
        ob_flush();
    } //$conn->query( $sql ) === TRUE
    else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        ob_flush();
    }
}
$sql    = "SELECT * FROM `urls` where `crawl`='0' limit 0,1";
$result = $conn->query( $sql );
$IO     = 0;
if ( $result->num_rows > 0 ) {
    while ( $row = $result->fetch_assoc() ) {
        echo "id: " . $row[ "id" ] . " - url: " . $row[ "url" ] . " " . $row[ "crawl" ] . "<br>";
        ob_flush();
        $sql  = "UPDATE `crawl_museo`.`urls` SET `crawl` = '1' WHERE `urls`.`id` = " . $row[ "id" ];
        $href = $row[ "url" ];
        if ( $conn->query( $sql ) === TRUE ) {
            echo "<hr>crawl url. $href ";
            ob_flush();
        } //$conn->query( $sql ) === TRUE
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            ob_flush();
        }
        crawl_page( $row[ "url" ], 1 );
        $IO++;
        if ( $result->num_rows >= $IO ) {
            doAgain();
        } //$result->num_rows >= $IO
    } //$row = $result->fetch_assoc()
} //$result->num_rows > 0
else {
    echo "0 results";
    ob_flush();
}
function doAgain( ) {
    global $absolute_url;
    $url = $absolute_url;
    echo '<h1>';
    echo $url;
    echo '</h1>';
    ob_flush();
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