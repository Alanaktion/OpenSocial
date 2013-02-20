var initialURL = location.href,
hasPushState   = !!(window.history && history.pushState),
hasNavigated   = false;

// Navigate Via AJAX, utilizing history.pushState
function go(url) {
	console.log('loading '+url+' [rel: ajax]...');
	$('.content').fadeTo(m ? 0 : 200,.5);
	$('#main').addClass('loading');
	$.get(url+'?rel=ajax',function(data){
		document.title = htmlDecode(data.substring(data.indexOf('<title>')+7,data.indexOf('</title>')));
		$('#main').html(data.substring(data.indexOf('</title>')+8)).removeClass('loading');
		addHasEvent();
	});
	if(url!=window.location)
		window.history.pushState({path:url},'',url);
}

function addHasEvent() {
	$('a[rel="like"],a[rel="unlike"],a[rel="liked-by"],.comment-delete a').addClass('hasEvent');
}

$(document).ready(function(){
	if(hasPushState) {
		// Add .hasEvent to all hyperlink elements with JS events
		addHasEvent();
		
		// Update hyperlinks to use AJAX
		$('body').on('click','a[href]:not([target]):not(.hasEvent)',function(e){
			hasNavigated = true;
			
			go($(this).attr('href'));		
			return false;
		});
		
		// Capture back/forward navigation from pushState
		$(window).bind('popstate',function(e) {
			if(hasNavigated) go(window.location);
		});
	}
});