(function(){
	
//First of all we need to extend some functionality
//that doesn't exist in MooTools 1.11

//We need the flatten method from Moo 1.2
if(typeof Array.flatten != 'function') {
	Array.extend({
		flatten: function(){
			var array = [];
			for (var i = 0, l = this.length; i < l; i++){
				var type = $type(this[i]);
				if (!type) continue;
				array = array.concat((type == 'array' || type == 'collection' || type == 'arguments') ? Array.flatten(this[i]) : this[i]);
			}
			return array;
		}
	});
};


//Very useful method by Nathan White for sorting based on visual positions
//http://www.nwhite.net/2009/02/13/visual-sorting/
Elements.implement({

	flatten: function(){
		var array = new Elements;
		for (var i = 0, l = this.length; i < l; i++){
			var type = $type(this[i]);
			if (!type) continue;
			array = array.concat((type == 'array' || type == 'collection' || type == 'arguments') ? Elements.flatten(this[i]) : this[i]);
		}
		return array;
	},
	 
	visualSort : function(tolerance,flatten){
 
		var tolerance = (tolerance) || .5;
		var idx = [], el, c1, c2, placed;
 
		for(var i = 0, l = this.length; i < l; i++){
			el = this[i]; c1 = el.getCoordinates();
			if(!i) idx[0] = [el, c1];
			else {
				j = 0; placed = false;
				while(j < i && !placed ){
					c2 = idx[j][1];
					if(c1.top < c2.top) placed = true;
					else j++;
				}
				idx.splice(j,0,[el,c1]);
			}
		}
 
		var rows = [], row = 0, sorted = [], slen, threshold;
		for(i = 0; i < l; i++){
			c1 = idx[i][1];
			if(!i){
				rows[row] = [c1.top,(c1.top + c1.height*tolerance)];
				sorted[row] = [ idx[i] ];
			}
			else {
				threshold = rows[row];
				if((threshold[0] <= c1.top) && (c1.top <= threshold[1]) ){
					j = 0; placed = false; slen = sorted[row].length;
					while(j < slen && !placed){
						c2 = sorted[row][j][1];
						if(c1.left < c2.left) placed = true;
						else j++
					}
					sorted[row].splice(j,0,idx[i]);
				} else {
					row++;
					rows[row] = [c1.top,(c1.top + c1.height*tolerance)];
					sorted[row] = [idx[i]];
				}
			}
		}
 
		var result = [];
		for(i = 0, l = sorted.length; i < l; i++){
			result[i] = [];
			for(j = 0, len = sorted[i].length; j < len; j++){
				result[i][j] = sorted[i][j][0];
			}
		}
 
		if(flatten) return $$(result.flatten());
		return result;
	}
});


/*
 * 
 */


imageSorter = new Class({ 
	Implements: [Events, Options],
	options: {
		handle: false
	
	},
	
	initialize: function(container, children, options) {

		//Set out options
		this.setOptions(options);
		this.children = children;
		this.container = $(container);
		this.sortlist = $$(children);

		var selfReference = this; //We need a reference object so we can refer back to this class instance
		
		//Make sure things are redrawn on a window resize
		window.addEvent('resize',function() {
			selfReference.sortImages();
		});
		
		//Work backwards through the list so positioning isn't messed up
		this.sortlist.reverse().each(function(el,i) {
			var pos = el.getCoordinates();
			
			var imgHandle = el.getFirst($(options.handle));
			el.makeDraggable( {
				container: this.container,
				handle: imgHandle,
				onComplete: function() {
					selfReference.sortImages();
				}
			});
		});
		this.sortImages();
	},
	
	//This takes the images and sorts them using Nathan's method
	sortImages: function() {
		
		//Get the visual sorting
		var sorted = this.sortlist.visualSort(.5,true);
		var container = this.container
		
		//Resort the HTML by re-injecting the elements into their parent
		sorted.each(function(el,i) {
			el.inject(container);
		});

		//Regrab the children so DOM reflects new HTML order
		this.sortlist = $$(this.children);
		
		//Cleanup the image alignments
		this.sortlist.setStyles({
			'position': 'static',
			'float':	'left',
			'top': null,
			'left': null
		});

		//Work backwards through the list so positioning isn't messed up
		this.sortlist.reverse().each(function(el,i) {
			var pos = el.getCoordinates();
			el.setStyles({
				'position': 'absolute',
				'top':		pos.top,
				'left':		pos.left
			});
		});
	},
	
	//This adds another element to the sort list
	addImage: function(el) {
		
		//Place the image in the right place in the DOM
		el.inject(this.container);
		this.sortlist.push(el);
		this.sortImages();
		
		var selfReference = this; //We need a reference object so we can refer back to this class instance
		
		//Make the item draggable and make sure it can trigger
		//further sorts
		var imgHandle = el.getFirst($(this.options.handle));
		el.makeDraggable( {
			container: this.container,
			handle: imgHandle,
			onComplete: function() {
				selfReference.sortImages();
			}
		});
	},
	
	//This removes an element from the list
	removeImage: function(el) {
		el.dispose();
		this.sortlist = $$(this.options.children);
		this.sortImages();
	}
});

})();