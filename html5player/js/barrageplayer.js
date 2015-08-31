var player = function () {
	var videoElementWidth = document.getElementById("video").width,
		videoElementHeight = document.getElementById("video").height,
		videoWidth = videoElementWidth,
		videoHeight = videoWidth * 9 / 16,
		videoPlaying = false,
		videoSrc = document.getElementById("videosrc"),
		videoCanvas = document.getElementById("video").getContext('2d'),
		that = {
			panel: function () {
				
			},
			// 播放或暂停视频
			play: function () {
				if (videoPlaying == true) {
					videoSrc.pause();
					videoPlaying = false;
				} else {
					videoSrc.play();
					videoPlaying = true;
				}
			}
		}
		
		videoSrc.addEventListener('play', function () {
			var i = window.setInterval(function () { videoCanvas.drawImage(videoSrc, 0, (videoElementHeight - videoHeight) / 2, videoWidth, videoHeight)}, 20);
			}, false);
		//videoSrc.addEventListener('pause',)
		//videoSrc.addEventListener('ended',)
		
		return that;
	},
	p = player();