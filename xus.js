var m = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|Blackberry)/), pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1, staggered = false;
$(document).ready(function(){
	if(m) {
		$('a:not([target])').on('click',function(){
			self.location = $(this).attr('href');
			return false;
		});
		// redesign with menu buttons
		$('.nav').hide();
		$('.header').append('<a class="btn-nav" id="mnav-nav">▼</a>');
		$('#mnav-nav').click(function(){
			$('.nav').toggle();
		});
		if(document.referrer) {
			$('.header').prepend('<a class="btn-nav" id="mnav-back">◄</a>');
			$('#mnav-back').click(function(){
				window.history.back();
			});
			$('.header .logo').css('margin-left',($(document).width() - 150) / 2 + 'px');
		}
		if($('#frmpost').length) {
			$('#frmpost').hide();
			$('.header').append('<a class="btn-nav btn-nav-2" id="mnav-post">+</a>');
			$('#mnav-post').click(function(){
				$('#frmpost').toggle();
			});
		}
	} else { // desktop only
		$.post('/ajax.php',{req:'cmsg'});
		
		// Multi-column and application layout sizing
		function resize() {
			$('#appframe').height(
				$(document).height() - $('header').outerHeight() - $('footer').outerHeight() - 20
			);
		}
		
		// Initialize layout
		resize();
		
		// Set onResize handler for layout, also for textarea resizing
		$(window).resize(function(){resize()});
		//$('#frmpost textarea').resize(function(){resize()});
		
		$('a[rel=share]').click(function(){
			var w = 660, h = 250
			var l = (screen.width/2)-(w/2);
			var t = (screen.height/2)-(h/2);
			window.open($(this).attr('href'), 'Share on your page', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+t+', left='+l);
			return false;
		});
		
		// Hovercards
		if(u_flags.hovercards) {
			var hcTimeout;
			$('<div />',{id:'hovercard'}).hide().appendTo('body').mouseenter(function(){
				if(hcTimeout) window.clearTimeout(hcTimeout);
				$('#hovercard').fadeOut(200);
			});
			$('.content').on('mouseenter','a[rel="author"][data-uid],a[rel="user"][data-uid]',function(){
				$('#hovercard').css({
					left: $(this).offset().left - 10,
					top:  $(this).offset().top + $(this).height()
				}).html('').addClass('loading');
				$.post('/ajax.php',{
					req: 'hovercard',
					uid: $(this).attr('data-uid')
				},function(data){
					if(hcTimeout) window.clearTimeout(hcTimeout);
					hcTimeout = window.setTimeout(function(){
						$('#hovercard').html(data).removeClass('loading').fadeIn(150);
					},500);
				})
			}).on('mouseleave','a[rel="author"][data-uid],a[rel="user"][data-uid]',function(){
				if(hcTimeout) window.clearTimeout(hcTimeout);
				hcTimeout = window.setTimeout(function(){
					$('#hovercard').fadeOut(200);
				},200);
			});
		}
		
		// Arrange Posts
		if(staggered = staggerlayout()) {
			// Keep posts arranged when new content is introduced
			$(window).resize(function(){
				resize();
				staggered = staggerlayout();
			});
			// Correct post arrangement when external media has loaded
			$(window).load(function(){
				staggered = staggerlayout();
			});
		}
		
		// Initialize Chat
		$('<div />').attr('id','chat-contain').appendTo('body');
		$('<div />').attr('id','chatbox').html('<p class="ajax-loader">Loading chat&hellip;</p>').appendTo('#chat-contain');
		$.post('/ajax.php',{
			req: 'chatlist'
		},function(data){
			$('#chatbox').html(data);
		});
		
	}
	
	// Set up post focus styling
	$('.streampost input').focus(function(){
		$(this).parents('.streampost').addClass('current');
	}).blur(function(){
		$(this).parents('.streampost').removeClass('current');
	});
	
	if(pixelRatio>1) {
		$('.i2x').each(function(){
			// apparently I was doing something with @2x... probably appending @2x to the img[src]...
		});
	}
	
	// Tell the form that JavaScript is enabled
	$('#jsinput').val(1);
	
	// Set up alert Dismiss links
	$('a[rel=alert-dismiss]').click(function(){
		$('div[data-alert=' + $(this).attr('data-flag') + ']').fadeTo(250,.5);
		setflag($(this).attr('data-flag'),1,function(){
			$('div[data-alert=' + $(this).attr('data-flag') + ']').hide();
		});
	});
	
	// Capture comment submissions and display inline
	$('.comment form').submit(function(){
		var $form = $(this);
		$(this).addClass('ajax-loader');
		$.post('/ajax.php',{
			req: 'comment',
			txt: $(this).children('input[type=text]').val(),
			//postid: $(this).attr('action').substr($(this).attr('action').length - 13),
			postid: $(this).parents('.streampost').attr('data-postid'),
			html: 1
		},function(data){
			if(data!='0') $form.parent().before(data);
			$form.removeClass('ajax-loader');
		});
		$(this).children('input[type=text]').val('');
		return false;
	});
	
	// Delete Post/Comment Confirmation
	$('a[rel="delete"]').on('click',function(){
		return confirm('Are you sure you want to delete this post?');
		$(this).addClass('ajax-loader');
	});
	$('.comment-delete a').on('click',function(){
		if(confirm('Are you sure you want to delete this comment?')) {
			$(this).addClass('ajax-loader');
			var cid = $(this).parent().parent().attr('data-cid');
			$.post('/ajax.php',{
				cid: cid
			},function(data){
				$('.comment[data-cid='+cid+']').remove();
			});
		}
		return false;
	});
	
	$('.streampost').on('click','a[rel="liked-by"]',function(){
		userselect();
	});
	
	// Like A Handlers
	$('.streampost').on('click','a[rel="like"]',function(){like($(this));return false});
	$('.streampost').on('click','a[rel="unlike"]',function(){unlike($(this));return false});
	
	// Create hidden element to detect text input size
	$('<div />',{id:'txtaprvw'}).css('font',$('#frmpost').css('font')).css('line-height',$('#frmpost').css('line-height')).appendTo('body');
	
	// Initialize live comment checker
	newcomments();
	
	// Embed content from external links
	$('.streampost a[rel="nofollow"], .comment a[rel="nofollow"]').oembed(null,{
		embedMethod: 'append',
		maxWidth:  staggered ? 416 : 912,
		maxHeight: staggered ? 300 : 600,
		onProviderNotFound: function(url) {
			if(url!=null){
				var regExp = new RegExp("http.*\.[jpg|gif|png]","i");
				if(url.match(regExp)!=null)
					$(this).append('<img src="'+url+' class="picture embed" alt="">');
			}
		}
	});
});

