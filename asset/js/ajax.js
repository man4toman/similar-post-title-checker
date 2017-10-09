/*
 * Result Element
 */
 
jQuery( "<div id='spresulte'></div>" ).insertAfter( "input#title[type='text']" );

/*
highlight v4
Highlights arbitrary terms.
<http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html>
MIT license.
Johann Burkard
<http://johannburkard.de>
<mailto:jb@eaio.com>
*/

jQuery.fn.highlight = function(pat) {
 function innerHighlight(node, pat) {
  var skip = 0;
  if (node.nodeType == 3) {
   var pos = node.data.toUpperCase().indexOf(pat);
   if (pos >= 0) {
    var spannode = document.createElement('span');
    spannode.className = 'highlight';
    var middlebit = node.splitText(pos);
    var endbit = middlebit.splitText(pat.length);
    var middleclone = middlebit.cloneNode(true);
    spannode.appendChild(middleclone);
    middlebit.parentNode.replaceChild(spannode, middlebit);
    skip = 1;
   }
  }
  else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
   for (var i = 0; i < node.childNodes.length; ++i) {
    i += innerHighlight(node.childNodes[i], pat);
   }
  }
  return skip;
 }
 return this.length && pat && pat.length ? this.each(function() {
  innerHighlight(this, pat.toUpperCase());
 }) : this;
};

jQuery.fn.removeHighlight = function() {
 return this.find("span.highlight").each(function() {
  this.parentNode.firstChild.nodeName;
  with (this.parentNode) {
   replaceChild(this.firstChild, this);
   normalize();
  }
 }).end();
};

/*
 * jQuery ajax actions
 */
 
jQuery("#sp-screen-options-apply").on('click', function(){
	var splimit   = jQuery('#sp_screen_options_limit').val();
	var spminchar = jQuery('#sp_screen_options_minchar').val();
	jQuery.post(ajaxurl,{splimit:splimit, spminchar:spminchar, action:'sp_ajax_hook_sc'},function(t){
		var e=t.substr(0,t.length-1);
	})
	.done(function() { jQuery(".metabox-prefs .success").fadeIn(500).delay(1000).fadeOut(1500); })
	.fail(function() { jQuery(".metabox-prefs .error").fadeIn(500).delay(1000).fadeOut(1500); });
});

jQuery("#title").on('keyup', function(){
	jQuery("#spresulte").html('<div class="spinner"></div>');
	var sptitle = jQuery(this).val();
	jQuery.post(ajaxurl,{sptitle:sptitle, action:'sp_ajax_hook'},function(t){
		var e=t.substr(0,t.length-1);
		jQuery("#spresulte").html(e);
		jQuery('#spresulte').highlight(sptitle);
	})
});