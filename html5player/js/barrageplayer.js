var player = function () {
	var videoElementWidth = document.getElementById("video").width,
		videoElementHeight = document.getElementById("video").height,
		videoWidth = videoElementWidth,
		videoHeight = videoWidth * 9 / 16, //暂时没有获取原始视频的长宽的办法呢，只有按16:9的
		videoPlaying = false,
		videoPlayTime = document.getElementById('playtime'),
		videoSrc = document.getElementById("videosrc"),
		videoCanvas = document.getElementById("video").getContext('2d'),
		videoId,
		videoHiding = true,
		maxBarrageHeight = 30,
		timeLine = document.getElementById('timeline').getContext('2d'),
		barrage = document.getElementById('barrage').getContext('2d'),
		// 弹幕池内容为x, y, type, color, speed, content
		barragePool = [], 
		// 通道的最大值
		channelStatus = [0,0,0,0,0,0,0,0,0,0,
						0,0,0,0,0,0,0,0,0,0],
		getAvaliableChannel = function(speed) {
			for (var i = 1; i < Math.floor(videoElementHeight / maxBarrageHeight); ++i) {
				// 该通道为空的情况
				if (channelStatus[i] == 0) {
					channelStatus[i] += 1;
					return i;
				}
				// 通道不为空的情况
				var maxX = 0,
					speedWithMaxX = 0.5;
				for (var j = 0; j < barragePool.length; ++j) {
					if ((barragePool[j].y / maxBarrageHeight) == i ) {// 先判断是否是同一个通道
						if (barragePool[j].x + barrage.measureText(barragePool[j].content).width > maxX) {// 再计算先前的弹幕右端到屏幕左边的时间是否比后面的弹幕左端到屏幕左边的时间短
							maxX = barragePool[j].x + barrage.measureText(barragePool[j].content).width;
							speedWithMaxX = barragePool[j].speed;
						}
					}
				}
				if ((maxX / speedWithMaxX) <= (videoWidth / speed)) {
					console.log('barrage before: ' + maxX + ' with speed ' + speedWithMaxX + '; barrage after with speed ' + speed);
					channelStatus[i] += 1;
					return i;
				}
			}
		}
		// 格式化时间
		secondsFormat = function (sec) {
			var rawTime = Math.floor(sec),
				minutes = Math.floor(rawTime / 60) < 10 ? '0' + Math.floor(rawTime / 60) : Math.floor(rawTime / 60),
				seconds = rawTime % 60 < 10 ? '0' + rawTime % 60 : rawTime % 60;
			return minutes + ':' + seconds;
		},
		// 添加弹幕
		addBarrageToPool = function (barrageArray) {
			for (var i = barrageArray.length - 1; i > 0; --i) {
				if (barrageArray[i] == "#") {
					console.log('delete ' + barrageArray[i] + ' from barrage array...');
					barrageArray.splice(i, 1);
				}
			}
			for (var i = 1; i < barrageArray.length; ++i) {	
				var barrageElement = barrageArray[i].split(',');
				if (barrageElement[0] < videoSrc.currentTime) {
					var barrageSpeed = barrage.measureText(barrageElement[6]).width / 50,
						barrageObj = {
							x: videoWidth,
							y: getAvaliableChannel(barrageSpeed) * maxBarrageHeight, // maxBarrageHeight为最大的弹幕高度
							type: barrageElement[1],
							size: barrageElement[2] <= maxBarrageHeight ? barrageElement[2] : maxBarrageHeight,
							color: barrageElement[4],
							speed: barrageSpeed,
							content: barrageElement[6],
						};
					barragePool.push(barrageObj);
					console.log("add " + barrageObj.content + " width " + barrage.measureText(barrageElement[6]).width + " speed " + barrageObj.speed);
					// 要删除的弹幕赋值为‘#’
					barrageArray[i] = "#";
				}
			}
		},
		// 移动弹幕
		moveBarrage = function () {
			barrage.clearRect(0, 0, 800, 600);
			for (var i = barragePool.length - 1; i >= 0; --i) {
				if (barragePool[i].content == '#') {
					console.log('delete ' + barragePool[i].content + " from barrage pool.." + "The number of barrage in channel " + (barragePool[i].y / maxBarrageHeight) + " is " + (channelStatus[barragePool[i].y / maxBarrageHeight] - 1) + '...');
					channelStatus[barragePool[i].y / maxBarrageHeight] -= 1;
					barragePool.splice(i, 1);
				}
			}
			for (var i = 0; i < barragePool.length; ++i) {
				if (barragePool[i].type == '1') {
					barrage.font = barragePool[i].size + 'px 微软雅黑';
					barrage.fillStyle = barragePool[i].color;
					barrage.fillText(barragePool[i].content, barragePool[i].x, barragePool[i].y);
				}
				barragePool[i].x -= barragePool[i].speed;
				if (barragePool[i].x + barrage.measureText(barragePool[i].content).width < 0) {
					barragePool[i].content = "#";
				}
			}
		},
		// 绘制每帧
		drawFrame = function () {
			videoCanvas.drawImage(videoSrc, 0, (videoElementHeight - videoHeight) / 2, videoWidth, videoHeight);
			videoPlayTime.innerText = secondsFormat(videoSrc.currentTime) + '/' + secondsFormat(videoSrc.duration);
			timeLine.fillStyle = '#999';
			timeLine.fillRect(0, 0, videoSrc.currentTime / videoSrc.duration * document.getElementById('timeline').width, document.getElementById('timeline').height);
			addBarrageToPool(danmuku);
			moveBarrage();
			videoId = window.requestAnimationFrame(drawFrame);
		},
		that = {
			// 控制音量和弹幕选项面板 
			panel: function (a, b) {
				if (a == 'volume') {
					if (b == 'display') {
						document.getElementById('volume').style.display = 'block';
					} else if (b == 'hide') {
						document.getElementById('volume').style.display = 'none';
					}
				} else if (a == 'barrageoptionpanel') {
					if (b == 'display') {
						document.getElementById('barrageoptionpanel').style.display = 'block';
					} else if (b == 'hide') {
						document.getElementById('barrageoptionpanel').style.display = 'none';
					}
				}
			},
			// 发送弹幕
			sendDanmaku: function () {
				
			},
			// 调整音量
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
	
	