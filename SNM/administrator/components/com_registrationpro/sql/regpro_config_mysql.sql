DELETE FROM `#__registrationpro_config`;
INSERT INTO `#__registrationpro_config` (`id`, `setting_name`, `setting_value`) VALUES
(1, 'oldevent', '0'),
(2, 'minus', '1'),
(3, 'paypalemail', ''),
(4, 'paypalmode', '0'),
(5, 'classsuffix', '-regpro'),
(6, 'showtime', '1'),
(7, 'showevdesc', '1'),
(8, 'showtitle', '1'),
(9, 'showlocation', '1'),
(10, 'showdetails', '1'),
(11, 'showlongdesc', '1'),
(12, 'showurl', '1'),
(13, 'showmapserv', '0'),
(14, 'map24id', ''),
(15, 'showhead', '1'),
(16, 'showintro', '1'),
(17, 'formatdate', 'd M,Y'),
(18, 'formattime', 'H:i:s'),
(19, 'introtext', ''),
(20, 'cbintegration', '0'),
(21, 'cbchoose', '0'),
(22, 'emailconfirmsubject', 'Congratulations! You registered to {eventtitle}'),
(23, 'emailconfirmbody', 'Dear {fullname}, <br /><br /> the {eventtitle} will take place at {location}, starting from {eventstart}.'),
(24, 'emailstatussubject', 'The {eventtitle} changed it''s status to {eventstatus}...'),
(25, 'emailstatusbody', 'New status for {eventtitle}: {eventstatus}'),
(26, 'emailremindersubject', '{eventtitle} reminder'),
(27, 'emailreminderbody', 'This e-mail is a reminder for the {eventtitle}.'),
(28, 'currency_sign', '$'),
(29, 'currency_value', 'USD'),
(30, 'transaction_name', 'Payment for Registration Pro Ticket(s)'),
(31, 'register_notify', ''),
(32, 'showurl', '1'),
(33, 'eventslimit', '10'),
(34, 'paymentmethod', 'Paypal Payment'),
(35, 'checkout_vendorid', '123'),
(36, 'checkout_secretword', ''),
(37, 'checkout_mode', '0'),
(38, 'offlinepayment', 'This is offline payment for the event component.'),
(39, 'taxrate', '1'),
(40, 'require_registration', '0'),
(41, 'numberofdays', '0'),
(42, 'emailtoregistersubject', 'Test subject'),
(43, 'emailtoregisterbody', 'Test Body'),
(44, 'table_update', ''),
(45, 'collapse_categories', '0'),
(46, 'maxseat', '1'),
(47, 'pendingseat', '0'),
(48, 'registeredseat', '0'),
(49, 'showcategory', '0'),
(50, 'duplicate_email_registration', '0'),
(51, 'default_userstatus_free_events', '0'),
(52, 'default_userstatus_offline_payment', '0'),
(53, 'quantitylimit', '10'),
(54, 'default_layout', '1'),
(55, 'rss_enable', '0'),
(56, 'archiveby', '1'),
(57, 'showeventdates', '1'),
(58, 'showeventtimes', '1'),
(59, 'showpricecolumn', '1'),
(60, 'showlocationcolumn', '0'),
(61, 'thankspagelink', ''),
(62, 'multiple_registration_button', '1'),
(63, 'enable_discount_code', '1'),
(64, 'show_all_dates_in_calendar', '0'),
(65, 'showshortdescriptioncolumn', '1'),
(66, 'mainadminemailconfirmsubject', 'New user regsiteration with {eventtitle}'),
(67, 'mainadminemailconfirmbody', 'Dear Admin, <br /><br /> {fullname} is registered with the {eventtitle} will take place at {location}.'),
(68, 'eventadminemailconfirmsubject', 'New user regsiteration with {eventtitle}'),
(69, 'eventadminemailconfirmbody', 'Dear Admin, <br /><br /> {fullname} is registered with the {eventtitle} will take place at {location}.'),
(70, 'eventlistordering', '1'),
(71, 'event_terms_and_conditions', '1'),
(72, 'disablethanksmessage', '0'),
(73, 'user_ids', ''),
(74, 'user_categories', ''),
(75, 'user_locations', ''),
(76, 'frontend_help_link', '0'),
(77, 'moderatoremailsubject', 'New event has been created by {fullname}'),
(78, 'moderatoremailbody', '{fullname} is created the {eventtitle} will take place at {location}.'),
(79, 'moderatoremail', ''),
(80, 'event_moderation', '0'),
(81, 'calendar_category_filter', '0'),
(82, 'message_color', '#cf0000'),
(83, 'show_max_seats_on_details_page', '0'),
(84, 'show_avaliable_seats_on_details_page', '0'),
(85, 'show_registered_seats_on_details_page', '0'),
(86, 'show_footer', '0'),
(87, 'countrylist', '<option value="United States" >United States</option>
  <option value="Canada" >Canada</option>
  <option value="Afghanistan" >Afghanistan</option>
  <option value="Albania" >Albania</option>
  <option value="Algeria" >Algeria</option>
  <option value="American Samoa" >American Samoa</option>

  <option value="Andorra" >Andorra</option>
  <option value="Angola" >Angola</option>
  <option value="Anguilla" >Anguilla</option>
  <option value="Antarctica" >Antarctica</option>
  <option value="Antigua And Barbuda" >Antigua And Barbuda</option>
  <option value="Argentina" >Argentina</option>

  <option value="Armenia" >Armenia</option>
  <option value="Aruba" >Aruba</option>
  <option value="Australia" >Australia</option>
  <option value="Austria" >Austria</option>
  <option value="Azerbaijan" >Azerbaijan</option>
  <option value="Bahamas" >Bahamas</option>

  <option value="Bahrain" >Bahrain</option>
  <option value="Bangladesh" >Bangladesh</option>
  <option value="Barbados" >Barbados</option>
  <option value="Belarus" >Belarus</option>
  <option value="Belgium" >Belgium</option>
  <option value="Belize" >Belize</option>

  <option value="Benin" >Benin</option>
  <option value="Bermuda" >Bermuda</option>
  <option value="Bhutan" >Bhutan</option>
  <option value="Bolivia" >Bolivia</option>
  <option value="Bosnia And Herzegowina" >Bosnia And Herzegowina</option>
  <option value="Botswana" >Botswana</option>

  <option value="Bouvet Island" >Bouvet Island</option>
  <option value="Brazil" >Brazil</option>
  <option value="British Indian Ocean Territory" >British Indian Ocean Territory</option>
  <option value="Brunei Darussalam" >Brunei Darussalam</option>
  <option value="Bulgaria" >Bulgaria</option>
  <option value="Burkina Faso" >Burkina Faso</option>

  <option value="Burundi" >Burundi</option>
  <option value="Cambodia" >Cambodia</option>
  <option value="Cameroon" >Cameroon</option>
  <option value="Cape Verde" >Cape Verde</option>
  <option value="Cayman Islands" >Cayman Islands</option>
  <option value="Central African Republic" >Central African Republic</option>

  <option value="Chad" >Chad</option>
  <option value="Chile" >Chile</option>
  <option value="China" >China</option>
  <option value="Christmas Island" >Christmas Island</option>
  <option value="Cocos (Keeling) Islands" >Cocos (Keeling) Islands</option>
  <option value="Colombia" >Colombia</option>

  <option value="Comoros" >Comoros</option>
  <option value="Congo" >Congo</option>
  <option value="Congo" >Congo</option>
  <option value=" The Democratic Republic Of The" > The Democratic Republic Of The</option>
  <option value="Cook Islands" >Cook Islands</option>
  <option value="Costa Rica" >Costa Rica</option>

  <option value="Cote D&rsquo;Ivoire" >Cote D&rsquo;Ivoire</option>
  <option value="Croatia (Local Name: Hrvatska)" >Croatia (Local Name: Hrvatska)</option>
  <option value="Cuba" >Cuba</option>
  <option value="Cyprus" >Cyprus</option>
  <option value="Czech Republic" >Czech Republic</option>
  <option value="Denmark" >Denmark</option>

  <option value="Djibouti" >Djibouti</option>
  <option value="Dominica" >Dominica</option>
  <option value="Dominican Republic" >Dominican Republic</option>
  <option value="East Timor" >East Timor</option>
  <option value="Ecuador" >Ecuador</option>
  <option value="Egypt" >Egypt</option>

  <option value="El Salvador" >El Salvador</option>
  <option value="Equatorial Guinea" >Equatorial Guinea</option>
  <option value="Eritrea" >Eritrea</option>
  <option value="Estonia" >Estonia</option>
  <option value="Ethiopia" >Ethiopia</option>
  <option value="Falkland Islands (Malvinas)" >Falkland Islands (Malvinas)</option>

  <option value="Faroe Islands" >Faroe Islands</option>
  <option value="Fiji" >Fiji</option>
  <option value="Finland" >Finland</option>
  <option value="France" >France</option>
  <option value="France" >France</option>
  <option value=" Metropolitan" > Metropolitan</option>

  <option value="French Guiana" >French Guiana</option>
  <option value="French Polynesia" >French Polynesia</option>
  <option value="French Southern Territories" >French Southern Territories</option>
  <option value="Gabon" >Gabon</option>
  <option value="Gambia" >Gambia</option>
  <option value="Georgia" >Georgia</option>

  <option value="Germany" >Germany</option>
  <option value="Ghana" >Ghana</option>
  <option value="Gibraltar" >Gibraltar</option>
  <option value="Greece" >Greece</option>
  <option value="Greenland" >Greenland</option>
  <option value="Grenada" >Grenada</option>

  <option value="Guadeloupe" >Guadeloupe</option>
  <option value="Guam" >Guam</option>
  <option value="Guatemala" >Guatemala</option>
  <option value="Guinea" >Guinea</option>
  <option value="Guinea-Bissau" >Guinea-Bissau</option>
  <option value="Guyana" >Guyana</option>

  <option value="Haiti" >Haiti</option>
  <option value="Heard And Mc Donald Islands" >Heard And Mc Donald Islands</option>
  <option value="Holy See (Vatican City State)" >Holy See (Vatican City State)</option>
  <option value="Honduras" >Honduras</option>
  <option value="Hong Kong" >Hong Kong</option>
  <option value="Hungary" >Hungary</option>

  <option value="Iceland" >Iceland</option>
  <option value="India" >India</option>
  <option value="Indonesia" >Indonesia</option>
  <option value="Iran (Islamic Republic Of)" >Iran (Islamic Republic Of)</option>
  <option value="Iraq" >Iraq</option>
  <option value="Ireland" >Ireland</option>

  <option value="Israel" >Israel</option>
  <option value="Italy" >Italy</option>
  <option value="Jamaica" >Jamaica</option>
  <option value="Japan" >Japan</option>
  <option value="Jordan" >Jordan</option>
  <option value="Kazakhstan" >Kazakhstan</option>

  <option value="Kenya" >Kenya</option>
  <option value="Kiribati" >Kiribati</option>
  <option value="Korea" >Korea</option>
  <option value=" Democratic People&rsquo;s Republic Of" > Democratic People&rsquo;s Republic Of</option>
  <option value="Korea" >Korea</option>
  <option value=" Republic Of" > Republic Of</option>

  <option value="Kuwait" >Kuwait</option>
  <option value="Kyrgyzstan" >Kyrgyzstan</option>
  <option value="Lao People&rsquo;s Democratic Republic" >Lao People&rsquo;s Democratic Republic</option>
  <option value="Latvia" >Latvia</option>
  <option value="Lebanon" >Lebanon</option>
  <option value="Lesotho" >Lesotho</option>

  <option value="Liberia" >Liberia</option>
  <option value="Libyan Arab Jamahiriya" >Libyan Arab Jamahiriya</option>
  <option value="Liechtenstein" >Liechtenstein</option>
  <option value="Lithuania" >Lithuania</option>
  <option value="Luxembourg" >Luxembourg</option>
  <option value="Macau" >Macau</option>

  <option value="Macedonia" >Macedonia</option>
  <option value=" Former Yugoslav Republic Of" > Former Yugoslav Republic Of</option>
  <option value="Madagascar" >Madagascar</option>
  <option value="Malawi" >Malawi</option>
  <option value="Malaysia" >Malaysia</option>
  <option value="Maldives" >Maldives</option>

  <option value="Mali" >Mali</option>
  <option value="Malta" >Malta</option>
  <option value="Marshall Islands" >Marshall Islands</option>
  <option value="Martinique" >Martinique</option>
  <option value="Mauritania" >Mauritania</option>
  <option value="Mauritius" >Mauritius</option>

  <option value="Mayotte" >Mayotte</option>
  <option value="Mexico" >Mexico</option>
  <option value="Micronesia" >Micronesia</option>
  <option value=" Federated States Of" > Federated States Of</option>
  <option value="Moldova" >Moldova</option>
  <option value=" Republic Of" > Republic Of</option>

  <option value="Monaco" >Monaco</option>
  <option value="Mongolia" >Mongolia</option>
  <option value="Montserrat" >Montserrat</option>
  <option value="Morocco" >Morocco</option>
  <option value="Mozambique" >Mozambique</option>
  <option value="Myanmar" >Myanmar</option>

  <option value="Namibia" >Namibia</option>
  <option value="Nauru" >Nauru</option>
  <option value="Nepal" >Nepal</option>
  <option value="Netherlands" >Netherlands</option>
  <option value="Netherlands Antilles" >Netherlands Antilles</option>
  <option value="New Caledonia" >New Caledonia</option>

  <option value="New Zealand" >New Zealand</option>
  <option value="Nicaragua" >Nicaragua</option>
  <option value="Niger" >Niger</option>
  <option value="Nigeria" >Nigeria</option>
  <option value="Niue" >Niue</option>
  <option value="Norfolk Island" >Norfolk Island</option>

  <option value="Northern Mariana Islands" >Northern Mariana Islands</option>
  <option value="Norway" >Norway</option>
  <option value="Oman" >Oman</option>
  <option value="Pakistan" >Pakistan</option>
  <option value="Palau" >Palau</option>
  <option value="Panama" >Panama</option>

  <option value="Papua New Guinea" >Papua New Guinea</option>
  <option value="Paraguay" >Paraguay</option>
  <option value="Peru" >Peru</option>
  <option value="Philippines" >Philippines</option>
  <option value="Pitcairn" >Pitcairn</option>
  <option value="Poland" >Poland</option>

  <option value="Portugal" >Portugal</option>
  <option value="Puerto Rico" >Puerto Rico</option>
  <option value="Qatar" >Qatar</option>
  <option value="Reunion" >Reunion</option>
  <option value="Romania" >Romania</option>
  <option value="Russian Federation" >Russian Federation</option>

  <option value="Rwanda" >Rwanda</option>
  <option value="Saint Kitts And Nevis" >Saint Kitts And Nevis</option>
  <option value="Saint Lucia" >Saint Lucia</option>
  <option value="Saint Vincent And The Grenadines" >Saint Vincent And The Grenadines</option>
  <option value="Samoa" >Samoa</option>
  <option value="San Marino" >San Marino</option>

  <option value="Sao Tome And Principe" >Sao Tome And Principe</option>
  <option value="Saudi Arabia" >Saudi Arabia</option>
  <option value="Senegal" >Senegal</option>
  <option value="Seychelles" >Seychelles</option>
  <option value="Sierra Leone" >Sierra Leone</option>
  <option value="Singapore" >Singapore</option>

  <option value="Slovakia (Slovak Republic)" >Slovakia (Slovak Republic)</option>
  <option value="Slovenia" >Slovenia</option>
  <option value="Solomon Islands" >Solomon Islands</option>
  <option value="Somalia" >Somalia</option>
  <option value="South Africa" >South Africa</option>
  <option value="South Georgia" >South Georgia</option>

  <option value=" South Sandwich Islands" > South Sandwich Islands</option>
  <option value="Spain" >Spain</option>
  <option value="Sri Lanka" >Sri Lanka</option>
  <option value="St. Helena" >St. Helena</option>
  <option value="St. Pierre And Miquelon" >St. Pierre And Miquelon</option>
  <option value="Sudan" >Sudan</option>

  <option value="Suriname" >Suriname</option>
  <option value="Svalbard And Jan Mayen Islands" >Svalbard And Jan Mayen Islands</option>
  <option value="Swaziland" >Swaziland</option>
  <option value="Sweden" >Sweden</option>
  <option value="Switzerland" >Switzerland</option>
  <option value="Syrian Arab Republic" >Syrian Arab Republic</option>

  <option value="Taiwan" >Taiwan</option>
  <option value="Tajikistan" >Tajikistan</option>
  <option value="Tanzania" >Tanzania</option>
  <option value=" United Republic Of" > United Republic Of</option>
  <option value="Thailand" >Thailand</option>
  <option value="Togo" >Togo</option>

  <option value="Tokelau" >Tokelau</option>
  <option value="Tonga" >Tonga</option>
  <option value="Trinidad And Tobago" >Trinidad And Tobago</option>
  <option value="Tunisia" >Tunisia</option>
  <option value="Turkey" >Turkey</option>
  <option value="Turkmenistan" >Turkmenistan</option>

  <option value="Turks And Caicos Islands" >Turks And Caicos Islands</option>
  <option value="Tuvalu" >Tuvalu</option>
  <option value="Uganda" >Uganda</option>
  <option value="Ukraine" >Ukraine</option>
  <option value="United Arab Emirates" >United Arab Emirates</option>
  <option value="United Kingdom" >United Kingdom</option>

  <option value="United States Minor Outlying Islands" >United States Minor Outlying Islands</option>
  <option value="Uruguay" >Uruguay</option>
  <option value="Uzbekistan" >Uzbekistan</option>
  <option value="Vanuatu" >Vanuatu</option>
  <option value="Venezuela" >Venezuela</option>
  <option value="Viet Nam" >Viet Nam</option>

  <option value="Virgin Islands (British)" >Virgin Islands (British)</option>
  <option value="Virgin Islands (U.S.)" >Virgin Islands (U.S.)</option>
  <option value="Wallis And Futuna Islands" >Wallis And Futuna Islands</option>
  <option value="Western Sahara" >Western Sahara</option>
  <option value="Yemen" >Yemen</option>
  <option value="Yugoslavia" >Yugoslavia</option>

  <option value="Zambia" >Zambia</option>
  <option value="Zimbabwe" >Zimbabwe</option>'),
(88, 'statelist', '<option value="Alabama" >Alabama</option>
  <option value="Alaska" >Alaska</option>
  <option value="American Samoa" >American Samoa</option>
  <option value="Arizona" >Arizona</option>
  <option value="Arkansas" >Arkansas</option>
  <option value=" Armed Forces Africa" > Armed Forces Africa</option>
  <option value=" Armed Forces Americas" > Armed Forces Americas</option>
  <option value=" Armed Forces Canada" > Armed Forces Canada</option>
  <option value=" Armed Forces Europe" > Armed Forces Europe</option>
  <option value=" Armed Forces Middle East" > Armed Forces Middle East</option>
  <option value=" Armed Forces Pacific " > Armed Forces Pacific </option>
  <option value="California" >California</option>
  <option value="Colorado" >Colorado</option>
  <option value="Connecticut" >Connecticut</option>
  <option value="Delaware" >Delaware</option>
  <option value="District Of Columbia" >District Of Columbia</option>
  <option value="Federated States Of Micronesia " >Federated States Of Micronesia </option>
  <option value="Florida" >Florida</option>
  <option value="Georgia" >Georgia</option>
  <option value=" Guam" > Guam</option>
  <option value="Hawaii" >Hawaii</option>
  <option value="Idaho" >Idaho</option>
  <option value="Illinois" >Illinois</option>
  <option value="Indiana" >Indiana</option>
  <option value="Iowa" >Iowa</option>
  <option value="Kansas" >Kansas</option>
  <option value="Kentucky" >Kentucky</option>
  <option value="Louisiana" >Louisiana</option>
  <option value="Maine" >Maine</option>
  <option value="Marshall Islands" >Marshall Islands</option>
  <option value="Maryland" >Maryland</option>
  <option value="Massachusetts" >Massachusetts</option>
  <option value="Michigan" >Michigan</option>
  <option value="Minnesota" >Minnesota</option>
  <option value="Mississippi" >Mississippi</option>
  <option value="Missouri" >Missouri</option>
  <option value="Montana" >Montana</option>
  <option value="Nebraska" >Nebraska</option>
  <option value="Nevada" >Nevada</option>
  <option value="New Hampshire" >New Hampshire</option>
  <option value="New Jersey" >New Jersey</option>
  <option value="New Mexico" >New Mexico</option>
  <option value="New York" >New York</option>
  <option value="North Carolina" >North Carolina</option>
  <option value="North Dakota" >North Dakota</option>
  <option value="Northern Mariana Islands" >Northern Mariana Islands</option>
  <option value="Ohio" >Ohio</option>
  <option value="Oklahoma" >Oklahoma</option>
  <option value="Oregon" >Oregon</option>
  <option value="Palau" >Palau</option>
  <option value="Puerto Rico" >Puerto Rico</option>
  <option value="Pennsylvania" >Pennsylvania</option>
  <option value="Rhode Island" >Rhode Island</option>
  <option value="South Carolina" >South Carolina</option>
  <option value="South Dakota" >South Dakota</option>
  <option value="Tennessee" >Tennessee</option>
  <option value="Texas" >Texas</option>
  <option value="Utah" >Utah</option>
  <option value="Vermont" >Vermont</option>
  <option value="Virgin Islands" >Virgin Islands</option>
  <option value="Virginia" >Virginia</option>
  <option value="Washington" >Washington</option>
  <option value="West Virginia" >West Virginia</option>
  <option value="Wisconsin" >Wisconsin</option>
  <option value="Wyoming" >Wyoming</option>'),
  (89, 'disable_remiders', '0'),
  (90, 'show_calendar_registration_flag', '0'),
  (91, 'user_forms', ''),
  (92, 'user_groups', ''),
  (93, 'timezone_offset', '0'),
  (94, 'enable_mandatory_field_note', '0'),
  (95, 'calendar_weekday', '0'),
  (96, 'session_dateformat', 'd M,Y'),
  (97, 'session_timeformat', 'H:i:s'),
  (98, 'listing_button', '1'),
  (99, 'accepted_registration_reports', '0'),
  (100, 'show_poster', '1'),
  (101, 'show_poster_cal', '1');