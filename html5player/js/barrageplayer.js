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
		videoHiding = true, // 隐藏原视频
		barrageShadowBlur = 10; // 阴影大小
		barrageShadowColor = '#000', // 阴影颜色
		maxBarrageHeight = 30,
		playButton = document.getElementById('playbutton'),
		timeLine = document.getElementById('timeline').getContext('2d'),
		barrage = document.getElementById('barrage').getContext('2d'),
		volumeCanvas = document.getElementById('volume').getContext('2d'),
		// 排序好的弹幕列表
		sortedBarrageList = [],
		// 下一个显示弹幕序号
		nextBarrageIndex = 0,
		// 弹幕池内容为x, y, type, color, speed, content
		barragePool = [], 
		// 通道的最大值，此为从右至左部分
		channelStatus = [0,0,0,0,0,0,0,0,0,0,
						0,0,0,0,0,0,0,0,0,0],
		// 通道的最大值, 此为上部悬停和下部悬停
		channelStatus2 = [0,0,0,0,0,0,0,0,0,0,
						0,0,0,0,0,0,0,0,0,0],
		// 调整宽度				
		adjustWidth = function (text, width) { 
			var outputText = '';
			for (var i = 0; i < text.length; ++i) {
				if (timeLine.measureText(outputText + text[i]).width > width) {
					break;
				}
				outputText += text[i];
			}
			return outputText;
		},
		// 输出到右边
		fillBarragePanel = function (danmaku) {
			var barragePanel = document.getElementById('barrage-panel-content'),
				adjustWidth = function (text, width) { // 调整宽度
					var outputText = '';
					for (var i = 0; i < text.length; ++i) {
						if (timeLine.measureText(outputText + text[i]).width > width) {
							return outputText + '...';
						}
						outputText += text[i];
					}
					return outputText;
				};
			for (var i = 0; i < danmaku.length; ++i) {
				var barrageArray = danmaku[i].split(','),
					barrageContent = barrageArray[6];
				if (barrageArray[1] == '4') {
					barrageContent = barrageContent.split('|')[1].split(':')[1];
				}
				barragePanel.innerHTML += '<div><span class="barrage-time">' + secondsFormat(barrageArray[0]) + '</span><span class="barrage-content" title="' + barrageArray[6] + '">' + adjustWidth(barrageContent, 210)+'</span><span class="barrage-date">' + barrageArray[5].slice(5) + '</span></div>\n';
			}
		},
		// 获取合适的通道
		getAvaliableChannel = function(speed, type) {
			if (type == 1) { //是从右往左型弹幕
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
				console.log("getAvaliableChannel: cannot get avaliable channel...");			
			} else if (type == 2 || type == 4) { //下部悬停
				//console.log("type:" + type);
				for (var i = Math.floor(videoElementHeight / maxBarrageHeight); i >= 1; --i) { //从下往上
					if (channelStatus2[i] == 0) {
						channelStatus2[i] = 1;
						return i;
					}
				}
			} else if (type == 3) { //上部悬停
				for (var i = 1; i <Math.floor(videoElementHeight / maxBarrageHeight); ++i) {
					if (channelStatus2[i] == 0) {
						channelStatus2[i] = 1;
						return i;
					}
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
		// 格式化日期
		dateFormat = function (yearFlag) {
			var date = new Date(),
				year = date.getFullYear(),
				month = date.getMonth() + 1,
				day = date.getDate(),
				hour = date.getHours(),
				minute = date.getMinutes(),
				addZero = function (time) {
					return time < 10 ? '0' + time : time;
				}
			if (yearFlag == undefined) {
				return addZero(month) + '-' + addZero(day) + ' ' + addZero(hour) + ':' + addZero(minute);
			} else if (yearFlag == 'year') {
				return year + '-' + addZero(month) + '-' + addZero(day) + ' ' + addZero(hour) + ':' + addZero(minute);
			}
		},
		// 获取待发送弹幕大小和颜色
		getBarrageDetails = function () {
			var size = parseInt(document.getElementById("barrage-size").value, 10),
				color = document.getElementById("barrage-color").value,
				barrageDetail = {};
			if (isNaN(size)) {
				size = 25;
			}
			if (size > 30) { // 最大30px，最小12px
				size = 30;
			} else if (size < 6) {
				size = 6;
			}
			
			if (color[0] != '#') {
				color = '#FFF';
			}
			if (isNaN(parseInt(color.substring(1), 16))) {
				color = '#FFF';
			}
			barrageDetail.color = color;
			barrageDetail.size = size;
			return barrageDetail;
		},
		// 对弹幕出现时间进行排序
		sortBarrages = function (danmuku) {
			// 排序函数
			var toSortList = danmuku.slice(1),
				sortFunc = function (a, b) {
					var timeA = a.split(',')[0],
						timeB = b.split(',')[0];
					return timeA - timeB;
				};
			return toSortList.sort(sortFunc)
			
		},
		// 更改视频时间之后重新获取弹幕下标
		getCurrentBarrageIndex = function (videoTime) {
			console.log('getCurrentBarrageIndex: change video time to ' + videoTime);
			if (sortedBarrageList.length == 0) {
				return -1;
			}
			// 在第0个弹幕之前
			if (videoTime < sortedBarrageList[0]) {
				console.log('getCurrentBarrageIndex: barrage index change to 0');
				return 0;
			}
			// 从0比较到length - 1
			for (var i = 0; i < sortedBarrageList.length - 1; ++i) {
				if (videoTime >= sortedBarrageList[i].split(',')[0] && videoTime < sortedBarrageList[i + 1].split(',')[0]) {
					console.log('getCurrentBarrageIndex: barrage index change to ' + (i + 1));
					return i + 1;
				}
				
			}
			return sortedBarrageList.length;
		}
		// 添加弹幕至弹幕池中
		addBarrageToPool = function (barrageArray) {
			var FROMR2L = '1',
				BOTTOMSTAY = '2',
				TOPSTAY = '3',
				ADVANCE = '4';
				
			// 如果弹幕列表里什么都没有则什么都不干
			if (barrageArray.length == 0) {
				return;
			}
				
			// 如果已经加载完则什么都不干
			if (nextBarrageIndex >= barrageArray.length) {
				return;
			}
			
			var barrageElement = barrageArray[nextBarrageIndex].split(','),
				barrageSize = barrageElement[2] <= maxBarrageHeight ? barrageElement[2] : maxBarrageHeight;
			// console.log("barrage:" + barrageArray[i]);
			barrage.font = barrageSize + 'px 微软雅黑';
			if (videoSrc.currentTime > barrageElement[0]) { // 现已修改为超过即放入弹幕池中
				nextBarrageIndex += 1; // 放入了则指向下一条
				if (barrageElement[1] == FROMR2L) { // 弹幕类型为从右至左
					var barrageSpeed = barrage.measureText(barrageElement[6]).width / 50, //字符宽度除以50
						barrageObj = {
							x: videoWidth,
							y: getAvaliableChannel(barrageSpeed, barrageElement[1]) * maxBarrageHeight, // maxBarrageHeight为最大的弹幕高度
							type: barrageElement[1],
							size: barrageSize,
							color: barrageElement[4],
							speed: barrageSpeed,
							content: barrageElement[6] //+ " w:" + barrage.measureText(barrageElement[6]).width + " s:" + barrageSpeed
						};			
				} else if (barrageElement[1] == BOTTOMSTAY || barrageElement[1] == TOPSTAY) { // 弹幕类型为下方悬停和上方悬停
					var stayTime = 3, // 3秒？
						barrageObj = {
							x: (videoWidth - barrage.measureText(barrageElement[6]).width) / 2,
							y: getAvaliableChannel(0, barrageElement[1]) * maxBarrageHeight,
							type: barrageElement[1],
							size: barrageElement[2] <= maxBarrageHeight ? barrageElement[2] : maxBarrageHeight,
							color: barrageElement[4],
							dispearTime: videoSrc.currentTime + stayTime,
							content: barrageElement[6]
						}
					//console.log("y=", barrageObj.y);
				} else if (barrageElement[1] == ADVANCE) { // 高级弹幕
					
					var advance = barrageElement[6].split('|'),
						dispearTime,// 消失时间
						x,			// 出现位置x
						content;	// 弹幕内容
						
					for (var i = 0; i < advance.length; ++i) {
						if (advance[i].substring(0, 3) == 'st:') {
							var num = parseFloat(advance[i].substring(3));
							if (!isNaN(num)) {
								dispearTime = videoSrc.currentTime + num;
								//console.log("dispearTime:" + videoSrc.currentTime + num);
							} 
							
						} else if (advance[i].substring(0, 3) == "ct:") {
							var cont = advance[i].substring(3)
							
							x = (videoWidth - barrage.measureText(cont).width) / 2;
							content = cont;
						}
						
					}
					var barrageObj = {
							x: x,
							y: getAvaliableChannel(0, barrageElement[1]) * maxBarrageHeight,
							type: barrageElement[1],
							size: barrageElement[2] <= maxBarrageHeight ? barrageElement[2] : maxBarrageHeight,
							color: barrageElement[4],
							dispearTime: dispearTime,
							content: content
						};
					//console.log("advance barrage:" + barrageObj.content);
					
				}
				//console.log("add " + barrageObj.content + " width " + barrage.measureText(barrageElement[6]).width + " speed " + barrageObj.speed);
				barragePool.push(barrageObj);
				// 要删除的弹幕赋值为‘#’
				// barrageArray[i] = "#";
			}
			
			for (var i = barrageArray.length - 1; i > 0; --i) {
				if (barrageArray[i] == "#") {
					barrageArray.splice(i, 1);
				}
			}
		},
		// 移动弹幕
		moveBarrage = function () {
			var FROMR2L = '1',
				BOTTOMSTAY = '2',
				TOPSTAY = '3',
				ADVANCE = '4';
				
			barrage.clearRect(0, 0, 800, 600);
			for (var i = barragePool.length - 1; i >= 0; --i) {
				if (barragePool[i].content == '#') {
					if (barragePool[i].type == 1) {
						//console.log('delete ' + barragePool[i].content + " from barrage pool.." + "The number of barrage in channel " + (barragePool[i].y / maxBarrageHeight) + " is " + (channelStatus[barragePool[i].y / maxBarrageHeight] - 1) + '...');
						channelStatus[barragePool[i].y / maxBarrageHeight] -= 1;
					} else if (barragePool[i].type == 2 || barragePool[i].type == 3 || barragePool[i].type == 4) {
						channelStatus2[barragePool[i].y / maxBarrageHeight] -= 1;
					}
					barragePool.splice(i, 1);
				}
			}
			for (var i = 0; i < barragePool.length; ++i) {
				barrage.font = barragePool[i].size + 'px 微软雅黑';
				barrage.shadowColor = barrageShadowColor;
				barrage.shadowBlur = barrageShadowBlur;
				barrage.fillStyle = barragePool[i].color;
				barrage.fillText(barragePool[i].content, barragePool[i].x, barragePool[i].y);
				
				if (barragePool[i].type == FROMR2L) {
					barragePool[i].x -= barragePool[i].speed;
					if (barragePool[i].x + barrage.measureText(barragePool[i].content).width < 0) {
						barragePool[i].content = "#";
					}
				} else if (barragePool[i].type == BOTTOMSTAY || barragePool[i].type == TOPSTAY || barragePool[i].type == ADVANCE) {
					if (barragePool[i].dispearTime - videoSrc.currentTime < 0) {
						barragePool[i].content = "#";
					}
				} 	
			}
		},
		// 绘制时间轴
		drawTimeLine = function () {
			var timeLineElement = document.getElementById('timeline'),
				timeLineBufferColor = '#ddd',
				timeLinePlayedColor = '#aaf',
				timeLineMargin = { // 上下左右留白
					top: 1,
					bottom: 1,
					left: 1,
					right: 1
				},
				timeLinePlayed = {
					x: 0 + timeLineMargin.left,
					y: 0 + timeLineMargin.top + 5,
					width: videoSrc.currentTime / videoSrc.duration * (timeLineElement.width - timeLineMargin.left - timeLineMargin.right) + 0 + timeLineMargin.left,
					height: timeLineElement.height - (timeLineMargin.top + 5) - timeLineMargin.bottom - 5,
				};
				
			timeLine.clearRect(0, 0, timeLineElement.width, timeLineElement.height);
			timeLine.fillStyle = timeLineBufferColor;
			
			for (var i = 0; i < videoSrc.buffered.length; ++i) {
				var x = videoSrc.buffered.start(i) / videoSrc.duration * (timeLineElement.width - timeLineMargin.right - timeLineMargin.left) + timeLineMargin.left,
					y = 0 + timeLineMargin.top,
					width = (videoSrc.buffered.end(i) - videoSrc.buffered.start(i)) / videoSrc.duration * (timeLineElement.width - timeLineMargin.right - timeLineMargin.left), 
					height = timeLineElement.height - timeLineMargin.top - timeLineMargin.bottom;
				
				timeLine.fillRect(x, y, width, height);
			}
			
			timeLine.fillStyle = timeLinePlayedColor;

			timeLine.fillRect(timeLinePlayed.x, timeLinePlayed.y, timeLinePlayed.width, timeLinePlayed.height);	
			
		},
		// 绘制音量条
		drawVolume = function (value) {
			var volumeWidth = document.getElementById('volume').width,
				volumeHeight = document.getElementById('volume').height,
				volumeBackColor = '#eee',
				volumeFrontColor = '#315CFF';
			volumeCanvas.clearRect(0, 0, volumeWidth, volumeHeight);
			
			volumeCanvas.fillStyle = volumeBackColor;
			volumeCanvas.fillRect(0, 0, volumeWidth, volumeHeight);
			
			volumeCanvas.fillStyle = volumeFrontColor;
			volumeCanvas.fillRect(0, value, volumeWidth, volumeHeight);
			
			videoSrc.volume = Math.round((volumeHeight - value) / volumeHeight * 100) / 100;
		},
		// 绘制每帧
		drawFrame = function () {
			// 初始化
			videoCanvas.drawImage(videoSrc, 0, (videoElementHeight - videoHeight) / 2, videoWidth, videoHeight);
			videoPlayTime.innerHTML = secondsFormat(videoSrc.currentTime) + '/' + secondsFormat(videoSrc.duration);
			//timeLine.fillRect(1, 1, videoSrc.currentTime / videoSrc.duration * document.getElementById('timeline').width + 1, document.getElementById('timeline').height - 1);
			drawTimeLine();
			addBarrageToPool(sortedBarrageList);
			moveBarrage();
			videoId = window.requestAnimationFrame(drawFrame);
		},
		// 发送弹幕
		sendBarrage = function () {
			var message = document.getElementById("message").value,
				barrageType = document.getElementById("barragetype").value,
				barragePanel = document.getElementById('barrage-panel-content'),
				date = new Date();
			document.getElementById("message").value = '';
			if (message == "") {
				return;
			}
			if (barrageType == 1) {
				var barrageSpeed = barrage.measureText(message).width / 50, //字符宽度除以50
					barrageObj = {
						x: videoWidth,
						y: getAvaliableChannel(barrageSpeed, barrageType) * maxBarrageHeight, // maxBarrageHeight为最大的弹幕高度
						type: barrageType,
						size: getBarrageDetails().size,
						color: getBarrageDetails().color,
						speed: barrageSpeed,
						content: message 
					};
				barragePool.push(barrageObj);
				sendBarrageToBackend(barrageObj); // 后台
			} else if (barrageType == 2 || barrageType == 4) {
				var stayTime = 3,
					barrageObj = {
						x: (videoWidth - barrage.measureText(message).width) / 2,
						y: getAvaliableChannel(0, barrageType) * maxBarrageHeight,
						type: barrageType,
						size: getBarrageDetails().size,
						color: getBarrageDetails().color,
						dispearTime: videoSrc.currentTime + stayTime,
						content: message
					};
				barragePool.push(barrageObj);
				sendBarrageToBackend(barrageObj); // 后台
				//console.log('type 2 put in...');
			} else if (barrageType == 3) {
				var stayTime = 3,
					barrageObj = {
						x: (videoWidth - barrage.measureText(message).width) / 2,
						y: getAvaliableChannel(0, barrageType) * maxBarrageHeight,
						type: barrageType,
						size: getBarrageDetails().size,
						color: getBarrageDetails().color,
						dispearTime: videoSrc.currentTime + stayTime,
						content: message
					};
				barragePool.push(barrageObj);
				sendBarrageToBackend(barrageObj); // 后台
			} 
			
			barragePanel.innerHTML += '<div><span class="barrage-time">' + secondsFormat(Math.floor(videoSrc.currentTime)) + '</span><span class="barrage-content" title="' + message + '">' + adjustWidth(message, 230) + '</span><span class="barrage-date">' + dateFormat() + '</span></div>\n';
		},
		// 发送至后台页面
		sendBarrageToBackend = function (barrage) {
			var date = new Date(),
				videoStr = videoSrc.src.split("/").pop(),
				// 弹幕格式为time, type, size, user, color, timestamp, content
				barrageStr = videoSrc.currentTime + ',' + barrage.type + ',' + barrage.size + ',' + 'test' + ',' + barrage.color + ',' + dateFormat('year') + ',' + barrage.content,
				req;
			
			if (window.XMLHttpRequest) {
				req = new XMLHttpRequest();
			} else {
				req = new ActiveXObject("Microsoft.XMLHTTP");
			}
			 
			req.open("POST", `backend/backend.php?barrage=${videoStr}`, true);
			req.send(barrageStr);
			console.log(barrageStr);
		},
		// 显示当前弹幕颜色
		colorPick = function () {
			var content = document.getElementById('color-picker'),
				colorValue = document.getElementById('barrage-color').value;
			// 检查是否以#起始，长度为4或者7
			if (!(colorValue[0] == '#' && (colorValue.length == 4 || colorValue == 7))) {
				document.getElementById('barrage-color').value = '#FFF';
			}
			if (isNaN(parseInt(colorValue.substring(1), 16))) {
				document.getElementById('barrage-color').value = '#FFF';
			}
			
			content.style.backgroundColor = colorValue;
		},
		that = {
			// 初始化
			init: function () {
				timeLine.fillStyle = '#999';
				timeLine.font = '16px 微软雅黑';
				drawVolume(0);
				volumeCanvas.fillRect(0, 0, document.getElementById('volume').width, document.getElementById('volume').height - 0);
				// 对弹幕排序
				var sortedBarrageList = sortBarrages(danmuku);
				fillBarragePanel(sortedBarrageList);
				colorPick();
			},
			// 控制音量和弹幕选项面板 
			panel: function (a, b) {
				if (a == 'volume') {
					if (b == 'display') {
						document.getElementById('volumeback').style.display = 'block';
					} else if (b == 'hide') {
						document.getElementById('volumeback').style.display = 'none';
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
				sendBarrage();
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
					drawVolume(event.offsetY);
					console.log('click:', 'volume change to', videoSrc.volume);
				// 时间轴
				} else if (item == 'timeline') {
					videoSrc.currentTime = videoSrc.duration * (event.offsetX - 1) / (document.getElementById('timeline').width - 2);
					drawTimeLine();
					// for (var i = 0; i < videoSrc.buffered.length; ++i) {
						// console.log('video buffer', i,'from', videoSrc.buffered.start(i), 'to', videoSrc.buffered.end(i));
					// }
					nextBarrageIndex = getCurrentBarrageIndex(videoSrc.currentTime);
				}
			},
			// 按键
			pressKey: function (key, event) {
				//console.log('en');
				if (key == 'send' && event.keyCode == 13) {
					sendBarrage();
				}
			},
			// 播放完成
			end: function () {
				var timeLineWidth = document.getElementById('timeline').width,
					timeLineHeight = document.getElementById('timeline').height;
				window.cancelAnimationFrame(videoId);
				timeLine.clearRect(0, 0, timeLineWidth, timeLineHeight);
				playbutton.innerText = 'play';
				videoPlaying = false;
				// 这个设置需要还原
				console.log('end: nextBarrageIndex has set to 0');
				nextBarrageIndex = 0;
			},
			// 更换弹幕颜色
			colorPicker: function () {
				colorPick();
			}
			
		}
		return that;
	},
	p = player();
	
window.onload = p.init();	