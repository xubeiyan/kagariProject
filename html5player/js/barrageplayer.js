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
		timeLine = document.getElementById('timeline').getContext('2d'),
		barrage = document.getElementById('barrage').getContext('2d'),
		barragePool = [],
		// 格式化时间
		secondsFormat = function (sec) {
			var rawTime = Math.floor(sec),
				minutes = Math.floor(rawTime / 60) < 10 ? '0' + Math.floor(rawTime / 60) : Math.floor(rawTime / 60),
				seconds = rawTime % 60 < 10 ? '0' + rawTime % 60 : rawTime % 60;
			return minutes + ':' + seconds;
		},
		// 添加弹幕
		addBarrageToPool = function (barrageArray) {
			for (var i = 1; i < barrageArray.length; ++i) {
				if (barrageArray[i] == "#") {
					console.log('delete ' + barrageArray[i] + ' from barrage array...');
					barrageArray.splice(i, 1);
				}
			}
			for (var i = 1; i < barrageArray.length; ++i) {	
				var barrageElement = barrageArray[i].split(',');
				if (barrageElement[0] < videoSrc.currentTime) {
					var barrageObj = {
						x: 800,
						y: 25,
						type: barrageElement[1],
						color: barrageElement[4],
						speed: barrage.measureText(barrageElement[6]).width / 50,
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
			for (var i = 0; i < barragePool.length; ++i) {
				if (barragePool[i].type == '1') {
					barrage.font = '25px 微软雅黑';
					barrage.fillStyle = barragePool[i].color;
					barrage.fillText(barragePool[i].content, barragePool[i].x, barragePool[i].y);
				}
				barragePool[i].x -= barragePool[i].speed;
				if (barragePool[i].x + barrage.measureText(barragePool[i].content).width < 0) {
					console.log('delete ' + barragePool[i].content + " from barrage pool..");
					barragePool.splice(i, 1);
				}
			}
		},
		// 绘制每帧
		drawFrame = function () {
			videoCanvas.drawImage(videoSrc, 0, (videoElementHeight - videoHeight) / 2, videoWidth, videoHeight);
			videoPlayTime.innerText = secondsFormat(videoSrc.currentTime) + '/' + secondsFormat(videoSrc.duration);
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
	p = player(),
	danmuku = ['time, type, size, user, color, timestamp, content',
		'1.5,1,25,test,#FFFFFF,2015-09-01 09:21,咦！！！！',
		'6.25,1,25,test,#EEEEEE,2015-09-01 09:43,嗯我想想',
		'6.75,1,25,test,#EFABCD,2015-09-01 09:43,这这这',
		'8,1,25,test,#FEDCBA,2015-09-01 15:42,简直太可怕',
		'10,1,15,test,'];
	