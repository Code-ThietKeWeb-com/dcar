		var animateXmas = (function(window, document) {
		var img=ROOT+"plugins/snow/image/Santa_text.png";
		var width=620;
		var height=100;
		var count=6;
		var speed=4;
		var s = this;
		  this.events = (function() {

    var old = (window.attachEvent), slice = Array.prototype.slice,
    evt = {
      add: (old?'attachEvent':'addEventListener'),
      remove: (old?'detachEvent':'removeEventListener')
    };

    function getArgs(oArgs) {
      var args = slice.call(oArgs), len = args.length;
      if (old) {
        args[1] = 'on' + args[1]; // prefix
        if (len > 3) {
          args.pop(); // no capture
        }
      } else if (len === 3) {
        args.push(false);
      }
      return args;
    }

    function apply(args, sType) {
      var oFunc = args.shift()[evt[sType]];
      if (old) {
        oFunc(args[0], args[1]);
      } else {
        oFunc.apply(this, args);
      }
    }

    function addEvent() {
      apply(getArgs(arguments), 'add');
    }

    function removeEvent() {
      apply(getArgs(arguments), 'remove');
    }

    return {
      add: addEvent,
      remove: removeEvent
    };

  }());
		
			function doStart() {
				var imgXmas=document.createElement('div');
				imgXmas.style.background='url('+img+')';
				imgXmas.style.height=height+'px';
				imgXmas.style.width=width+'px';
				imgXmas.style.position='fixed';
				imgXmas.style.bottom='0px';
				imgXmas.style.zIndex=10;
				document.body.appendChild(imgXmas);
				var i=0;
				var left=0;
				var timeInterval=0
				var tempSpeed=speed;
				var positionLeft=0;
				setInterval(function() {
					timeInterval++;
					if(timeInterval==2)
					{
					timeInterval=0;
					i=i==count?0:i+1;
					
					imgXmas.style.backgroundPosition=positionLeft+'px ' + -(height*i)+'px';
					}
					left=left+tempSpeed; 
				 
					imgXmas.style.left= (left+tempSpeed)+'px';
					if((left+tempSpeed)>document.documentElement.offsetWidth)
					{
					positionLeft=width;
					tempSpeed=-speed;
					}
					
					if((left+tempSpeed)<(0-width)){
					positionLeft=0;
					tempSpeed=speed;
					}
				}, 50);
				s.events.remove(window, 'load', doStart);
			}
			s.events.add(window, 'load', doStart, false);

			return this;
		}(window, document));