var http_baseuri = new String(document.getElementById('baseuri').href);
var agt = navigator.userAgent.toLowerCase();
var is_op = (agt.indexOf("opera") != -1);
var is_ie = (agt.indexOf("msie") != -1) && document.all && !is_op;
var is_ie5 = (agt.indexOf("msie 5") != -1) && document.all && !is_op;
var is_mac = (agt.indexOf("mac") != -1);
var is_gk = (agt.indexOf("gecko") != -1);
var is_sf = (agt.indexOf("safari") != -1);
var is_kq = (agt.indexOf("konqueror") != -1);

document.write('<script type="text/javascript" src="script/prototype162.js"></script>');
document.write('<script type="text/javascript" src="script/scriptaculous18/scriptaculous.js"></script>'); 
document.write('<script type="text/javascript" src="script/transition.js"></script>'); 
document.write('<script type="text/javascript" src="script/cal.js"></script>');
    document.write('<script type="text/javascript" src="script/blog_suggest.js"></script>');

var req = new String(location.pathname).toLowerCase();
if (req.indexOf('user/settings') != -1) {
	document.write('<script type="text/javascript" src="script/uploader.js"></script>');
}
if (req.indexOf('user/settings') != -1) {
	document.write('<script type="text/javascript" src="script/uploader.js"></script>');
}
else if (req.indexOf('user/register') != -1) {
    document.write('<script type="text/javascript" src="script/scriptaculous.js"></script>');
	document.write('<script type="text/javascript" src="script/register.js"></script>');
	document.write('<script type="text/javascript" src="script/select_area.js"></script>');
}
else if (req.indexOf('signup') != -1) {
	document.write('<script type="text/javascript" src="script/registerrox.js"></script>');
    document.write('<script type="text/javascript" src="script/blog_suggest.js"></script>');
}/*
else if (req.indexOf('blog') != -1) {
    document.write('<script type="text/javascript" src="script/prototype162.js"></script>');
	document.write('<script type="text/javascript" src="script/scriptaculous18/scriptaculous.js?effects"></script>');
} */
else if (req.indexOf('blog/create') != -1 || req.indexOf('blog') != -1 || req.indexOf('message/write')) {
    	document.write('<script type="text/javascript" src="script/tiny_mce/tiny_mce.js"></script>');
        document.write('<script type="text/javascript" src="script/blog_suggest.js"></script>');
}
else if (req.indexOf('message/write')) {
    document.write('<script type="text/javascript" src="script/tiny_mce/tiny_mce.js"></script>');
    document.write('<script type="text/javascript" src="script/blog_suggest.js"></script>');
}
if (req.indexOf('user/settings') != -1) {
    document.write('<script type="text/javascript" src="script/prototype.js"></script>');
    document.write('<script type="text/javascript" src="script/scriptaculous.js?effects"></script>');
    document.write('<script type="text/javascript" src="script/blog_suggest.js"></script>');
}
if (
		req.indexOf('blog/create') != -1 
		|| req.indexOf('blog/edit') != -1
		|| req.indexOf('user/settings') != -1
		|| req.indexOf('trip/create') != -1
		|| req.indexOf('gallery/show/image') != -1
		|| req.indexOf('message/write') != -1
	) {
	document.write('<script type="text/javascript" src="script/fieldset.js"></script>');
}
if (req.indexOf('gallery') != -1) {
    document.write('<script type="text/javascript" src="script/prototype162.js"></script>');
	document.write('<script type="text/javascript" src="script/scriptaculous18/scriptaculous.js?effects"></script>');
/*	document.write('<script type="text/javascript" src="script/inplaceeditor_extensions.js"></script>'); */
	document.write('<script type="text/javascript" src="script/lightview.js"></script>');
}
if (req.indexOf('gallery/upload') != -1) {
	document.write('<script type="text/javascript" src="script/uploader.js"></script>');
	document.write('<script type="text/javascript" src="script/gallery.js"></script>');
}
if (req.indexOf('trip') != -1) {
	document.write('<script type="text/javascript" src="script/scriptaculous.js"></script>');
}
if (req.indexOf('bod') != -1) {
	document.write('<script type="text/javascript" src="script/scriptaculous.js"></script>');
}
if (req.indexOf('thepeople') != -1) {
	document.write('<script type="text/javascript" src="script/scriptaculous.js"></script>');
	document.write('<script type="text/javascript" src="script/transition.js"></script>');
}
if (req.indexOf('tour/meet') != -1) {
    document.write('<script type="text/javascript" src="script/prototype162.js"></script>');
	document.write('<script type="text/javascript" src="script/scriptaculous18/scriptaculous.js?effects"></script>');
	document.write('<script type="text/javascript" src="script/lightview.js"></script>');
}
if (req.indexOf('searchmembers') != -1) {
    document.write('<script type="text/javascript" src="script/prototype162.js"></script>');
	document.write('<script type="text/javascript" src="script/scriptaculous18/scriptaculous.js?effects"></script>');
	document.write('<script type="text/javascript" src="script/prototip.js"></script>');
	document.write('<script type="text/javascript" src="script/labeled_marker.js"></script>');
}
