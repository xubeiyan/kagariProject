var player = function () {
	var videoElementWidth = document.getElementById("video").width,
		videoElementHeight = document.getElementById("video").height,
		videoWidth = videoElementWidth,
		videoHeight = videoWidth * 9 / 16, // 暂时没有获取原始视频的长宽的办法呢，只有按16:9的
		videoPlaying = false,
		videoPlayTime = document.getElementById('playtime'),
		videoSrc = document.getElementById("videosrc"),
		videoCanvas = document.getElementById("video").getContext('2d'),
		videoId,
		videoHiding = true,
		barrageShadowBlur = 10; // 阴影大小
		barrageShadowColor = '#000', // 阴影颜色
		maxBarrageHeight = 30,
		playButton = document.getElementById('playbutton'),
		timeLine = document.getElementById('timeline').getContext('2d'),
		barrage = document.getElementById('barrage').getContext('2d'),
		volumeCanvas = document.getElementById('volume').getContext('2d'),
		// 弹幕池内容为x, y, type, color, speed, content
		barragePool = [], 
		// 通道的最大值
		channelStatus = [0,0,0,0,0,0,0,0,0,0,
						0,0,0,0,0,0,0,0,0,0],
		//
		fillBarragePanel = function (danmaku) {
			var barragePanel = document.getElementById('barrage-panel-content'),
				adjustWidth = function (text, width) { // 调整宽度
					var outputText = '';
					for (var i = 0; i < text.length; ++i) {
						if (timeLine.measureText(outputText + text[i]).width > width) {
							break;
						}
						outputText += text[i];
					}
					return outputText;
				};
			for (var i = 1; i < danmaku.length; ++i) {
				var barrageArray = danmaku[i].split(',');
				barragePanel.innerHTML += '<div><span class="barrage-time">' + secondsFormat(barrageArray[0]) + '</span><span class="barrage-content" title=' + barrageArray[6] + '>' + adjustWidth(barrageArray[6], 230)+'</span><span class="barrage-date">' + barrageArray[5].slice(5) + '</span></div>\n';
			}
		},
		// 获取合适的通道
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
				// maxX大于屏幕边缘的情况,直接跳过计算下一行
				if (maxX <= videoWidth) {
					// 计算最右端弹幕是否比即将放入的弹幕快，是则放入该弹幕
					if ((maxX / speedWithMaxX) <= (videoWidth / speed)) {
						//console.log('barrage before: ' + maxX + ' with speed ' + speedWithMaxX + '; barrage after with speed ' + speed);
						channelStatus[i] += 1;
						return i;
					}
				} else {
					//console.log("maxX is " + maxX + ", try the next channel." );
				}
			}
			console.log("cannot get avaliable channel...");
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
					//console.log('delete ' + barrageArray[i] + ' from barrage array...');
					barrageArray.splice(i, 1);
				}
			}
			for (var i = 1; i < barrageArray.length; ++i) {	
				var barrageElement = barrageArray[i].split(',');
				if (barrageElement[0] - videoSrc.currentTime < 0.1 && barrageElement[0] - videoSrc.currentTime > -0.1) { // 放入弹幕池的时间是无法使用==来精确匹配的
					if (barrageElement[1] == 1) { // 弹幕类型为从右至左
						var barrageSpeed = barrage.measureText(barrageElement[6]).width / 50, //字符宽度除以50
							barrageObj = {
								x: videoWidth,
								y: getAvaliableChannel(barrageSpeed) * maxBarrageHeight, // maxBarrageHeight为最大的弹幕高度
								type: barrageElement[1],
								size: barrageElement[2] <= maxBarrageHeight ? barrageElement[2] : maxBarrageHeight,
								color: barrageElement[4],
								speed: barrageSpeed,
								content: barrageElement[6] //+ " w:" + barrage.measureText(barrageElement[6]).width + " s:" + barrageSpeed
							};
						barragePool.push(barrageObj);			
					} else if (barrageElement[1] == 2) { // 弹幕类型为下方悬停
						var stayTime = 3, // 3秒？
							barrageObj = {
								x: (videoWidth - barrage.measureText(barrageElement[6]).width) / 2,
								y: 550,
								type: barrageElement[1],
								size: barrageElement[2] <= maxBarrageHeight ? barrageElement[2] : maxBarrageHeight,
								color: barrageElement[4],
								dispearTime: videoSrc.currentTime + stayTime,
								content: barrageElement[6]
							}
						barragePool.push(barrageObj);
					} else if (barrageElement[1] == 3) {
						
					}
					//console.log("add " + barrageObj.content + " width " + barrage.measureText(barrageElement[6]).width + " speed " + barrageObj.speed);
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
					//console.log('delete ' + barragePool[i].content + " from barrage pool.." + "The number of barrage in channel " + (barragePool[i].y / maxBarrageHeight) + " is " + (channelStatus[barragePool[i].y / maxBarrageHeight] - 1) + '...');
					channelStatus[barragePool[i].y / maxBarrageHeight] -= 1;
					barragePool.splice(i, 1);
				}
			}
			for (var i = 0; i < barragePool.length; ++i) {
				barrage.font = barragePool[i].size + 'px 微软雅黑';
				barrage.shadowColor = barrageShadowColor;
				barrage.shadowBlur = barrageShadowBlur;
				barrage.fillStyle = barragePool[i].color;
				barrage.fillText(barragePool[i].content, barragePool[i].x, barragePool[i].y);
				
				if (barragePool[i].type == '1') {
					barragePool[i].x -= barragePool[i].speed;
					if (barragePool[i].x + barrage.measureText(barragePool[i].content).width < 0) {
						barragePool[i].content = "#";
					}
				} else if (barragePool[i].type == '2'){
					if (barragePool[i].dispearTime - videoSrc.currentTime < 0) {
						barragePool[i].content = "#";
					}
				}
				
				
			}
		},
		// 绘制每帧
		drawFrame = function () {
			// 初始化
			videoCanvas.drawImage(videoSrc, 0, (videoElementHeight - videoHeight) / 2, videoWidth, videoHeight);
			videoPlayTime.innerHTML = secondsFormat(videoSrc.currentTime) + '/' + secondsFormat(videoSrc.duration);
			timeLine.fillRect(1, 1, videoSrc.currentTime / videoSrc.duration * document.getElementById('timeline').width + 1, document.getElementById('timeline').height - 1);
			addBarrageToPool(danmuku);
			moveBarrage();
			videoId = window.requestAnimationFrame(drawFrame);
		},
		that = {
			// 初始化
			init: function () {
				timeLine.fillStyle = '#999';
				timeLine.font = '16px 微软雅黑';
				volumeCanvas.fillStyle = '#999';
				volumeCanvas.fillRect(0, 0, document.getElementById('volume').width, document.getElementById('volume').height);
				fillBarragePanel(danmuku);
			},
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
					playbutton.innerText = 'play';
					videoSrc.pause();
					window.cancelAnimationFrame(videoId);
					videoPlaying = false;
				} else {
					playbutton.innerText = 'pause';
					videoSrc.play();
					videoId = window.requestAnimationFrame(drawFrame);
					videoPlaying = true;
				}
			},
			// 单击鼠标
			click: function (item, event) {
				//console.log("volume:" + videoSrc.volume);
				// 音量
				if (item == 'volume') {
					var volumeWidth = document.getElementById('volume').width,
						volumeHeight = document.getElementById('volume').height;
					volumeCanvas.clearRect(0, 0, volumeWidth, volumeHeight);
					volumeCanvas.fillRect(0, event.offsetY, volumeWidth, volumeHeight);
					videoSrc.volume = (volumeHeight - event.offsetY) / volumeHeight;
				// 时间轴
				} else if (item == 'timeline') {
					var timeLineWidth = document.getElementById('timeline').width,
						timeLineHeight = document.getElementById('timeline').height;
					timeLine.clearRect(0, 0, timeLineWidth, timeLineHeight);
					timeLine.fillRect(1, 1, event.offsetX, timeLineHeight - 1); // 周围空1像素感觉好看点
					videoSrc.currentTime = videoSrc.duration * (event.offsetX - 1) / (timeLineWidth - 2);
				}
			}
		}
		return that;
	},
	p = player();
	
window.onload = p.init();	