var conducteur = angular.module('conducteur', ['ui.bootstrap']);

var QueryString = function () {
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = decodeURIComponent(pair[1]);
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
      query_string[pair[0]] = arr;
    } else {
      query_string[pair[0]].push(decodeURIComponent(pair[1]));
    }
  } 
    return query_string;
}();

var directory = conducteur.controller('directory', function($scope, $http) {
	$scope.directoryExpanded = false;

	$scope.filters = [];

	$scope.directoryToggle = function() {
		$scope.directoryExpanded = !$scope.directoryExpanded;
	};

	$scope.formatTime = function(time) {
		var min = Math.floor(time / 60);
		var sec = time - min * 60;
		return min + ':' + (sec < 10 ? '0' : '') + Math.floor(sec); //round(sec, 2);
	};

	$scope.cleanSelection = function() {
		$scope.directoryData.forEach(function(item) {
			item.checked = false;
		});
	};

	$scope.prepareDownload = function() {
		var ids = $scope.directoryData.filter(function(item) {
			return item.checked;
		}).map(function(item) {
			return item.ID;
		}).join(',');
		if (ids.length > 0) {
			window.open('api/downloadSongs.php?ids=' + ids);
		}
	};

	$http({
		url: 'api/getDirectory.php'
	}).then(function success(data) {
		var data = data.data;
		$scope.directoryData = data;
	});
});

var templateEditor = conducteur.controller('templateEditor', function($scope, $http) {
	$scope.loaded = false;

	$scope.start = new Date(1970, 0, 1, 0, 0, 0);

	$scope.lines = [];

	$scope.lineTypes = {
		'fixed': {
			label: window.TR('Fixed')
		},
		'elastic': {
			label: window.TR('Elastic')
		},
		'driven': {
			label: window.TR('From resource')
		}
	};

	$scope.lineRessources = {
		'nope': {
			label: window.TR('No resource')
		},
		'fixed': {
			label: window.TR('Fixed resource')
		},
		'query': {
			label: window.TR('Query')
		}
	};

	$scope.init = function(callback) {
		var id = $scope.id;
		if (!!id) {
			$http({
				url: 'api/template_getInfos.php',
				params: {
					id: id
				}
			}).then(function success(data) {
				var data = data.data;
				$scope.title = data.title;
				$scope.description = data.description;
				$scope.start = new Date(data.start);
				$scope.start.setMilliseconds(0);
				$scope.lines = data.lines.map(function(item) {
					item.duration = new Date(item.duration);
					item.duration.setMilliseconds(0);
					return item;
				});
				callback();
			});
		} else {
			callback();
		}
	};

	$scope.startAt = function(index) {
		var value = Math.floor($scope.start.getTime() / 1000);
		for (var i = 0; i < index; ++i) {
			value+= Math.floor($scope.lines[i].duration.getTime() / 1000);
		}
		return value * 1000;
	};

	$scope.autoCompleteProvide = function(url, params) {
		return $http.get('api/' + url, {
    		params: params
    	}).then(function(response){
    		return response.data;
    	});
	};

	$scope.getSongs = function(val) {
		return $scope.autoCompleteProvide('getSongs.php', {value: val});
	};

	$scope.newLine = function() {
		$scope.lines.push({
			itemType: 'fixed',
			itemRessource: 'nope',
			duration: new Date(0),
			params: {}
		});
	};

	$scope.moveTop = function($index) {
		if ($index <= 0) return;
		$scope.lines[$index] = $scope.lines.splice($index - 1, 1, $scope.lines[$index])[0];
	};

	$scope.moveBottom = function($index) {
		if ($index >= $scope.lines.length - 1) return;
		$scope.lines[$index] = $scope.lines.splice($index + 1, 1, $scope.lines[$index])[0];
	};

	$scope.removeLine = function($index) {
		if ($index < 0 || $index >= $scope.lines.length) return;
		$scope.lines.splice($index, 1);	
	};

	$scope.setDuration = function($index, $item) {
		var duration = new Date($item.duration * 1000);
		duration.setMilliseconds(0);
		$scope.lines[$index].duration = duration;
	};

	$scope.getSerial = function() {
		return JSON.stringify({
			'title': $scope.title,
			'description': $scope.description,
			'start': $scope.start,
			'lines': $scope.lines
		});
	};

	$scope.$watch('lines', function(newLines, oldLines) {
		newLines.forEach(function(value, key) {
			if (!value) return;
			if (value.itemType == 'elastic') {
				var endsAt = new Date(value.params.endsAt).getTime();
				$scope.lines[key].duration = new Date(endsAt - $scope.startAt(key));
				$scope.lines[key].duration.setMilliseconds(0);
			}
		});
	}, true);

	$scope.id = QueryString.tid || '';
	$scope.init(function() {
		$scope.loaded = true;
		var loadingEls = document.querySelectorAll('.loading');
		for (var i = 0; i < loadingEls.length; ++i) {
			loadingEls[i].classList.remove('loading');
		}
	});
});

