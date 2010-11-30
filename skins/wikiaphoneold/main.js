_uacct = "UA-2871474-1";
username = wgUserName == null ? 'anon' : 'user';
urchinTracker('/1_wikiaphone/' + username + '/view');
if(wgPrivateTracker) {
	urchinTracker('/1_wikiaphone/' + wgDB + '/' + username + '/view');
}

document.onclick = function(event){
	//IE doesn't pass in the event object
	event = event || window.event;

	//IE uses srcElement as the target
	var target = event.target || event.srcElement;
	var baseEvent = '1_wikiaphone/anon/click/';
	var eventToTrack = baseEvent;
	
	switch(target.id){
		case 'searchGoButton':
		case 'mw-searchButton':
			eventToTrack += 'search'
			urchinTracker(eventToTrack);
			break;
		default:
			if(target.nodeName === 'A'){
				if(target.href.indexOf(CategoryNamespaceMessage) !== -1){
					eventToTrack += 'categorylink';
					urchinTracker(eventToTrack);
				}else if(target.href.indexOf(SpecialNamespaceMessage) === -1){
					eventToTrack += 'contentlink';
					urchinTracker(eventToTrack);
				}
			}
			
			if(target.parentNode){
				switch(target.parentNode.id){
					case 'ca-edit':
						eventToTrack += 'edit';
						urchinTracker(eventToTrack);
						break;
					case 'n-randompage':
						eventToTrack += 'randompage';
						urchinTracker(eventToTrack);
						break;
				}
			}
	}
	
	if(typeof console !== 'undefined' && typeof console.log !== 'undefined' && eventToTrack !== baseEvent) console.log('urchinTracker', eventToTrack);
	
};