<?php
require_once("../lib/bootloader.php");
$_GET["id"] = s($_GET["id"]);
$live = getLive($_GET["id"]);
if (!$_GET["id"] || !$live) {
    header("HTTP/1.1 404 Not Found");
    exit();
}
if (!getMe() && $live["privacy_mode"] == "3") {
    http_response_code(403);
    exit("ERR:この配信は非公開です。");
}
$mode = "dash";
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex">
    <link href="https://vjs.zencdn.net/7.1.0/video-js.css" rel="stylesheet">
    <style>
        html,
        body,
        .video-js {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <video id="my-video" class="video-js" controls preload="auto" data-setup="{}">
    <?php if ($mode === "hls") : ?><source src="http://<?=$_GET["rtmp"]?>/hls/<?=$_GET["id"]?>stream.m3u8" type='application/x-mpegURL'><?php endif; ?>
    <?php if ($mode === "rtmp") : ?><source src="rtmp://<?=$_GET["rtmp"]?>/<?=$_GET["id"]?>stream" type='rtmp/mp4'><?php endif; ?>
    <p class="vjs-no-js">
      To view this video please enable JavaScript, and consider upgrading to a web browser that
      <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
  </video>

    <script src="https://vjs.zencdn.net/7.1.0/video.js"></script>
<?php if ($mode === "dash") : ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dashjs/2.9.0/dash.all.min.js" integrity="sha256-WQRlnkRVJncPG+GSENXHuEb84m29r6Tm81aPxTmtZZ8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-dash/2.10.0/videojs-dash.min.js" integrity="sha256-xhLRr5mlvCCC7DndQjNURZOXGxwYUoB2VoF0mNUiuJc=" crossorigin="anonymous"></script>
<script>
var player = videojs('my-video');

player.ready(function() {
  player.src({
    src: '<?=(empty($_SERVER["HTTPS"]) ? "http" : "https")?>://<?=$_GET["rtmp"]?>/dash/<?=$_GET["id"]?>stream.mpd',
    type: 'application/dash+xml'
  });

  player.play();
});
</script>
<?php endif; ?>
</body>
</html>