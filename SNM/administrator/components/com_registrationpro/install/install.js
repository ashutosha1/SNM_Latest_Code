var install_timeout;
var num_steps = 7;
var install_script = 'index.php';

function initInstall() {
	install_timeout = setTimeout('ajaxInstaller(2)', 3000);
}

function ajaxInstaller(step) {
	updateStep(step);
		
	new Request({
		url: install_script,		
		onFailure: function(){
			setError(REGPRO_AJAX_FAILURE);
		},
		onComplete: function(inResponse) {	
			try {
				json = JSON.decode(inResponse);
			} catch (err) {
				setError(inResponse);
			}
			if (typeof json == 'object') {
				if (parseInt(json.status) == 1) {
					var next_step = parseInt(step)+1;
					if (step < num_steps) {
						var tmt1 = setTimeout('updateFeedback(\'' + json.message + '\')', 2000);
						var tmt2 = setTimeout('updateTitle(\'' + json.done + '\',1,' + step + ')', 4000);
					} else if (step == num_steps) {
						var tmt1 = setTimeout('successRedirect()', 5000);
					}
					if (next_step <= num_steps) {
						if (install_timeout) clearTimeout(install_timeout);
						install_timeout = setTimeout('ajaxInstaller(' + next_step + ')', 7000);
					}
				} else {
					setError(json.error);
				}
			} else {
			}
		}
	}).post({
		"option": 'com_registrationpro',
		"task": "install",
		"tmpl": "component",
		"format": "raw",
		"step": step,
		"install": 1	
	});	
}

function updateStep(step) {
	var prev_step = parseInt(step) - 1;
	$('error_holder').style.dispplay = 'none';
	$('regpro_install_error').innerHTML = '';
	$('regpro_install_feedback').innerHTML = '';
	$('install_step_' + prev_step).className = '';
	$('install_step_' + step).className = 'regpro_install_steps_active';
	$('step_label').innerHTML = $('install_step_' + step).innerHTML;
	updateTitle($('install_step_desc_' + step).innerHTML,0,step);
}

function updateTitle(msg,add,step) {
	if (add) {
		$('regpro_install_title').innerHTML = $('regpro_install_title').innerHTML + msg;
		$('regpro_install_loading').style.display = 'none';
	} else {
		$('regpro_install_title').innerHTML = msg;
		if (step < num_steps) $('regpro_install_loading').style.display = 'block';
	}
}

function updateFeedback(msg) {
	if (msg) $('regpro_install_feedback').innerHTML = $('regpro_install_feedback').innerHTML + msg;
}

function setError(error) {
	$('regpro_install_loading').style.display = 'none';
	$('error_holder').style.display = 'block';
	$('regpro_install_error').innerHTML = buildErrorString(error);
}

function buildErrorString(error) {
	var result = '<li>' + REGPRO_AJAX_ERRORS + '</li>';
	if (typeof error != 'object') {
		result += '<li>' + error + '</li>';
	} else {
		for (var i=0; i < error.length; i++) {
			result += '<li>' + error[i] + '</li>';
		}
	}
	return result;
}

function successRedirect() {
	window.location.href = REGPRO_SUCCESS_LINK;
}