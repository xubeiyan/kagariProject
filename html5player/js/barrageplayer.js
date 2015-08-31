var player = function () {
	var videoElementWidth = document.getElementById("video").width,
		videoElementHeight = document.getElementById("video").height,
		videoWidth = videoElementWidth,
		videoHeight = videoWidth * 9 / 16, //暂时没有获取原始视频的长宽的办法呢
		videoPlaying = false,
		videoPlayTime = document.getElementById('playtime'),
		videoSrc = document.getElementById("videosrc"),
		videoCanvas = document.getElementById("video").getContext('2d'),
		videoId,
		videoHiding = true,
		timeLine = document.getElementById('timeline').getContext('2d'),
		secondsFormat = function (sec) {
			var rawTime = Math.floor(sec),
				minutes = Math.floor(rawTime / 60) < 10 ? '0' + Math.floor(rawTime / 60) : Math.floor(rawTime / 60),
				seconds = rawTime % 60 < 10 ? '0' + rawTime % 60 : rawTime % 60;
			return minutes + ':' + seconds;
		},
		drawFrame = function () {
			videoCanvas.drawImage(videoSrc, 0, (videoElementHeight - videoHeight) / 2, videoWidth, videoHeight);
			videoPlayTime.innerText = secondsFormat(videoSrc.currentTime) + '/' + secondsFormat(videoSrc.duration);
			videoId = window.requestAnimationFrame(drawFrame);
		},
		that = {
			panel: function () {
				
			},
			// 发送弹幕
			sendDanmaku: function () {
				
			},
			volume: function () {
				
			},
			// 隐藏原视频
			hideVideo: function () {
				if (videoHiding == false) {
					videoSrc.style.display = 'none';
					videoHiding = true;
				} else {
					videoSrc.style.display = 'block';
					videoHiding = false;
				}
			},
			// 播放或暂停视频
			play: function () {
				if (videoPlaying == true) {
					videoSrc.pause();
					window.cancelAnimationFrame(videoId);
					videoPlaying = false;
				} else {
					videoSrc.play();
					videoId = window.requestAnimationFrame(drawFrame);
					videoPlaying = true;
				}
			}
		}
		return that;
	},
	p = player();