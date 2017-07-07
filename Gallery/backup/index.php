<?php
// When you switch to SQL on this page instead of remote data, it will load much faster


$page = 1;
if( isset( $_GET[ "page" ] ) ) $page = intval( $_GET[ "page" ] );
$searchString = "";
if( isset( $_GET[ "searchString" ] ) ) $searchString = $_GET[ "searchString" ];
$sortOption = "";
if( isset( $_GET[ "sort" ] ) ) $sortOption = $_GET[ "sort" ];


$itemsPerPage = 6;
$pagesPerPage = 5;

$titleSortSelected = "";

if( $sortOption == "title" )
	$titleSortSelected = "selected='selected'";

//dynamic sintax for server url & port
$URL = strtok( "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?' );

// If the links on the page are broken, please enter the url (with port) of this page on your server here
// $URL = "";
?>

<html>
	<head>
		<script src ="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<link rel="stylesheet" type="text/css" href="general.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
	</head>
	<body>
		<form id="search-form" method="GET">
			<input name="searchString" type="text" placeholder="Search..."/>
			<input type="submit" value="Go"/>

			<label>Filter By:</label>
			<select name="sort" onchange="$( '#search-form' ).submit( )">
				<option value="default" >None</option>
				<option value="title" <?= $titleSortSelected ?>>Title</option>
			</select>
           <!-- page number will be hidden out of the server url  -->
			<input name="page" type="hidden" value="<?= $page ?>" />
		</form>

 		<div id="gallery-wrapper" class="gallery">

			<div id="main-image-block">
				<figure id="main-image-wrapper"></figure>
				<div class="title"></div>

				<!-- this buttons are use to make the  selected imageresponsive  -->
				<button class="button-display-left" onclick="previousImage( 'gallery-wrapper' )">&#10094;</button>
		 		<button class="button-display-right" onclick="nextImage( 'gallery-wrapper' )">&#10095;</button>
			</div>

			<div class="thumbnails">
				<?php
				$response = file_get_contents( "http://jsonplaceholder.typicode.com/photos" );

				$imageObjects = json_decode( $response );

				// The number of images in the data
				$imageCount = count( $imageObjects );

				//error handling when the user reaches the max pages either manually or dynamically . validation points  --> 
				if( $page < 1 )
					$page = 1;
				elseif( $page > intval( $imageCount / $itemsPerPage ) )
					$page = intval( $imageCount / $itemsPerPage );


				// If the user is searching
				if( $searchString != "" )
				{
					for( $index = 0; $index < $imageCount; $index += 1 )
					{
						$object = $imageObjects[ $index ];
						if( $object->title == $searchString )
						{
							$imageObjects = [ $object ];

							$page = 1;
							break;
						}
					}
				}

				// sorting function by title 

				if( $sortOption == "title" )
				{
					function compareTitles( $a, $b )
					{
						return strcmp( $a->title, $b->title );
					}

					usort( $imageObjects, "compareTitles" );
				}

				// The number of images after searching
				$imageCount = count( $imageObjects );

				if( $itemsPerPage > $imageCount ) $itemsPerPage = $imageCount;

				//show 6 per page , it loops to every page and display 6 items 
				for( $index = ( $page * $itemsPerPage - $itemsPerPage ); $index < ( $page * $itemsPerPage ); $index += 1 )
				{
					$object = $imageObjects[ $index ];

				?><div class="image-wrapper">
					<img src="<?= $object->url; ?>" />
					<div class="title"><?= $object->title ?></div>
				</div><?php


				}

				?>
			</div>
			<div class="pagination">
				<?php
				$previousPage = $page - $pagesPerPage;
				if( $previousPage < 1 ) $previousPage = 1;
				?>
				<a href="<?= $URL ?>?page=<?= $previousPage ?>">&laquo;</a>

				<?php
				$offset = intval( $pagesPerPage / 2 );
				if( $page <= $offset ) $offset = $page - 1;
				if( $page >= $imageCount - $offset ) $offset = $imageCount - $offset;

				for( $index = ( $page - $offset ); $index < ( $page + $pagesPerPage - $offset ); $index += 1 )
				{
					$class = "";
					if( $index == $page ) $class = "selected-page";
				?>

				<a class="<?= $class ?>" href="<?= $URL ?>?page=<?= $index ?>"><?= $index ?></a>

				<?php
				}
				?>

				<?php
				$nextPage = $page + $pagesPerPage;
				if( $nextPage > $imageCount ) $nextPage = $imageCount;
				?>
				<a href="<?= $URL ?>?page=<?= $nextPage ?>">&raquo;</a>
			</div>
		</div> 
		


		<script>
			//selects the first image by default 
			selectImage( $( ".image-wrapper" ).first( ) );

			$( ".image-wrapper" ).on( "click", function( ){
				selectImage( this );
			} );

			//passing a genericID , can be reusable for any other galleries 
			function previousImage( galleryID )
			{
				var gallery = $( "#" + galleryID );
				var previous = $( ".selected", gallery ).prev( );


				// if there is a previous element
				if( previous.length > 0 )
				{
					selectImage( previous );
				}
			}

			function nextImage( galleryID )
			{
				var gallery = $( "#" + galleryID );
				var next = $( ".selected", gallery ).next( );


				// if there is a previous element
				if( next.length > 0 )
				{
					selectImage( next );
				}

			}

			function selectImage( imageWrapper )
			{
				$( ".selected" ).removeClass( "selected" );
				$( imageWrapper ).addClass( "selected" );

				var selected = imageWrapper;
				var gallery = $( selected ).closest( ".gallery" );
				$( ".button-display-left", gallery ).css( "display", "inline-block" );
				$( ".button-display-right", gallery ).css( "display", "inline-block" );
				$( ".thumbnails" ).css( "display", "block" );
				$( ".pagination" ).css( "display", "block" );

				// Clone the img tag from the previous image-wrapper
				$( "#main-image-wrapper", gallery ).html( $( "img", selected ).clone( ) );
				$( "#main-image-block .title", gallery ).html( $( ".title", selected ).html( ) );

				// If the imageWrapper has no next
				if( $( selected ).next( ).length == 0 )
				{
					$( ".button-display-right", gallery ).css( "display", "none" );
				}

				// If the imageWrapper has no previous
				if( $( selected ).prev( ).length == 0 )
				{
					$( ".button-display-left", gallery ).css( "display", "none" );
				}

				if( $( ".image-wrapper", gallery ).length == 1 )
				{
					$( ".thumbnails" ).css( "display", "none" );
					$( ".pagination" ).css( "display", "none" );
				}
			}
		</script>

		
	</body>
</html>