// Like toggle and AJAX functions (separated from event to allow cross-access)
// Required: jQuery instance of A element triggering function
function like(jq){
	$.post('/ajax.php',{
		req: 'like',
		postid: jq.parents('.streampost').attr('data-postid')
	},function(data){
		jq.attr('rel','unlike').text('Unlike') .click(function(){unlike(jq)});
	});
	return false;
}
function unlike(jq) {
	$.post('/ajax.php',{
		req: 'unlike',
		postid: jq.attr('data-postid')
	},function(data){
		jq.attr('rel','like').text('Like').click(function(){like(jq)});
	});
	return false;
}

// Poop
function poop() {
	$('*').contents().filter(function() {
		return this.nodeType == Node.TEXT_NODE && this.nodeValue.trim() != '';
	}).each(function() {
		this.nodeValue = 'poop ';
	});
	$('input[value!=""]').val('poop');
	$('input,textarea').attr('placeholder','poop');
}

// Arrage posts in a staggered layout
function staggerlayout() {
	if($('#main').hasClass('stag-on') == true) {
		// Initialize Variables
		var lcol_height = 0,
		    rcol_height = 0;
		
		// Apply Layout
		$('.streampost,.pageitem').each(function(){
			if(lcol_height > rcol_height)
				rcol_height += $(this).addClass('right').outerHeight(true);
			else
				lcol_height += $(this).removeClass('right').outerHeight(true);
		});
		
		// Update Renderer
		$('#main').addClass('staggered');
		
		return true;
	} else {
		return false;
	}
}

