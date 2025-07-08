<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include __DIR__ . "/include/model.php";
?>

<!doctype html>
<html>
<head>
	<title><?php echo $username; ?> on last.fm</title>
	<link rel="stylesheet" type="text/css" href="styles/last.fm.css?v=<?php echo time(); ?>">
	<style type="text/css">body { background: #<?php echo $bgcolor; ?> }</style>
	<?php if($autorefresh) { ?><meta http-equiv="refresh" content="100"><?php } ?>
</head>
<body>
	<div id="lastfm" class="<?php echo $size; ?> center">
		<div id="topbar" class="<?php echo $color; ?>">
			<?php if($track['nowplaying']) { echo "now playing"; } else { echo "last played"; } ?> &middot; last.fm
		</div>
		<?php if(!empty($track['url'])) { ?><a target="_blank" href="<?php echo $track['url']; ?>"><?php } ?>

			<img id="artwork" src="<?php echo $track['image']; ?>">
		<?php if(!empty($track['url'])) { ?></a><?php } ?>

		<div id="songinfo">
			<artist><?php echo $track['artist']; ?></artist>
			<song><?php echo $track['name']; ?></song>
			<album><?php echo $track['album']; ?></album>
		</div>
	</div>
</body>
</html>