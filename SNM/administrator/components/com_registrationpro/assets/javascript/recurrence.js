var $select_value;
var $content = $("#recurrence_output");
var $recc = $("#recc");

function start_recurrencescript() {
	var $type = $("#recurrence_type").val();
	if (!isNaN($type)) {
		$("#recurrence_select").val($type);
		output_recurrencescript();
	}
}

function output_recurrencescript() {
	var $select_value = $("#recurrence_select").val();
	var $content = $("#recurrence_output");
	var $recc = $("#recc");
	var $recc_label = $("#recc_label");

	$("#repeat_every").css('display', 'none');
	$("#repeat_on").css('display', 'none');
	
	if ($select_value != 0) {
		var $element = generate_output($select_output[$select_value], $select_value);
		$content.empty().append($element);
		set_parameter();

		if (navigator.appName == "Microsoft Internet Explorer") {
			$("#counter_row").css('display', 'inline');
			$("#repeat_every").css('display', 'inline');
			$("#repeat_on").css('display', 'none');
			$recc.css('display', 'inline');
		} else {
			$("#counter_row").css('display', 'table-row');
			$("#repeat_every").css('display', 'table-row');
			$("#repeat_on").css('display', 'none');
			$recc.css('display', 'table-row');
		}

		if($select_value == 5){
			$("#repeat_every").css('display', 'none');
			$("#counter_row").css('display', 'none');
			if (navigator.appName == "Microsoft Internet Explorer") {
				$("#repeat_on").css('display', 'inline');
			} else {
				$("#repeat_on").css('display', 'table-row');
			}
		}

	} else {
		$content.text('');
		$("#recurrence_number").val(0);
		$("#recurrence_type").val(0);
		$("#counter_row").css('display', 'none');
		$recc.css('display', 'none');
	}
}

function generate_output($select_output, $select_value) {
	var $output_array = $select_output.split("[placeholder]");
	var $span = document.createElement("div");
	$span.style.cssText = 'vertical-align:top;';
	
	var $btns = document.createElement("div");
	$btns.style.cssText = 'width:200px;text-align:center;margin:0px;padding:5px;padding-top:10px;';
	
	for ($i = 0; $i < $output_array.length; $i++) {
		$weekday_array = $output_array[$i].split("[placeholder_weekday]");
		$date_array = $output_array[$i].split("[placeholder_dates]");

		if ($weekday_array.length > 1) {
			for ($k = 0; $k < $weekday_array.length; $k++) {
				$span.appendChild(document.createTextNode($weekday_array[$k]));
				if ($k == 0) $span.appendChild(generate_selectlist_weekday());
			}
		} else if ($date_array.length > 1) {
			for ($k = 0; $k < $date_array.length; $k++) {
				if ($k == 0) {
					$span.appendChild(document.createElement("br"));
					$btns.appendChild(generate_arrows1());
					$btns.appendChild(generate_arrows2());
					$span.appendChild($btns);
					var $dtb = dates_textbox();
					$span.appendChild($dtb);
				}
			}
		} else { $span.appendChild(document.createTextNode($output_array[$i]));}
		if ($i == 0) {$span.appendChild(generate_selectlist($select_value));}
	}
	return $span;
}

function generate_selectlist($select_value) {
	var $selectlist = document.createElement("select");
	$selectlist.name = "recurrence_selectlist";
	$selectlist.id = "recurrence_selectlist";
	$selectlist.onchange = set_parameter;
	
	switch($select_value) {
		case "1": $limit = 14; break; // days
		case "2": $limit = 8;  break; // weeks
		case "3": $limit = 12; break; // months
		case "4": $limit = 6;  break; // weekdays
		default:
			$selectlist.name = "recurrence_selectlist[]";
			$selectlist.multiple = "multiple";
			$selectlist.size = 8;
			$limit = 0; // dates
			break;
	}
	for ($j = 0; $j < $limit; $j++) {
		var $option = document.createElement("option");
		//if ($j == (parseInt($("recurrence_number").val()) - 1)) {
		if ($j == ($("recurrence_number").val() - 1)) {
			$option.selected = true;
		}
		if (($j >= 4) && ($select_value == 4)) {
			var $name_value = "";
			switch($j) {
				case 4:
					$name_value = $last;
					break;
				case 5:
					$name_value = $before_last;
					break;
			}
			$option.appendChild(document.createTextNode($name_value));
			$option.value = $j+1;
		} else {
			$option.appendChild(document.createTextNode($j + 1));
			$option.value = $j + 1;
		}
		$selectlist.appendChild($option);
	}
	return $selectlist;
}