// Decode HTML characters
function htmlDecode(s) {
	return $('<div />').html(s).text();
}


// Checks for new comments on all currently displayed posts periodically
function newcomments() {
	window.setTimeout(function(){
		$('.streampost').each(function(index){
			var cid = $(this).find('.comment:last-child').prev().attr('data-cid');
			var pid = $(this).attr('data-postid');
			$.post('/ajax.php',{
				req: 'newcomments',
				cid: (cid != undefined) ? cid : null,
				pid: pid
			},function(data){
				if(data.length > 0)
					$('.streampost[data-postid='+pid+'] .comment:last-child').before(data);
			});
		});
		newcomments();
	},1000*8);
}

// Construct a user picker UI, and return the user selection
function userpicker(users,type,callback) {
	// Generate .overlay
	$('<div class="overlay" id="userpicker" />').hide().appendTo('body');
	if(m) $('#userpicker').show(); else $('#userpicker').fadeIn(250);
	
	// Append container
	$('<div class="cont" />').appendTo('#userpicker').html('<p style="margin:1em">Loading users&hellip;</p>');
	
	// Load user list
	$.post('/ajax.php',{
		req:   'userlist',
		users: users,
		type:  type
	},function(data){
		$('#userpicker .cont').html('');
		// Add buttons to .overlay and set handlers
		if(type>0) $('<input />',{
			type:  'button',
			value: 'Select Users',
			id:    'usrpcksave',
			disabled: 'disabled'
		}).appendTo('#userpicker .cont').click(function(){
			var data = '';
			$('#userpicker .ulist input:checked').each(function(){
				data += $(this).val() + ', ';
			});
			callback(data.slice(0,-2));
			if(m) $('#userpicker').remove(); else{
				$('#userpicker .cont .ulist').slideUp(250);
				$('#userpicker').fadeOut(250,function(){
					$(this).remove();
				});
			}
		});
		$('<input />',{
			type:  'button',
			value: (type>0) ? 'Cancel' : 'Close'
		}).appendTo('#userpicker .cont').click(function(){
			if(m) $('#userpicker').remove(); else{
				$('#userpicker .cont .ulist').slideUp(250);
				$('#userpicker').fadeOut(250,function(){
					$(this).remove();
				});
			}
		});
		
		// Add user list
		$('#userpicker .cont').prepend(data);
		if(!m) $('#userpicker .cont .ulist').hide().slideDown(400);
		
		// Set up selection monitoring
		$('#userpicker .cont .ulist input').on('change','',function(){
			$('#userpicker .ulist label').removeClass('selected');
			$('#userpicker .ulist input:checked').parent().addClass('selected');
			if($('#userpicker .ulist input:checked').length>0)
				$('#usrpcksave').prop('disabled',false);
			else
				$('#usrpcksave').prop('disabled',true);
		});
	});
}

// Set user flag, optionally run callback(data) on completion
function setflag(flag,value,callback) {
	$.post('/ajax.php',{
		req: 'setflag',
		flag: flag,
		val: value
	},function(data){
		if(callback)
			callback(data);
	});
}

// Handle Post TextArea keystrokes
function tefs(e) {
	// Apply sampled text size to TextArea
	$('#txtaprvw').text($('#frmpost textarea').val()).css('width',$('#frmpost').css('width'));
	$('#frmpost textarea').height($('#txtaprvw').height()+14);
	
	// If mobile, return default
	if(m) return true;
	
	// Submits form when Enter is pressed, unless Shift or Ctrl were also pressed
	var charCode=(e.which)?e.which:window.event.keyCode;
	if(charCode==13 && e.shiftKey) {
		document.getElementById('frmpost').submit();
		return false;
	}
}