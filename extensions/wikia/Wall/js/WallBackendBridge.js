var WallBackendBridge = $.createClass(Observable, {
	constructor: function() {
		WallNewMessageForm.superclass.constructor.apply(this,arguments);
	},
	
	loadPage: function(username, page, callback) {
		$.nirvana.sendRequest({
			controller: 'WallExternalController',
			method: 'getCommentsPage',
			format: 'json',
			data: {
				page: page,
				username: username
			},
			callback: this.proxy(function(data) {
				var html = innerShiv(data.html);
				var page = $('.comments', html);
				var pagination = $('.Pagination', html);
				if(typeof callback == 'function') {
					callback(page, pagination);
				}
				this.fire('pageLoaded', page, pagination);
			})
		});
	},
	
	postNew: function(username, title, body, convertFormat, callback) {
		$.nirvana.sendRequest({
			controller: 'WallExternalController',
			method: 'postNewMessage',
			data: {
				body: body,
				messagetitle: title,
				username: username,
				convertFormat: convertFormat
			},
			callback: this.proxy(function(data) {
				var newmsg = $(innerShiv(data.message));
				if(typeof callback == 'function') {
					callback(newmsg);
				}
				this.fire('newPosted', newmsg);
			})
		});
	},
	
	postReply: function(username, body, convertFormat, parent, callback) {
		$.nirvana.sendRequest({
			controller: 'WallExternalController',
			method: 'replyToMessage',
			data: {
				body: body,
				parent: parent,
				username: username,
				convertFormat: convertFormat
			}, callback: this.proxy(function(data) {
				var newmsg = $(innerShiv(data.message));
				
				if(typeof callback == 'function') {
					callback(newmsg);
				}
				
				this.fire('postReply', newmsg);
			})
		});
	}, 
	
	cancelEdit: function(username, id, callback) {
		if(typeof callback == 'function') {
			callback(newmsg);
		}
		
		this.fire('editCanceled', newmsg);
	},
	
	loadEditData: function(username, id, mode, convertFormat, callback) {
		var data = {
				'msgid': id,
				'username': this.username,
				'convertFormat': convertFormat
		};
		
		this.fire('beforeEditDataLoad', id);
				
		$.nirvana.sendRequest({
			controller: 'WallExternalController',
			method: 'editMessage',
			format: 'json',
			data: data,
			callback: this.proxy(function(data) {
				// backend error lets reload the page
				if(data.status == false && data.forcereload == true) {
					var url = window.location.href;
					if (url.indexOf('#') >= 0) {
						url = url.substring( 0, url.indexOf('#') );
					}
					window.location.href = url + '?reload=' + Math.floor(Math.random()*999);
				}
				
				data.mode = mode;
				data.id = id;

				if(typeof callback == 'function') {
					callback(data);
				}
				this.fire('editDataLoaded', data);
			})
		});
	},
	
	saveEdit: function(username, id, title, body, isreply, convertFormat, callback) {
		var data = {
			'msgid': id,
			'newtitle': title,
			'newbody': body,
			'isreply': isreply,
			'username': username,
			'convertFormat': convertFormat
		};

		$.nirvana.sendRequest({
			controller: 'WallExternalController',
			method: 'editMessageSave',
			format: 'json',
			data: data,
			callback: this.proxy(function(data) {
				data.body = innerShiv(data.body);
				if(typeof callback == 'function') {
					callback(data);
				}
				this.fire('editSaved', data);
			})
		});
	}
});