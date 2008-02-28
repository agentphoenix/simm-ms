/**
 * jQuery.Preload - Multifunctional preloader
 * Copyright (c) 2008 Ariel Flesler - aflesler(at)gmail(dot)com
 * Licensed under GPL license (http://www.opensource.org/licenses/gpl-license.php).
 * Date: 2/18/2008
 * @author Ariel Flesler
 * @version 1.0.4
 **/
;(function($){var g=$.preload=function(c,d){if(c.split)c=$(c);if(!c.length)return;d=$.extend({},g.defaults,d);var f=$.map(c,function(a){if(!a)return'';if(a.split)return d.base+a+d.ext;var b=a.src||a.href||'';if(d.placeholder&&a.src)a.src=d.placeholder;if(d.find)b=b.replace(d.find,d.replace);return b}),h={loaded:0,failed:0,next:0,done:0,total:f.length},i=$(Array(d.threshold)).map(function(){return new Image}).load(handler).error(handler).each(fetch);function handler(e){h.found=e.type=='load';h.image=this.src;var a=h.original=c[this.index];h[h.found?'loaded':'failed']++;h.done++;if(d.placeholder&&a.src)a.src=h.found&&h.image||d.notFound||a.src;if(d.onComplete)d.onComplete(h);if(h.done<h.total)fetch(0,this);else{i.unbind('load').unbind('error');i=0;if(d.onFinish)d.onFinish(h)}};function fetch(i,a){if(h.next==h.total)return false;a.index=h.next;a.src=f[h.next++];if(d.onRequest){h.image=a.src;h.original=c[h.next-1];d.onRequest(h)}}};g.defaults={threshold:1,base:'',ext:'',replace:''};$.fn.preload=function(a){g(this,a);return this}})(jQuery);