var exports = exports || {};
//TODO: Create animation managment system

define.call(exports, function(){
	var Points = my.Class( {

		_points: 0,
		
		constructor: function(points) {
			this._points = points || 0;
		},
		
		getPoints: function() {
			return this._points;
		},
		
		setPoints: function(points) {
			this._points = Math.round(points);
		},
		
		deductPoints: function(points) {
			this._points = Math.max(0, (this._points - Math.round(points)));
		},
		
		addPoints: function(points) {
			this._points += Math.round(points);
		}
	});
	
	var Game = my.Class({
		
		STATIC: {
			ROUND_LENGTH: 10,
			INCORRECT_CLASS_NAME: 'incorrect',
			TIME_UP_NOTIFICATION_DURATION_MILLIS: 3000,
			MAX_SECONDS_PER_ROUND: 15,
			UPDATE_INTERVAL_MILLIS: 500,
			PERCENT_FOR_TIME_IS_LOW: 25,
			MAX_POINTS_PER_ROUND: 1000,
			PERCENT_DEDUCTION_WRONG_GUESS: 30
		},

		_numCorrect: 0,
		_roundIsOver: false,
		_timeIsLow: false,
		_pause: true,
		_tutorialSteps: {},
		_timer: null,
		_correctAnswer: '',
		_currentRound: 0,
		_totalPoints: new Points(0),
		
		constructor: function(options){
			options = options || {};
			
			Observe(this);
			this._id = options.id;
			this._data = options.data || [];
			this._numRounds = options.data.length;
			this._roundPoints = new Points(Game.MAX_POINTS_PER_ROUND);
			this._timerPointDeduction = Math.round((Game.MAX_POINTS_PER_ROUND / ((Game.MAX_SECONDS_PER_ROUND*1000) / Game.UPDATE_INTERVAL_MILLIS)));
			this._wrongAnswerPointDeduction = Math.round((Game.MAX_POINTS_PER_ROUND * (Game.PERCENT_DEDUCTION_WRONG_GUESS / 100)));
		},
		
		getId: function(){
			return this._id;
		},
		
		getPercent: function() {
			return (this._roundPoints.getPoints() * 100)/ Game.MAX_POINTS_PER_ROUND;
		},
		
		prepareGame: function(){
			console.log('Starting game: ' + this.getId());
			this.fire('initGameScreen', this.getId());
			this.prepareAnswerDrawer();
			this.prepareContinueView();
			this.prepareFinishScreen();
			this.prepareHud();
			this.prepareTiles();
			this.nextRound();
		},
		
		nextRound: function(){
			if(this._currentRound < this._data.length){
				this._currentRound++
				this.play();
			}else{
				this.fire('endGame');
			}
		},
		
		play: function() {
			this._roundPoints.setPoints(Game.MAX_POINTS_PER_ROUND);
			this._timeIsLow = false;
			this._pause = true;
			this._timer = null;
			this._roundIsOver = false;
			
			this.fire('roundStart', {gameId: this.getId(), image: this._data[this._currentRound-1].image, totalPoints: this._totalPoints.getPoints()});
			//this.hideContinue();
			//this.showScoreBar();
			//this.hideAnswerDrawer();//puts the drawer back in place
			//this.showAnswerDrawer();
			//this.hideEndGameScreen();//needs to hide previously shown final screen
			this.prepareAnswers();
			//this.updateHudScore();
			//this.updateScoreBar();//resets the scorebar
		},
		
		prepareTiles: function() {
			var tds = document.getElementsByTagName('td'),
			tdsLength = tds.length,
			self = this;
			
			for(var i = 0; i < tdsLength; i++) {
				tds[i].onclick = function() {
					console.log('clicked;');
					self.resume();
					self.fire('tileClicked', this)
				}
			}
		},
		
		prepareHud: function() {
			var self = this;
			document.getElementById('totalPoints').innerHTML = '0';
			document.getElementById('home').onclick = function() {
				self.fire('goHome');
			};
			document.getElementById('pauseButton').onclick = function() {
				var pause = self._pause;
				self.fire('pauseButtonClicked', pause);
				if(pause) {
					self.resume();
				} else {
					self.pause();
				}
			};
			document.getElementById('muteButton').onclick = function() {
				self.fire('muteButtonClicked');
			};
			document.getElementById('home').onclick = function() {
				self.pause();
				self.fire('homeClicked');
			};
		},
		
		prepareContinueView: function() {
			var self = this;
			document.getElementById('continue').onclick = function() {
				self.fire('continueClicked');	
			}
		},
		
		prepareAnswerDrawer: function() {
			var self = this,
			answerList = document.getElementById('answerList').getElementsByTagName('li');
			document.getElementById('answerButton').onclick = function() {
				self.fire('answerDrawerButtonClicked');
			};
			
			for(var i = 0; i < 4; i++) {
				answerList[i].onclick = function() {
					if(this.id != self._correctAnswer && !this.clicked) {
						self._roundPoints.deductPoints(self._wrongAnswerPointDeduction);
						self.resume();
						self.fire('wrongAnswerClicked', {li: this, percent: self.getPercent()});
					} else {
						self._roundIsOver = true;
						self._timer = null;
						// Record this as a correct answer (for the stats at the end of the game).
						self._numCorrect++;
						// Move the points from the round-score to the total score.
						self._totalPoints.addPoints(self._roundPoints.getPoints());
						self._roundPoints.setPoints(0);
						self.fire('rightAnswerClicked', this.innerHTML);
					}
				}
			};
		},
		
		prepareAnswers: function() {
			var round = this._currentRound-1,
			answers = this._data[round].answers,
			correct = this._data[round].correct;
			
			answers.sort(function() {return Math.round(Math.random())-0.5});
			this.fire('answersPrepared', {answers:answers, "class":Game.INCORRECT_CLASS_NAME});
			this._correctAnswer = 'answer' + answers.indexOf(correct);
		},
		
		prepareFinishScreen: function() {
			var self = this;
			document.getElementById('goHome').onclick = function() {
				self.fire('goHome');
			};
			
			document.getElementById('playAgain').onclick = function() {
				self.fire('playAgain');
			};
			
			document.getElementById('goToHighScores').onclick = function() {
				self.fire('goToHighScores');
			};
			
			document.getElementById('playAgain').style.visibility = (this._id == 'tutorial') ? 'hidden' : 'visible';
		},
		
		pause: function() {
			console.log('pause');
			this._pause = true;
			this._timer = null;
		},
		
		resume: function() {
			console.log('resume');
			if(this._pause) {
				this._pause = false;
				this.timer();
			}
		},
		
		timer: function() {
			if(!this._timer) {
				var self = this;
				(function time() {
					if( !self._pause ) {
						if((self._roundPoints.getPoints() <= 0) && (!self._roundIsOver)){
							self.fire('timeIsUp');
						} else if(!self._roundIsOver){
							self._roundPoints.deductPoints(self._timerPointDeduction);
							self.fire('timerEvent', self.getPercent());
							if(!self._timeIsLow){
								if(((self._roundPoints.getPoints() * 100) / Game.MAX_POINTS_PER_ROUND) < Game.PERCENT_FOR_TIME_IS_LOW){
									self.fire('timeIsLow');
									self._timeIsLow = true;
								}
							}
							self._timer = setTimeout(time, Game.UPDATE_INTERVAL_MILLIS);
						}
					}
				})();
			}
		}
	});
	
	return {
		Game: Game
	};
});