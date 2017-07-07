<?php
$page = 0;
if( isset( $_GET[ "page" ] ) ) $page = $_GET[ "page" ];
$itemsPerPage = 6;

$pagesPerPage = 5;


?>

<html>
	<head>
		<script src ="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<link rel="stylesheet" type="text/css" href="general.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
	</head>
	<body>
 		<div id="gallery-wrapper" class="gallery">

			<div id="main-image-block">
				<div id="main-image-wrapper"></div>
				<button class="button-display-left" onclick="previousImage( 'gallery-wrapper' )">&#10094;</button>
		 		<button class="button-display-right" onclick="nextImage( 'gallery-wrapper' )">&#10095;</button>
			</div>

			<div class="thumbnails">
				<?php
				$response = file_get_contents( "http://jsonplaceholder.typicode.com/photos" );

				$imageObjects = json_decode( $response );

				//error handling when the user reaches the max pages either manually or dynamically . validation points  --> 
				if( $page < 1 )
					$page = 1;
				elseif( $page > intval( count( $imageObjects ) / $itemsPerPage ) )
					$page = intval( count( $imageObjects ) / $itemsPerPage );

				//show 6 per page , it loops to every page and display 6 items 
				for( $index = ( $page * $itemsPerPage - $itemsPerPage ); $index < ( $page * $itemsPerPage ); $index += 1 )
				{
					$object = $imageObjects[ $index ];

				?><div class="image-wrapper">
					<img src="<?= $object->url; ?>" /></a>
					<!--
					<figcaption>
        				<p><?= $object->title; ?></p>
                	</figcaption>
                	-->
				</div><?php


				}

				?>
			</div>
			<div>
				<?php


				?>
				<div class="pagination">
					<a href="http://localhost:8888/Gallery/?page=<?= ( $page - $pagesPerPage ) ?>">&laquo;</a>

					<?php
					$offset = intval( $pagesPerPage / 2 );
					if( $page <= $offset ) $offset = $page - 1;
					if( $page >= count( $imageObjects ) - $offset ) $offset = count( $imageObjects ) - $offset;

					for( $index = ( $page - $offset ); $index < ( $page + $pagesPerPage - $offset ); $index += 1 )
					{
					?>

					<a href="http://localhost:8888/Gallery/?page=<?= $index ?>"><?= $index ?></a>

					<?php
					}
					?>

					<a href="#">&raquo;</a>
				</div>
			</div>
		</div> 
		


		<script>
			$( ".image-wrapper" ).on( "click", function( ){
				selectImage( this );
			} );

			//passing a genericID , can be reusable for any other galleries 
			function previousImage( galleryID )
			{
				var gallery = $( "#" + galleryID );
				var previous = $( ".selected", gallery ).prev( );


				// There is a previous element
				if( previous.length > 0 )
				{
					selectImage( previous );
				}
			}

			function nextImage( galleryID )
			{
				var gallery = $( "#" + galleryID );
				var next = $( ".selected", gallery ).next( );


				// There is a previous element
				if( next.length > 0 )
				{
					selectImage( next );
				}

			}

			function selectImage( imageWrapper )
			{
				var selected = $( ".selected" );
				var gallery = $( selected ).closest( ".gallery" );
				$( ".button-display-left", gallery ).css( "display", "inline-block" );
				$( ".button-display-right", gallery ).css( "display", "inline-block" );

				// Clone the img tag from the previous image-wrapper
				$( "#main-image-wrapper", gallery ).html( $( "img", imageWrapper ).clone( ) );

				$( selected ).removeClass( "selected" );
				$( imageWrapper ).addClass( "selected" );

				// If the imageWrapper has no next
				if( $( imageWrapper ).next( ).length == 0 )
				{
					$( ".button-display-right", gallery ).css( "display", "none" );
				}

				// If the imageWrapper has no previous
				if( $( imageWrapper ).prev( ).length == 0 )
				{
					$( ".button-display-left", gallery ).css( "display", "none" );
				}
			}
		</script>

		
	</body>
</html>