var guideEditor = conducteur.controller('guideEditor', function($scope, $http) {
	$scope.datePopupPicker = {
		opened: false
	};

	$scope.title = '';
	$scope.template = {};
	$scope.lines = [];
	$scope.date = '';

	$scope.getTimestamp = function(date, isDuration) {
		var dateInstance = (date instanceof Date 
			? date 
			: isNaN(+date)
				? new Date(date)
				: new Date(date * 1000)
		);
		dateInstance.setMilliseconds(0);
		if (!!isDuration) {
			dateInstance.setDate(1);
			dateInstance.setMonth(0);
			dateInstance.setFullYear(1970);
		}
		var r = Math.floor(dateInstance.getTime() / 1000);
		return r;
	};

	$scope.getDate = function(timestamp, isDuration) {
		var dateInstance = new Date(timestamp * 1000);
		dateInstance.setMilliseconds(0);
		if (!!isDuration) {
			dateInstance.setDate(1);
			dateInstance.setMonth(0);
			dateInstance.setFullYear(1970);
		}
		return dateInstance;
	};

	$scope.getSerial = function() {
		var lines = JSON.parse(JSON.stringify($scope.lines));
		$out = JSON.stringify({
			'title': $scope.title,
			'details': $scope.details,
			'template': $scope.template,
			'lines': lines.map(function(item) {
				item.duration = $scope.getTimestamp(item.duration, true);
				return item;
			}),
			'date': $scope.getTimestamp($scope.date)
		});
		return $out;
	};

	$scope.openDatePopupPicker = function() {
		$scope.datePopupPicker.opened = true;
	};

	$scope.autoCompleteProvide = function(url, params) {
		return $http.get('api/' + url, {
    		params: params
    	}).then(function(response){
    		return response.data;
    	});
	};

	$scope.getItems = function(val) {
		var propositions = $scope.autoCompleteProvide('getItems.php', {value: val});
		return propositions;
	};

	$scope.querySong = function($index) {
		$http({
			url: 'api/guide_pickSong.php',
			params: {
				actualTemplate: {actualTemplate: $scope.lines.map(function(item, i) {
					return {
						'start': $scope.startAt(i).getTime(),
						'ID': item.ID
					};
				}).filter(function(item) {
					return !!item.ID;
				})},
				newQueryId: $scope.lines[$index].newQueryId,
				startAt: $scope.startAt($index).getTime()
			}
		}).then(function success(data) {
			$scope.setInfos($index, data.data);
		});
	};

	$scope.unlock = function($index) {
		$scope.lines[$index].locked = false;
	};

	$scope.setInfos = function($index, $item) {
		$scope.lines[$index].locked = true;
		$scope.lines[$index].showQueryForm = false;
		$scope.lines[$index].ID = $item.ID;
		$scope.lines[$index].additionnals = $item;
		$scope.lines[$index].duration = $scope.getDate($item.duration, true);
	};

	$scope.init = function(callback) {
		var id = $scope.id;
		if (!!id) {
			$http({
				url: 'api/guide_getInfos.php',
				params: {
					id: id
				}
			}).then(function success(data) {
				var data = data.data;
				$scope.title = data.title;
				$scope.details = data.details;
				$scope.date = $scope.getDate(data.date);
				$scope.template = data.template;
				$scope.lines = data.lines.map(function(item) {
					item.duration = $scope.getDate(item.duration, true);
					return item;
				});
				callback();
			});
		} else {
			callback();
		}
	};

	$scope.newLine = function() {
		$scope.lines.push({});
	};

	$scope.moveTop = function($index) {
		if ($index <= 0) return;
		$scope.lines[$index] = $scope.lines.splice($index - 1, 1, $scope.lines[$index])[0];
	};

	$scope.moveBottom = function($index) {
		if ($index >= $scope.lines.length - 1) return;
		$scope.lines[$index] = $scope.lines.splice($index + 1, 1, $scope.lines[$index])[0];
	};

	$scope.removeLine = function($index) {
		if ($index < 0 || $index >= $scope.lines.length) return;
		$scope.lines.splice($index, 1);	
	};

	$scope.startAt = function(index) {
		var value = 0;
		for (var i = 0; i < index; ++i) {
			var ts = $scope.getTimestamp($scope.lines[i].duration, true);
			value+= ts;
		}
		var start = $scope.getTimestamp($scope.template.start);
		start+= value;
		return $scope.getDate(start);
	};

	$scope.getIntro = function($index) {
		var line = $scope.lines[$index];
		if (!line.isSong) {
			return false;
		}
		var cues = line.additionnals.cue_times.split('&').map(function(item) {
			if (!item) return null;
			var kv = item.split('=');
			if (kv.length == 2) {
				var key = kv[0];
				var value = kv[1];
				return {
					key: key,
					value: value
				};
			} else return null;
		}).filter(function(item) {
			return !!item && item.key == 'int';
		});
		if (cues.length == 1) {
			return Math.round(cues[0].value);
		}
		return false;
	};

	$scope.id = QueryString.gid;

	$scope.init(function() {
		$scope.loaded = true;
		var loadingEls = document.querySelectorAll('.loading');
		for (var i = 0; i < loadingEls.length; ++i) {
			loadingEls[i].classList.remove('loading');
		}
	});
});

