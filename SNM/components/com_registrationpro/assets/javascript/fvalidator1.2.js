/* ************************************************************************************* *\
 * The MIT License
 * 
 *  - Updated for usage with MooTools 1.2
 *  - Added language support
 *  - Added a few validation types
 *  - Copyright (c) 2008 Lennart Pilon - http://ljpilon.nl
 *
 * Copyright (c) 2007 Fabio Zendhi Nagao - http://zend.lojcomm.com.br
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
\* ************************************************************************************* */

var $count = 0;
var $arr_confirm = new Array(); 
var $arr_confirm_with = new Array();

var fValidator = new Class({
	Implements: [Options, Events],
	options: {
		msgContainerTag: "span",
		msgClass: "fValidator-msg",

		/*styleNeutral: {"background-color": "#ffc", "border-color": "#cc0"},
		styleInvalid: {"background-color": "#fcc", "border-color": "#c00"},
		styleValid: {"background-color": "#cfc", "border-color": "#0c0"},*/

		language: "en",
		languageConfig: {	
			en: {	required:	"This field is required.",
					alpha:		"This field accepts alphabetic characters only.",
					alphanum:	"This field accepts alphanumeric characters only.",
					integer:	"Please enter a valid integer.",
					real:		"Please enter a valid number.",
					date:		"Please enter a valid date (mm/dd/yyyy).",
					dateISO8601:"Please enter a valid date (yyyy-mm-dd).",
					dateEU:		"Please enter a valid date (dd-mm-yyyy).",
					email:		"Please enter a valid email.",
					phone:		"Please enter a valid phone.",
					url:		"Please enter a valid url.",
					zip:		"Please enter a valid postal code",
					confirm:	"Confirm value does not match original one."
				},
			nl: {	required:	"Dit veld is verplicht.",
					alpha:		"U kunt in dit veld alleen karakters uit het alphabet invoeren.",
					alphanum:	"U kunt in dit veld alleen alphanumerieke karakters invoeren.",
					integer:	"Voer een geheel getal in.",
					real:		"Voer een getal in.",
					date:		"Voer een geldige datum in (mm/dd/yyyy).",
					dateISO8601:"Voer een geldige datum in (yyyy-mm-dd).",
					dateEU:		"Voer een geldige datum in (dd-mm-yyyy).",
					email:		"Voer een geldig emailadres in.",
					phone:		"Voer een geldig telefoonnummer in.",
					url:		"Voer een geldige url in.",
					zip:		"Voer een geldigep postcode in",
					confirm:	"Het bevestigingswachtwoord komt niet overeen met het originele wachtwoord."
				},
			/* Italian and spanish translation by davcaffa */
			it: {	required:	"Questo campo &egrave; obbligatorio.",
					alpha:		"Questo campo accetta solo lettere.",
					alphanum:	"Questo campo accetta solo caratteri alfanumerici.",
					integer:	"Per favore inserisca un valido numero intero.",
					real:		"Per favore inserisca un numero valido.",
					date:		"Per favore inserisca una data in formato valido (mm/gg/aaaa).",
					dateISO8601:"Per favore inserisca una data in formato valido (aaaa-mm-gg).",
					dateEU:		"Per favore inserisca una data in formato valido (mm-gg-aaaa).",
					email:		"Per favore inserisca una email valida.",
					phone:		"Per favore inserisca un telefono valido.",
					url:		"Per favore inserisca un indirizzo internet valido.",
					zip:		"Per favore inserisca un codice postale valido.",
					confirm:	"La password di controllo non &egrave; uguale alla password originale."
				},
			es: {	required:	"Este campo es un campo obligatorio.",
					alpha:		"Este campo acepta solo letteras.",
					alphanum:	"Este campo acepta solo caracteres alfanum&eacute;ricos.",
					integer:	"Este campo acepta solo n&uacute;meros enteros.",
					real:		"Este campo acepta &uacute;nicamente caracteres num&eacute;ricos.",
					date:		"Por favor inserte una fetcha en formato valido (mm/dd/aaaa).",
					dateISO8601:"Por favor inserte una fetcha en formato valido (aaaa-mm-dd).",
					dateEU:		"Por favor inserte una fetcha en formato valido (mm-dd-aaaa).",
					email:		"Por favor inserte un correo electr&oacute;nico valido.",
					phone:		"Por favor inserte un numero de tel&eacute;fono valido.",
					url:		"Por favor inserte una direc&iacute;on de internet valido.",
					zip:		"Por favor inserte un codigo postal valido.",
					confirm:	"La contrase&ntilde;a de controlo no es igual a la contrase&ntilde;a original."
					},
			/* French translation by Stephane */
			fr : { 	required: "Ce champ est obligatoire.", 
					alpha: "Ce champ n'accepte que des caract&egrave;res alphab&eacute;tiques.", 
					alphanum: "Ce champ n'accepte que des caract&egrave;res alpha-num&eacute;riques.", 
					integer: "Veuillez saisir un entier.", 
					real: "Veuillez saisir un nombre.", 
					date: "Veuillez saisir une date valide (mm/dd/yyyy).", 
					dateISO8601:"Veuillez saisir une date valide (yyyy-mm-dd).", 
					dateEU: "Veuillez saisir une date valide (dd-mm-yyyy).", 
					email: "Veuillez saisir un e-mail valide.", 
					phone: "Veuillez saisir un num&eacute;ro de t&eacute;l&eacute;phone valide.", 
					url: "Veuillez saisir une url valide.",
					zip: "Veuillez saisir une code postal valide.",
					confirm: "La confirmation du mot de passe ne correspond pas &agrave; l'original." 
					// You can change "e-mail" to "adresse &eacute;lectronique", or "&eacute;mel" for the french purists (not in use).
					},
			/* German translation by Axel Beck */
			de: { 	required: "Dies ist ein Pflichtfeld.",
					alpha: "Hier sind ausschlie&szlig;lich Buchstaben erlaubt.", 
					alphanum: "Hier sind ausschlie&szlig;lich Buchstaben und Ziffern erlaubt.", 
					integer: "Bitte geben Sie eine g&uuml;ltige ganze Zahl ein.", 
					real: "Bitte geben Sie eine g&uuml;ltige Zahl ein.", 
					date: "Bitte geben Sie ein g&uuml;ltiges Datum ein (MM/TT/JJJJ).", 
					dateISO8601:"Bitte geben Sie ein g&uuml;ltiges Datum ein (JJJJ-MM-TT).", 
					dateEU: "Bitte geben Sie ein g&uuml;ltiges Datum ein (TT-MM-JJJJ).", 
					email: "Bitte geben Sie eine g&uuml;ltige E-Mailadresse ein.", 
					phone: "Bitte geben Sie eine g&uuml;ltige Telefonnummer ein.", 
					url: "Bitte geben Sie einen g&uuml;ltigen URL ein.", 
					zip: "Bitte geben Sie einen g&uuml;ltigen Postcode ein.", 
					confirm: "Die Wiederholung des Passworts stimmt nicht mit dem Original &uuml;berein." 
					},
			/* Swedish translation by Tom Stone (http://tomstone.se) */
			sv: {	required: "Obligatoriskt fält.",
					alpha:  "Endast bokstäver godkänns.",
					alphanum: "Endast siffror godkänns.",
					integer: "Skriv in ett heltal.",
					real:  "Skriv in ett nummer.",
					date:  "Skriv in ett giltigt datum (mm/dd/åååå).",
					dateISO8601:"Skriv in ett giltigt datum (åååå-mm-dd).",
					dateEU:  "Skriv in ett giltigt datum (dd-mm-åååå).",
					email:  "Skriv en giltig emailadress.",
					phone:  "Skriv ett giltigt telefonnummer.",
					url:  "Skriv en giltig webbadress.",
					confirm: "Lösenord och Bekräfta lösenord är olika."
					},
			/* Portugese translation by akira_lee */
			pt: { required: "Este campo é de preenchimento obrigatório.",
				  alpha:  "Este campo só aceita caracteres alfanuméricos.",
				  alphanum: "Este campo só aceita caracteres alfanuméricos.",
				  integer: "Digite um número inteiro válido.",
				  real:  "Digite números válidos.",
				  date:  "Digite uma data válida (mm/dd/yyyy).",
				  dateISO8601:"Digite uma data válida (yyyy-mm-dd).",
				  dateEU:  "Digite uma data válida (mm-dd-yyyy).",
				  email:  "Preencha o campo com um e-mail válido.",
				  phone:  "Digite um número de telefone válido.",
				  url:  "Digite um URL válido.",
				  confirm: "A Password de confirmação não é igual à Password original."
				 }
		},

		required: {type: "required", re: /[^.*]/},
		alpha: {type: "alpha", re: /^[a-z ._-]+$/i},
		alphanum: {type: "alphanum", re: /^[a-z0-9 ._-]+$/i},
		integer: {type: "integer", re: /^[-+]?\d+$/},
		real: {type: "real", re: /^[-+]?\d*\.?\d+$/},
		date: {type: "date", re: /^((((0[13578])|([13578])|(1[02]))[\/](([1-9])|([0-2][0-9])|(3[01])))|(((0[469])|([469])|(11))[\/](([1-9])|([0-2][0-9])|(30)))|((2|02)[\/](([1-9])|([0-2][0-9]))))[\/]\d{4}$|^\d{4}$/},
		dateISO8601: {type: "dateISO8601", re: /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/},
		dateEU: {type: "dateEU", re: /^(((([1-9])|([0-2][0-9])|(3[01]))[-]((0[13578])|([13578])|(1[02])))|((([1-9])|([0-2][0-9])|(30))[-]((0[469])|([469])|(11)))|((([1-9])|([0-2][0-9])))[-](2|02))[-]\d{4}$|^\d{4}$/},
		email: {type: "email", re: /^[a-z0-9._%-]+@[a-z0-9.-]+\.[a-z]{2,4}$/i},
		phone: {type: "phone", re: /^[\d\s ().-]+$/},
		url: {type: "url", re: /^(http|https|ftp)\:\/\/[a-z0-9\-\.]+\.[a-z]{2,3}(:[a-z0-9]*)?\/?([a-z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$/i},
		confirm: {type: "confirm"},
		zip: {type: "zip", re: /^\d{5}(-\d{4})?$/i},

		onValid: Class.empty,
		onInvalid: Class.empty
	},

	initialize: function(form, options) {
		this.form = $(form);
		this.setOptions(options);

		this.fields = this.form.getElements("*[class^=fValidate]");
		this.validations = [];

		this.fields.each(function(element) {
			if(!this._isChildType(element)) element.setStyles(this.options.styleNeutral);
			element.cbErr = 0;
			var classes = element.getProperty("class").split(' ');
			classes.each(function(klass) {
				if(klass.match(/^fValidate(\[.+\])$/)) {
					var aFilters = eval(klass.match(/^fValidate(\[.+\])$/)[1]);
					for(var i = 0; i < aFilters.length; i++) {
						if(this.options[aFilters[i]]) this.register(element, this.options[aFilters[i]]);
						if(aFilters[i].charAt(0) == '=') {
							this.register(element, $extend(this.options.confirm, {idField: aFilters[i].substr(1)}));
							
							$arr_confirm[$count] = element.id;
							$arr_confirm_with[$count] = aFilters[i].substr(1);							
							$count++;
						}
					}
				}
			}.bind(this));
		}.bind(this));

		this.form.addEvents({
			"submit": this._onSubmit.bind(this),
			"reset": this._onReset.bind(this)
		});
	},

	register: function(field, options) {
		field = $(field);
		this.validations.push([field, options]);
		field.addEvent("blur", function() {
			this._validate(field, options);
		}.bind(this));
	},

	_isChildType: function(el) {
		var elType = el.type.toLowerCase();
		if((elType == "radio") || (elType == "checkbox")) return true;
		return false;
	},

	_validate: function(field, options) {
		switch(options.type) {
			case "confirm":
				
				for (var z = 0; z < $count; z++)
				{
					//alert(field.id+"  -  "+$arr_confirm[z]);
					if(field.id == $arr_confirm[z]){																		
						//alert(field.value+"  -  "+$arr_confirm_with[z]);	
						if(field.value == this.form.elements[$arr_confirm_with[z]].value)	{
							this._msgRemove(field, options);
						}else{							
							this._msgInject(field, options);
						}
					}
				}
				break;
										
				/*if($(options.idField).get('value') == field.get('value')) this._msgRemove(field, options);
				else this._msgInject(field, options);
				break;*/
			default:
				if(options.re.test(field.get('value'))) this._msgRemove(field, options);
				else this._msgInject(field, options);
		}
	},

	_validateChild: function(child, options) {
		var nlButtonGroup = this.form[child.getProperty("name")];
		var cbCheckeds = 0;
		var isValid = true;
 		for(var i = 0; i < nlButtonGroup.length; i++) {
			if(nlButtonGroup[i].checked) {
				cbCheckeds++;
				if(!options.re.test(nlButtonGroup[i].get('value'))) {
					isValid = false;
					break;
				}
			}
		}
		if(cbCheckeds == 0 && options.type == "required") isValid = false;
		if(isValid) this._msgRemove(child, options);
		else this._msgInject(child, options);
	},

	_msgInject: function(owner, options) {
		if(!$(owner.getProperty("id") + options.type +"_msg")) {
			var msgContainer = new Element(this.options.msgContainerTag, {"id": owner.get("id") + options.type +"_msg", "class": this.options.msgClass})
				.set('html', this.options.languageConfig[this.options.language][options.type])
				.setStyle("opacity", 0)
				.injectAfter(owner);
			var myFx = new Fx.Tween(msgContainer, {
					duration: 500,
					transition: Fx.Transitions.linear					
				}).start('opacity', 0, 1);
			owner.cbErr++;
			this._chkStatus(owner, options);
		}
	},

	_msgRemove: function(owner, options, isReset) {
		isReset = isReset || false;
		if($(owner.getProperty("id") + options.type +"_msg")) {
			var el = $(owner.getProperty("id") + options.type +"_msg");
			var myFx = new Fx.Tween(el, {
					duration: 500,
					transition: Fx.Transitions.linear,
					onComplete: function() {el.destroy()}
				}).start('opacity', 1, 0);
			if(!isReset) {
				owner.cbErr--;
				this._chkStatus(owner, options);
			}
		}
	},

	_chkStatus: function(field, options) {
		if(field.cbErr == 0) {
			var myFx = new Fx.Morph(field, {
					duration: 500,
					transition: Fx.Transitions.linear					
				}).start(this.options.styleValid);			
			this.fireEvent("onValid", [field, options], 50);
		} else {
			var myFx = new Fx.Morph(field, {
					duration: 500,
					transition: Fx.Transitions.linear					
				}).start(this.options.styleInvalid);			
			this.fireEvent("onInvalid", [field, options], 50);
		}
	},

	_onSubmit: function(event) {
		var event = new Event(event);
		var isValid = true;

		this.validations.each(function(array) {
			if(this._isChildType(array[0])) this._validateChild(array[0], array[1]);
			else this._validate(array[0], array[1]);
			if(array[0].cbErr > 0) isValid = false;
		}.bind(this));

		if(!isValid){ 
			//$fvalidate_flag = 0; 
			document.getElementById("mandatory_note").style.display = "";
			event.stop(); 
		}else {
			/*$fvalidate_flag = 1; 	
			alert("hello");
			alert($fvalidate_flag);*/
			document.getElementById("mandatory_note").style.display = "none";
		}
		
		return isValid;
	},

	_onReset: function() {
		this.validations.each(function(array) {
			if(!this._isChildType(array[0])) array[0].setStyles(this.options.styleNeutral);
			array[0].cbErr = 0;
			this._msgRemove(array[0], array[1], true);
		}.bind(this));
	}
});