/** TwentyTwentyProton jQuery Module */
(function($){
	$.fn.twentytwentyproton = function(options) {
		var options = $.extend({'proton':true,'fullscreen':true,'description':null,'quickslide':true}, options);
		return this.each(function() {
			var container = $(this).twentytwenty(options);
			container.addClass('twentytwenty-proton');
			var overlay = container.find(".twentytwenty-overlay");
			var slider = container.find(".twentytwenty-handle");

			if (options.proton)
			{
				var canvas;
				var image = new Image();
				var proton;
				var renderer;
				var emitter;

				var tick = function(){
					requestAnimationFrame(tick);
					proton.update();
				}
				image.onload = function()
				{
					overlay.append(
						canvas = $('<canvas>').attr({
							'width': overlay.width(),
							'height': overlay.height()
						})
					);
					proton = new Proton;
					renderer = new Proton.Renderer('webgl', proton, canvas[0]);
					renderer.blendFunc("SRC_ALPHA", "ONE");
					renderer.start();
					emitter = new Proton.Emitter();
					emitter.rate = new Proton.Rate(new Proton.Span(5, 10), new Proton.Span(.01, .015));
					emitter.addInitialize(new Proton.Life(.1, 0.5));
					emitter.addInitialize(new Proton.ImageTarget(['/wp-content/plugins/twentytwenty-proton/twentytwenty-proton/img/particle.png'], 32));
					emitter.addBehaviour(new Proton.Alpha(1, 0));
					emitter.addBehaviour(new Proton.Color('#E374F7', '#E374F7'));
					emitter.p.x = canvas.width() / 2;
					emitter.p.y = canvas.height() / 2;
					proton.addEmitter(emitter);
					
					emitter.p.x = slider.offset().left;
					emitter.emit('once');

					slider.on("move", function(e) {
						emitter.p.x = slider.offset().left-container.offset().left;
						emitter.emit('once');
					});
					tick();
				}
				image.src = '/wp-content/plugins/twentytwenty-proton/twentytwenty-proton/img/particle.png';
			}

			if (options.fullscreen)
			{
				var fullscreen;
				container.append(
					fullscreen = $('<div>').addClass('fullscreen').append(
						$("<span>").addClass('glyphicon glyphicon-fullscreen')
					)
				);
				fullscreen.click(function(event){
					if (container.hasClass('isFullscreen'))
					{
						if (document.exitFullscreen)
						{
							document.exitFullscreen();
						}
						else if (document.msExitFullscreen)
						{
							document.msExitFullscreen();
						}
						else if (document.mozCancelFullScreen)
						{
							document.mozCancelFullScreen();
						}
						else if (document.webkitExitFullscreen)
						{
							document.webkitExitFullscreen();
						}
					}
					else
					{
						if (container[0].requestFullscreen)
						{
							container[0].requestFullscreen();
						}
						else if (container[0].msRequestFullscreen)
						{
							container[0].msRequestFullscreen();
						}
						else if (container[0].mozRequestFullScreen)
						{
							container[0].mozRequestFullScreen();
						}
						else if (container[0].webkitRequestFullscreen)
						{
							container[0].webkitRequestFullscreen();
						}
					}
					container.toggleClass('isFullscreen');
				});
			}

			if (options.description)
			{
				var description;
				container.append(
					description = $("<div>").addClass("twentytwenty-description").text(options.description)
				);
			}

			if (options.quickslide)
			{
				var sliderMoved;
				var sliderMoved_start_event;
				container.addClass('twentytwenty-quickslide');
				slider.append($("<div>").addClass('quickslide-overlay'));
				var getEventProp = function(event,prop)
				{
					return((event.originalEvent.touches ? event.originalEvent.touches[0][prop] : event.originalEvent[prop]));
				}
				var animateTo = function(to,speed)
				{
					var event = jQuery.Event("movestart",{_handled:function(){}});
					slider.trigger(event);
					var from = getEventProp(sliderMoved_start_event,'pageX');
					var event = jQuery.Event("move",{pageX:from,touches:[]});
					var tick = function()
					{
						if (to > from && event.pageX < to)
						{
							requestAnimationFrame(tick);
							event.pageX+=speed;
							slider.trigger(event);
						}
						else if (to < from && event.pageX > to)
						{
							requestAnimationFrame(tick);
							event.pageX-=speed;
							slider.trigger(event);
						}
						else
						{
							window.setTimeout(function(){container.removeClass('active');},0);
						}
					}
					container.addClass('active');
					tick();
				}
				slider.addClass('quickslide');
				slider.on("touchstart mousedown", function(e) {
					sliderMoved = false;
					sliderMoved_start_event = e;
				});
				slider.on("touchend mouseup", function(e) {
					if (!sliderMoved)
					{
						var left = (getEventProp(sliderMoved_start_event,'screenX') - slider.offset().left);
						if (left > (slider.width()/2))
						{
							animateTo(container.offset().left+container.width(),25);
						}
						else
						{
							animateTo(0,25);
						}
					}
					sliderMoved_start_event = null;
				});
				slider.on("touchmove mousemove", function(e) {
					var event_screenX = getEventProp(e,'screenX');
					if (sliderMoved_start_event && e.originalEvent)
					{
						var diff = 
							getEventProp(sliderMoved_start_event,'screenX') -
							event_screenX;
						if (diff < -3 || diff > 3)
						{
							sliderMoved=true;
						}
					}
					var percentage = (100/slider.width())*(event_screenX - slider.offset().left);
					if (percentage < 50)
					{
						slider.removeClass('quickslide-right');
						slider.addClass('quickslide-left');
					}
					else
					{
						slider.removeClass('quickslide-left');
						slider.addClass('quickslide-right');	
					}
				});
				slider.on('mouseleave', function(e){
					slider.removeClass('quickslide-left');
					slider.removeClass('quickslide-right');
				});
			}
		});
	};
})(jQuery);