var audioEditor = conducteur.controller('audioEditor', function($scope, $http) {
	$scope.cue = {
		cues: {
			sta: '',
			end: '',
			int: '',
			out: '',
			lin: '',
			lou: '',
			hin: '',
			hou: '',
			fin: '',
			fou: '',
			xta: ''
		},
		previous: function(index) {
			if (isNaN(parseFloat(this.cues[index]))) {
				this.cues[index] = '0.0';
			}
			this.cues[index] = '' + Math.max((parseFloat(this.cues[index]) - 0.01), $scope.audio.buffered.start(0));
			this.play(index);
		},
		next: function(index) {
			if (isNaN(parseFloat(this.cues[index]))) {
				this.cues[index] = '0.0';
			}
			this.cues[index] = '' + Math.min((parseFloat(this.cues[index]) + 0.01), $scope.audio.buffered.end(0));
			this.play(index);
		},
		now: function(index) {
			this.cues[index] = '' + $scope.audio.currentTime;
		},
		play: function(index) {
			$scope.audio.currentTime = isNaN(parseFloat(this.cues[index])) ? 0.0 : parseFloat(this.cues[index]);
			$scope.audio.play();
		}
	};

	$scope.audio = new Audio;
	$scope.audio.controls = 'controls';
	$scope.audio.preload = 'auto';
	$scope.audio.classList.add('wrapper');
	var src = document.querySelector('.audio-wrapper').getAttribute('data-src');
	$http({
		url: src
	}).then(function success(data) {
		$scope.audio.src = 'api/' + data.data;
		document.querySelector('.audio-wrapper').appendChild($scope.audio);
	});

	$scope.getCueTimes = function() {
		var cue_times = '';
		Object.keys($scope.cue.cues).forEach(function(index) {
			if ($scope.cue.cues[index] !== '' && $scope.cue.cues[index] !== null && $scope.cue.cues[index] !== undefined) {
				cue_times+= '&' + index + '=' + $scope.cue.cues[index];
			}
		});
		return cue_times;
	};

	$scope.submit = function() {
		var cue_times = document.createElement('input');
		cue_times.setAttribute('type', 'hidden');
		cue_times.setAttribute('name', 'cue_times');
		cue_times.setAttribute('value', $scope.getCueTimes());
		document.querySelector('form').appendChild(cue_times);
		return true;
	};
});

var queryEditor = conducteur.controller('queryEditor', function($scope, $http) {
	$scope.modes = {
		'random': {
			label: window.TR('Random pick')
		},
		'priority': {
			label: window.TR('By priority')
		},
		/* 'lrp': {
			label: 'Moins récemment joué'
		},
		'mrp': {
			label: 'Plus récemment joué'
		}, */
		'lfp': {
			label: window.TR('Least frequently played')
		},
		'mfp': {
			label: window.TR('Most frequently played')
		}
	};

	$scope.init = function(callback) {
		$http({
			url: 'api/getQueries.php'
		}).then(function success(data) {
			$scope.queries = data.data;
			callback();
		});
	};

	$scope.createQuery = function() {
		$scope.queries.push({
			alias: '',
			category: null,
			subCategory: null,
			genre: null,
			mode: 'random'
		});
	};

	$scope.deleteQuery = function($index) {
		if (confirm(window.TR('Do you really want to delete this query?'))) {
			$scope.queries.splice($index, 1);
		}
	};

	$scope.autoCompleteProvide = function(url, params) {
		return $http.get('api/' + url, {
    		params: params
    	}).then(function(response){
    		return response.data;
    	});
	};

	$scope.getCategories = function(val) {
		return $scope.autoCompleteProvide('getCategories.php', {value: val});
	};

	$scope.getSubCategories = function(val, pid) {
		return $scope.autoCompleteProvide('getSubCategories.php', {value: val, pid: pid.ID});
	};

	$scope.getGenres = function(val) {
		return $scope.autoCompleteProvide('getGenres.php', {value: val});
	};

	$scope.getSerial = function() {
		return JSON.stringify($scope.queries);
	};

	$scope.init(function() {
		$scope.loaded = true;
		var loadingEls = document.querySelectorAll('.loading');
		for (var i = 0; i < loadingEls.length; ++i) {
			loadingEls[i].classList.remove('loading');
		}
	});
});