function generate_selectlist_weekday() {
	var $selectlist = document.createElement("select");
	$selectlist.name = "recurrence_selectlist_weekday";
	$selectlist.id = "recurrence_selectlist_weekday";
	$selectlist.onchange = set_parameter;
	
	for ($j = 0; $j < 7; $j++) {
		var $option = document.createElement("option");
		//if ($j == parseInt($("recurrence_weekday").value)) {
		if ($j == $("recurrence_weekday").val()) {
			$option.selected = true;
		}
		$option.value = $j;
		$option.appendChild(document.createTextNode($weekday[$j]));
		$selectlist.appendChild($option);
	}
	return $selectlist;
}

function generate_arrows1() {
	var $button1 = document.createElement("input");
	$button1.setAttribute('type','button');
	$button1.name = "select_date_img";
	$button1.id = "select_date_img";
	$button1.value = "Add";
	$button1.setAttribute('onclick', "cal1.select(document.adminForm.select_date, 'select_date_img', 'yyyy-MM-dd'); return false;");
	$button1.addClass("btn btn-small btn-success");
	return $button1;
}

function generate_arrows2(){
	var $button1 = document.createElement("input");
	$button1.setAttribute('type','button');
	$button1.name = "recurrence_button2";
	$button1.id = "recurrence_button2";
	$button1.value = "Remove";
	$button1.setAttribute('onclick', "return RemoveDate();");
	$button1.addClass("btn btn-small btn-danger");
	return $button1;
}

function dates_textbox() {
	var $date_textbox = document.createElement("input");
	$date_textbox.setAttribute('type','text');
	$date_textbox.setAttribute('size', "10");
	$date_textbox.name = "select_date";
	$date_textbox.id = "select_date";
	$date_textbox.value = "";
	$date_textbox.addClass("hasDatepicker hiddenElement");
	$date_textbox.setAttribute('onchange', "AddDate();");
	return $date_textbox;
}

function AddDate() {
	$multiple_selectlist = document.getElementById("recurrence_selectlist");
	$datevalue = document.getElementById("select_date");

	if($datevalue.value == "") return false;

	for (var $i = 0; $i < $multiple_selectlist.options.length; $i++) {
		if ($multiple_selectlist.options[$i].value == $datevalue.value) {
			return false;
		}
	}

	var $option = document.createElement("option");
	$option.value = $datevalue.value;
	$option.appendChild(document.createTextNode($option.value));
	$multiple_selectlist.appendChild($option);

	for (var $i = 0; $i < $multiple_selectlist.options.length; $i++) {
		$multiple_selectlist.options[$i].selected = true;
	}
}

function RemoveDate() {
	$multiple_selectlist = document.getElementById("recurrence_selectlist");

	for (var $i = 0; $i < $multiple_selectlist.options.length; $i++) {
		if ($multiple_selectlist.options[ $i ].selected){
			$multiple_selectlist.options[$i] = null;
		}
	}

	for (var $i = 0; $i < $multiple_selectlist.options.length; $i++) {
		$multiple_selectlist.options[$i].selected = true;
	}
}

function set_parameter() {
	var $rec = $("#recurrence_select").val() * 1;
	$("#recurrence_type").val($rec);

	var $sel_list = $("#recurrence_selectlist").val() * 1;
	$("#recurrence_number").val($sel_list);

	if ($rec == 4) {
		var $weekday = $("#recurrence_selectlist_weekday").val();
		$("#recurrence_weekday").val($weekday);
	}

	//alert('REC_NUM = ' + $("#recurrence_number").val() + ', REC_WEEKDAY = ' + $("#recurrence_weekday").val());
	
	if($rec == 5) $("#recurrence_number").val(1);
}

function unlimited_starter() {
	document.getElementById('adminForm').onsubmit = submit_unlimited();
}

function include_unlimited($unlimited_name) {
	$("#recurrence_counter").val($unlimited_name);
	return false;
}

function submit_unlimited() {
	var $value = $("#recurrence_counter").val();
	var $date_array = $value.split("-");
	if ($date_array.length < 3) {
		$("#recurrence_counter").val("0000-00-00");
	}
}