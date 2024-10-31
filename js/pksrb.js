jQuery(document).ready(function($) {
var rcSiteKey = pksrbParam.rcSiteKey;
grecaptcha.ready(function() {
grecaptcha.execute(rcSiteKey).then(function(token){
  // Add token to hidden input filed for the form
  if($("#pk_captcha").length) {
	$("#pk_captcha").val(token);
  }
});
});
});