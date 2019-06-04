<?php
require_once 'config.php';
?>

<html>

<head>
    <title>versadesk</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link href="style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="scripts/jquery.mockjax.js"></script>
    <script type="text/javascript" src="src/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="scripts/countries.js"></script>
    <script type="text/javascript" src="scripts/demo.js"></script>
   
    <script src="https://code.jquery.com/jquery-migrate-1.1.0.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="versadeskformpage">
        <div class="formcontainer">
            <h1> Add an Order </h1>
            <form id="msform" class="commonform">
                <ul id="progressbar" class="numberset d-flex">
                    <li class="active"> Customer Info </li>
                    <li>Items</li>
                    <li>Shipping</li>
                    <li>Finalize</li>
                </ul>
                <fieldset class="stepsform">
                    <legend>
                        <h2 class="form-detail-heading">Customer Information</h2>
                    </legend>

                    <div class="customerInfo whiteBox">
                        <input type="hidden" name="customerId" value="0" id="customerId">
                        <div class="field field-align">

                        </div>
                        <div id="search-customer-form" class="field d-flex">
                            <label for="orderForSearch">Search</label>
                            <div class="field-group">
                                <input type="text" id="orderForSearch" name="orderForSearch" class="field-xlarge" autocomplete="off" placeholder="Search by customer name, email address or company">
                            </div>
                        </div>
                    </div>

                    <legend>
                        <h2 class="form-detail-heading"> Billing Information </h2>
                    </legend>
                    <div class="BillingInfo whiteBox">
                        <div class="f-left">
                            <div class="field formRow d-flex">
                                <label for="FormField_4"><span class="FormFieldLabel">First Name</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="4"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="FirstName">
                                    <input type="text" id="FormField_4" name="FormField[2][4]" value="" aria-required="true" class="Textbox Field200 FormField">
                                </div>
                            </div>
                            <div class="field formRow d-flex">
                                <label for="FormField_4"><span class="FormFieldLabel">Last Name</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="4"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="FirstName">
                                    <input type="text" id="FormField_4" name="FormField[2][41]" value="" aria-required="true" class="Textbox Field200 FormField">
                                </div>
                            </div>
                            <div class="field formRow field-optional d-flex">
                                <label for="FormField_6"><span class="FormFieldLabel">Company Name</span>
                                    <span class="field-label-note">(Optional)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="6"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="CompanyName">
                                    <input type="text" id="FormField_6" name="FormField[2][6]" value="" aria-required="false" class="Textbox Field200 FormField">

                                </div>
                            </div>
                            <div class="field formRow field-optional d-flex">
                                <label for="FormField_7"><span class="FormFieldLabel">Phone Number</span>
                                    <span class="field-label-note">(Optional)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="7"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="Phone">
                                    <input type="text" id="FormField_7" name="FormField[2][7]" value="" aria-required="false" class="Textbox Field200 FormField">
                                </div>
                            </div>
                            <div class="field formRow d-flex">
                                <label for="FormField_8"><span class="FormFieldLabel">Address Line 1</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="8"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="AddressLine1">
                                    <input type="text" id="FormField_8" name="FormField[2][8]" value="" aria-required="true" class="Textbox Field200 FormField">
                                </div>
                            </div>
                            <div class="field formRow field-optional d-flex">
                                <label for="FormField_9"><span class="FormFieldLabel">Address Line 2</span>
                                    <span class="field-label-note">(Optional)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="9"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="AddressLine2">
                                    <input type="text" id="FormField_9" name="FormField[2][9]" value="" aria-required="false" class="Textbox Field200 FormField">
                                </div>
                            </div>
                            <div class="field formRow d-flex">
                                <label for="FormField_10"><span class="FormFieldLabel">Suburb/City</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="10"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="City">
                                    <input type="text" id="FormField_10" name="FormField[2][10]" value="" aria-required="true" class="Textbox Field200 FormField">

                                </div>
                            </div>
                            <div class="field formRow d-flex">
                                <label for="FormField_11"><span class="FormFieldLabel">Country</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldChoosePrefix" value="Choose a Country"><input type="hidden" class="FormFieldId" value="11"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleselect"><input type="hidden" class="FormFieldPrivateId" value="Country">
                                    <select id="FormField_11" name="FormField[2][11]" value="United States" aria-required="true" class="Field200 FormField field-xlarge" style="" size="1">
                                        <option value="">Choose a Country</option>
                                        <option value="Afghanistan">Afghanistan</option>
                                        <option value="Albania">Albania</option>
                                        <option value="Algeria">Algeria</option>
                                        <option value="American Samoa">American Samoa</option>
                                        <option value="Andorra">Andorra</option>
                                        <option value="Angola">Angola</option>
                                        <option value="Anguilla">Anguilla</option>
                                        <option value="Antarctica">Antarctica</option>
                                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                        <option value="Argentina">Argentina</option>
                                        <option value="Armenia">Armenia</option>
                                        <option value="Aruba">Aruba</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Austria">Austria</option>
                                        <option value="Azerbaijan">Azerbaijan</option>
                                        <option value="Bahamas">Bahamas</option>
                                        <option value="Bahrain">Bahrain</option>
                                        <option value="Bangladesh">Bangladesh</option>
                                        <option value="Barbados">Barbados</option>
                                        <option value="Belarus">Belarus</option>
                                        <option value="Belgium">Belgium</option>
                                        <option value="Belize">Belize</option>
                                        <option value="Benin">Benin</option>
                                        <option value="Bermuda">Bermuda</option>
                                        <option value="Bhutan">Bhutan</option>
                                        <option value="Bolivia">Bolivia</option>
                                        <option value="Bonaire, Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
                                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                        <option value="Botswana">Botswana</option>
                                        <option value="Bouvet Island">Bouvet Island</option>
                                        <option value="Brazil">Brazil</option>
                                        <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                        <option value="Brunei Darussalam">Brunei Darussalam</option>
                                        <option value="Bulgaria">Bulgaria</option>
                                        <option value="Burkina Faso">Burkina Faso</option>
                                        <option value="Burundi">Burundi</option>
                                        <option value="Cambodia">Cambodia</option>
                                        <option value="Cameroon">Cameroon</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Cape Verde">Cape Verde</option>
                                        <option value="Cayman Islands">Cayman Islands</option>
                                        <option value="Central African Republic">Central African Republic</option>
                                        <option value="Chad">Chad</option>
                                        <option value="Chile">Chile</option>
                                        <option value="China">China</option>
                                        <option value="Christmas Island">Christmas Island</option>
                                        <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                        <option value="Colombia">Colombia</option>
                                        <option value="Comoros">Comoros</option>
                                        <option value="Congo">Congo</option>
                                        <option value="Congo, the Democratic Republic of the">Congo, the Democratic Republic of the</option>
                                        <option value="Cook Islands">Cook Islands</option>
                                        <option value="Costa Rica">Costa Rica</option>
                                        <option value="Cote d'Ivoire">Cote d'Ivoire</option>
                                        <option value="Croatia">Croatia</option>
                                        <option value="Cyprus">Cyprus</option>
                                        <option value="Czech Republic">Czech Republic</option>
                                        <option value="Denmark">Denmark</option>
                                        <option value="Djibouti">Djibouti</option>
                                        <option value="Dominica">Dominica</option>
                                        <option value="Dominican Republic">Dominican Republic</option>
                                        <option value="Ecuador">Ecuador</option>
                                        <option value="Egypt">Egypt</option>
                                        <option value="El Salvador">El Salvador</option>
                                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                                        <option value="Eritrea">Eritrea</option>
                                        <option value="Estonia">Estonia</option>
                                        <option value="Ethiopia">Ethiopia</option>
                                        <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                        <option value="Faroe Islands">Faroe Islands</option>
                                        <option value="Fiji">Fiji</option>
                                        <option value="Finland">Finland</option>
                                        <option value="France">France</option>
                                        <option value="French Guiana">French Guiana</option>
                                        <option value="French Polynesia">French Polynesia</option>
                                        <option value="French Southern Territories">French Southern Territories</option>
                                        <option value="Gabon">Gabon</option>
                                        <option value="Gambia">Gambia</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Germany">Germany</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="Gibraltar">Gibraltar</option>
                                        <option value="Greece">Greece</option>
                                        <option value="Greenland">Greenland</option>
                                        <option value="Grenada">Grenada</option>
                                        <option value="Guadeloupe">Guadeloupe</option>
                                        <option value="Guam">Guam</option>
                                        <option value="Guatemala">Guatemala</option>
                                        <option value="Guernsey">Guernsey</option>
                                        <option value="Guinea">Guinea</option>
                                        <option value="Guinea-Bissau">Guinea-Bissau</option>
                                        <option value="Guyana">Guyana</option>
                                        <option value="Haiti">Haiti</option>
                                        <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
                                        <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                        <option value="Honduras">Honduras</option>
                                        <option value="Hong Kong">Hong Kong</option>
                                        <option value="Hungary">Hungary</option>
                                        <option value="Iceland">Iceland</option>
                                        <option value="India">India</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Iraq">Iraq</option>
                                        <option value="Ireland">Ireland</option>
                                        <option value="Isle of Man">Isle of Man</option>
                                        <option value="Israel">Israel</option>
                                        <option value="Italy">Italy</option>
                                        <option value="Jamaica">Jamaica</option>
                                        <option value="Japan">Japan</option>
                                        <option value="Jersey">Jersey</option>
                                        <option value="Jordan">Jordan</option>
                                        <option value="Kazakhstan">Kazakhstan</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="Kiribati">Kiribati</option>
                                        <option value="Korea, Republic of">Korea, Republic of</option>
                                        <option value="Kuwait">Kuwait</option>
                                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                                        <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
                                        <option value="Latvia">Latvia</option>
                                        <option value="Lebanon">Lebanon</option>
                                        <option value="Lesotho">Lesotho</option>
                                        <option value="Liberia">Liberia</option>
                                        <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                                        <option value="Liechtenstein">Liechtenstein</option>
                                        <option value="Lithuania">Lithuania</option>
                                        <option value="Luxembourg">Luxembourg</option>
                                        <option value="Macao">Macao</option>
                                        <option value="Macedonia, the Former Yugoslav Republic of">Macedonia, the Former Yugoslav Republic of</option>
                                        <option value="Madagascar">Madagascar</option>
                                        <option value="Malawi">Malawi</option>
                                        <option value="Malaysia">Malaysia</option>
                                        <option value="Maldives">Maldives</option>
                                        <option value="Mali">Mali</option>
                                        <option value="Malta">Malta</option>
                                        <option value="Marshall Islands">Marshall Islands</option>
                                        <option value="Martinique">Martinique</option>
                                        <option value="Mauritania">Mauritania</option>
                                        <option value="Mauritius">Mauritius</option>
                                        <option value="Mayotte">Mayotte</option>
                                        <option value="Mexico">Mexico</option>
                                        <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                                        <option value="Moldova, Republic of">Moldova, Republic of</option>
                                        <option value="Monaco">Monaco</option>
                                        <option value="Mongolia">Mongolia</option>
                                        <option value="Montenegro">Montenegro</option>
                                        <option value="Montserrat">Montserrat</option>
                                        <option value="Morocco">Morocco</option>
                                        <option value="Mozambique">Mozambique</option>
                                        <option value="Myanmar">Myanmar</option>
                                        <option value="Namibia">Namibia</option>
                                        <option value="Nauru">Nauru</option>
                                        <option value="Nepal">Nepal</option>
                                        <option value="Netherlands">Netherlands</option>
                                        <option value="Netherlands Antilles">Netherlands Antilles</option>
                                        <option value="New Caledonia">New Caledonia</option>
                                        <option value="New Zealand">New Zealand</option>
                                        <option value="Nicaragua">Nicaragua</option>
                                        <option value="Niger">Niger</option>
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Niue">Niue</option>
                                        <option value="Norfolk Island">Norfolk Island</option>
                                        <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                        <option value="Norway">Norway</option>
                                        <option value="Oman">Oman</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="Palau">Palau</option>
                                        <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                        <option value="Panama">Panama</option>
                                        <option value="Papua New Guinea">Papua New Guinea</option>
                                        <option value="Paraguay">Paraguay</option>
                                        <option value="Peru">Peru</option>
                                        <option value="Philippines">Philippines</option>
                                        <option value="Pitcairn">Pitcairn</option>
                                        <option value="Poland">Poland</option>
                                        <option value="Portugal">Portugal</option>
                                        <option value="Puerto Rico">Puerto Rico</option>
                                        <option value="Qatar">Qatar</option>
                                        <option value="Republic of Kosovo">Republic of Kosovo</option>
                                        <option value="Reunion">Reunion</option>
                                        <option value="Romania">Romania</option>
                                        <option value="Russian Federation">Russian Federation</option>
                                        <option value="Rwanda">Rwanda</option>
                                        <option value="Saint Helena">Saint Helena</option>
                                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                        <option value="Saint Lucia">Saint Lucia</option>
                                        <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                        <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                        <option value="Samoa">Samoa</option>
                                        <option value="San Marino">San Marino</option>
                                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                        <option value="Saudi Arabia">Saudi Arabia</option>
                                        <option value="Senegal">Senegal</option>
                                        <option value="Serbia">Serbia</option>
                                        <option value="Seychelles">Seychelles</option>
                                        <option value="Sierra Leone">Sierra Leone</option>
                                        <option value="Singapore">Singapore</option>
                                        <option value="Slovakia">Slovakia</option>
                                        <option value="Slovenia">Slovenia</option>
                                        <option value="Solomon Islands">Solomon Islands</option>
                                        <option value="Somalia">Somalia</option>
                                        <option value="South Africa">South Africa</option>
                                        <option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
                                        <option value="Spain">Spain</option>
                                        <option value="Sri Lanka">Sri Lanka</option>
                                        <option value="Sudan">Sudan</option>
                                        <option value="Suriname">Suriname</option>
                                        <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                        <option value="Swaziland">Swaziland</option>
                                        <option value="Sweden">Sweden</option>
                                        <option value="Switzerland">Switzerland</option>
                                        <option value="Taiwan">Taiwan</option>
                                        <option value="Tajikistan">Tajikistan</option>
                                        <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                                        <option value="Thailand">Thailand</option>
                                        <option value="Timor-Leste">Timor-Leste</option>
                                        <option value="Togo">Togo</option>
                                        <option value="Tokelau">Tokelau</option>
                                        <option value="Tonga">Tonga</option>
                                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                        <option value="Tunisia">Tunisia</option>
                                        <option value="Turkey">Turkey</option>
                                        <option value="Turkmenistan">Turkmenistan</option>
                                        <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                        <option value="Tuvalu">Tuvalu</option>
                                        <option value="Uganda">Uganda</option>
                                        <option value="Ukraine">Ukraine</option>
                                        <option value="United Arab Emirates">United Arab Emirates</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="United States" selected="selected">United States</option>
                                        <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                        <option value="Uruguay">Uruguay</option>
                                        <option value="Uzbekistan">Uzbekistan</option>
                                        <option value="Vanuatu">Vanuatu</option>
                                        <option value="Venezuela">Venezuela</option>
                                        <option value="Viet Nam">Viet Nam</option>
                                        <option value="Virgin Islands, British">Virgin Islands, British</option>
                                        <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                                        <option value="Wallis and Futuna">Wallis and Futuna</option>
                                        <option value="Western Sahara">Western Sahara</option>
                                        <option value="Yemen">Yemen</option>
                                        <option value="Zambia">Zambia</option>
                                        <option value="Zimbabwe">Zimbabwe</option>

                                    </select>

                                </div>
                            </div>
                            <div class="field formRow d-flex">
                                <label for="FormField_12"><span class="FormFieldLabel">State/Province</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldChoosePrefix" value="Choose a State"><input type="hidden" class="FormFieldId" value="12"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="selectortext"><input type="hidden" class="FormFieldPrivateId" value="State">
                                    <noscript>
                                        <input type="text" name="FormField[2][12]" value="" class="Field200" style="" />
                                    </noscript>
                                    <select name="FormField[2][12]" id="FormField_12" aria-required="true" class="FormField JSHidden Field200 field-xlarge" style="">
                                        <option value="">Choose a State</option>
                                        <option value="Alabama">Alabama</option>
                                        <option value="Alaska">Alaska</option>
                                        <option value="American Samoa">American Samoa</option>
                                        <option value="Arizona">Arizona</option>
                                        <option value="Arkansas">Arkansas</option>
                                        <option value="Armed Forces Africa">Armed Forces Africa</option>
                                        <option value="Armed Forces Americas">Armed Forces Americas</option>
                                        <option value="Armed Forces Canada">Armed Forces Canada</option>
                                        <option value="Armed Forces Europe">Armed Forces Europe</option>
                                        <option value="Armed Forces Middle East">Armed Forces Middle East</option>
                                        <option value="Armed Forces Pacific">Armed Forces Pacific</option>
                                        <option value="California">California</option>
                                        <option value="Colorado">Colorado</option>
                                        <option value="Connecticut">Connecticut</option>
                                        <option value="Delaware">Delaware</option>
                                        <option value="District of Columbia">District of Columbia</option>
                                        <option value="Federated States Of Micronesia">Federated States Of Micronesia</option>
                                        <option value="Florida">Florida</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Guam">Guam</option>
                                        <option value="Hawaii">Hawaii</option>
                                        <option value="Idaho">Idaho</option>
                                        <option value="Illinois">Illinois</option>
                                        <option value="Indiana">Indiana</option>
                                        <option value="Iowa">Iowa</option>
                                        <option value="Kansas">Kansas</option>
                                        <option value="Kentucky">Kentucky</option>
                                        <option value="Louisiana">Louisiana</option>
                                        <option value="Maine">Maine</option>
                                        <option value="Marshall Islands">Marshall Islands</option>
                                        <option value="Maryland">Maryland</option>
                                        <option value="Massachusetts">Massachusetts</option>
                                        <option value="Michigan">Michigan</option>
                                        <option value="Minnesota">Minnesota</option>
                                        <option value="Mississippi">Mississippi</option>
                                        <option value="Missouri">Missouri</option>
                                        <option value="Montana">Montana</option>
                                        <option value="Nebraska">Nebraska</option>
                                        <option value="Nevada">Nevada</option>
                                        <option value="New Hampshire">New Hampshire</option>
                                        <option value="New Jersey">New Jersey</option>
                                        <option value="New Mexico">New Mexico</option>
                                        <option value="New York">New York</option>
                                        <option value="North Carolina">North Carolina</option>
                                        <option value="North Dakota">North Dakota</option>
                                        <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                        <option value="Ohio">Ohio</option>
                                        <option value="Oklahoma">Oklahoma</option>
                                        <option value="Oregon">Oregon</option>
                                        <option value="Palau">Palau</option>
                                        <option value="Pennsylvania">Pennsylvania</option>
                                        <option value="Puerto Rico">Puerto Rico</option>
                                        <option value="Rhode Island">Rhode Island</option>
                                        <option value="South Carolina">South Carolina</option>
                                        <option value="South Dakota">South Dakota</option>
                                        <option value="Tennessee">Tennessee</option>
                                        <option value="Texas">Texas</option>
                                        <option value="Utah">Utah</option>
                                        <option value="Vermont">Vermont</option>
                                        <option value="Virgin Islands">Virgin Islands</option>
                                        <option value="Virginia">Virginia</option>
                                        <option value="Washington">Washington</option>
                                        <option value="West Virginia">West Virginia</option>
                                        <option value="Wisconsin">Wisconsin</option>
                                        <option value="Wyoming">Wyoming</option>
                                    </select>


                                </div>
                            </div>
                            <div class="field formRow d-flex">
                                <label for="FormField_13"><span class="FormFieldLabel">Zip/Postcode</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="13"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="Zip">
                                    <input type="text" id="FormField_13" name="FormField[2][13]" value="" aria-required="true" class="Textbox Field45 FormField" style="width:40px;">


                                </div>
                            </div>
                            <div class="field formRow d-flex">
                                <label for="FormField_25"><span class="FormFieldLabel">What industry are you in?</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldChoosePrefix" value="Select"><input type="hidden" class="FormFieldId" value="25"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleselect"><input type="hidden" class="FormFieldPrivateId" value="">
                                    <select id="FormField_25" name="FormField[2][25]" value="" aria-required="true" class="Field200 FormField field-xlarge" style="" size="1">
                                        <option value="">Select</option>
                                        <option value="Advertising">Advertising</option>
                                        <option value="Architecture ">Architecture </option>
                                        <option value="Education">Education</option>
                                        <option value="Financial">Financial</option>
                                        <option value="Food Service">Food Service</option>
                                        <option value="Government">Government</option>
                                        <option value="Healthcare">Healthcare</option>
                                        <option value="Insurance">Insurance</option>
                                        <option value="Legal">Legal</option>
                                        <option value="Manufacturing">Manufacturing</option>
                                        <option value="Non-Profit">Non-Profit</option>
                                        <option value="Other">Other</option>
                                        <option value="Professional Services">Professional Services</option>
                                        <option value="Real Estate">Real Estate</option>
                                        <option value="Retail">Retail</option>
                                        <option value="Transport">Transport</option>

                                    </select>

                                </div>
                            </div>
                            <div class="field formRow d-flex">
                                <label for="FormField_27"><span class="FormFieldLabel">How did you hear about us?</span>
                                    <span class="hide-visually">(Required)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldChoosePrefix" value="Select"><input type="hidden" class="FormFieldId" value="27"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleselect"><input type="hidden" class="FormFieldPrivateId" value="">
                                    <select id="FormField_27" name="FormField[2][27]" value="" aria-required="true" class="Field200 FormField field-xlarge" style="" size="1">
                                        <option value="">Select</option>
                                        <option value="Google or other search">Google or other search</option>
                                        <option value="Word of mouth">Word of mouth</option>
                                        <option value="Press">Press</option>
                                        <option value="Advertisement">Advertisement</option>
                                        <option value="Video">Video</option>
                                        <option value="Article or blog post">Article or blog post</option>
                                        <option value="Social media">Social media</option>
                                        <option value="Other">Other</option>
                                        <option value="Return Customer">Return Customer</option>

                                    </select>


                                </div>
                            </div>
                            <div class="field formRow field-optional d-flex">
                                <label for="FormField_30"><span class="FormFieldLabel">Sales Rep.</span>
                                    <span class="field-label-note">(Optional)</span>
                                </label>
                                <div class="field-group value">
                                    <input type="hidden" class="FormFieldId" value="30"><input type="hidden" class="FormFieldFormId" value="2"><input type="hidden" class="FormFieldType" value="singleline"><input type="hidden" class="FormFieldPrivateId" value="">
                                    <input type="text" id="FormField_30" name="FormField[2][30]" value="" aria-required="false" class="Textbox Field200 FormField">


                                </div>
                            </div>
                        </div>


                        <div class="field">
                            <div class="field-group">

                            </div>
                        </div>
                    </div>
                    <input type="button" name="next" class="next action-button" value="Next" />
                </fieldset>


                <fieldset class="stepsform">
                    <h2 class="fs-title">Add Products</h2>
                    <div style="position: relative; height: 80px;">
                       <input type="text" name="country" id="autocomplete-ajax" style="position: absolute; z-index: 2; background: transparent;"/>          
                   </div>
                  <div id="selction-ajax"></div>
       
   
                    
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog">                        
                        <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Options</h4>
                                </div>
                                <div class="modal-body">
                                <p></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-default" id= "add_item" >Add Item</button>
                                </div>
                            </div>
                        
                        </div>
                    </div>
 
                    <div class = "selected_product"></div>
                    <input type="button" name="next" class="next action-button" value="Next">
                    <input type="button" name="previous" class="previous action-button" value="Previous">
                    <script type="text/javascript">
                        var pdctarray = <?php echo json_encode($maparray); ?>;
                    
                </script>

                </fieldset>

                <fieldset class="stepsform" >
                    <h2 class="fs-title">Comment and Notes</h2>
                    <h3 class="fs-subtitle"></h3>
                    <input type="text" name="Comments" placeholder="Comments" /></br>
                    <input type="text" name="Staff Notes" placeholder="Notes" /></br>
                    <div> </div>
                   
                    <input type="button" name="next" class="next action-button" value="Next" />
                    <input type="button" name="previous" class="previous action-button" value="Previous" />

                </fieldset>

                <fieldset class="stepsform">
                    <h2 class="fs-title">Finalize</h2>
                    <h3 class="fs-subtitle"></h3>

                    <div class="block1 p-15">

                        <div class="order-page">

                            <div class="main-orderbar leftside">

                                <h2 class="main-heading">Customer Billing Details</h2>

                                <div class="order-panel-body">

                                    <div class="order-details-box">

                                        <h3 class="order-details-heading">Billing To:</h3> <a href="javascript:;" class="action-change">Change</a>

                                        <dl class="dl-horizontal-box">

                                            <dt>Name</dt>

						<dd class="FormField24"></dd>

						<dt>Company</dt>

						<dd class="FormField26"></dd>

						<dt>Phone</dt>

						<dd class="FormField27"></dd>

						<dt>Address</dt>

						<dd class="FormField28"></dd>

						<dt>Suburb/City</dt>

						<dd class="FormField210"></dd>

						<dt>State/Province</dt>

						<dd class="FormField212"></dd>

						<dt>Country</dt>

						<dd class="FormField211"></dd>

						<dt>ZIP/Postcode</dt>

						<dd class="FormField213"></dd>

                                        </dl>

                                    </div>

                                </div>



                                <h2 class="main-heading">Customer Shipping Details</h2>

                                <div class="order-panel-body">

                                    <div class="order-details-box">

                                        <h3 class="order-details-heading">Shipping To: </h3> <a href="javascript:;" class="action-change">Change</a>

                                        <dl class="dl-horizontal-box">

                                            <dt>Name</dt>

                                            	<dd class="FormField24"></dd>

						<dt>Company</dt>

						<dd class="FormField26"></dd>

						<dt>Phone</dt>

						<dd class="FormField27"></dd>

						<dt>Address</dt>

						<dd class="FormField28"></dd>

						<dt>Suburb/City</dt>

						<dd class="FormField210"></dd>

						<dt>State/Province</dt>

						<dd class="FormField212"></dd>

						<dt>Country</dt>

						<dd class="FormField211"></dd>

						<dt>ZIP/Postcode</dt>

						<dd class="FormField213"></dd>

                                        </dl>

                                    </div>

                                    <div class="order-details-box">

                                        <h3 class="order-details-heading">Shipping Method:</h3> <a href="#" class="action-change">Change</a>

                                        <span>None: $0.00</span>

                                    </div>

                                    <div class="order-details-box">

                                        <table class="order-table">

                                            <thead>

                                                <tr>

                                                    <th scope="col">

                                                        Products

                                                    </th>

                                                    <th scope="col">

                                                        Products shipped to test, test, test, 125254, Tokelau

                                                    </th>

                                                    <th scope="col">Quantity</th>

                                                    <th scope="col">Item Price</th>

                                                    <th scope="col">Item Total</th>

                                                </tr>

                                            </thead>

                                            <tbody>

                                                <tr>

                                                    <td>

                                                        <img src="https://betterhealthalaska.com/wp-content/uploads/2018/10/Better-Health-Chiropractic-in-Anchorage.png" alt="" />

                                                    </td>

                                                    <td>

                                                        <div>

                                                            <strong>Universal Single LCD Spider Monitor Arm</strong>

                                                        </div>

                                                        <div class="number-tab">VT6070000-00-01</div>

                                                        <div class="colorbox">

                                                            <div class="bankinfo">

                                                                <div class="order-option">Color:</div>

                                                                <div class="value">Industrial Black</div>

                                                            </div>

                                                        </div>

                                                    </td>

                                                    <td>

                                                        1

                                                    </td>

                                                    <td>

                                                        $98.10

                                                    </td>

                                                    <td>

                                                        <span>$98.10</span>

                                                    </td>

                                                </tr>

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                                <h2 class="main-heading">Comments and Notes</h2>

                                <div class="order-panel-body">

                                    <div class="order-details-box">

                                        <div class="form-group">

                                            <label>Comments</label>

                                            <textarea rows="5" class="form-control" placeholder="Comments"></textarea>

                                        </div>

                                        <div class="form-group">

                                            <label>Staff Notes </label>

                                            <textarea rows="5" class="form-control" placeholder="Staff Notes"></textarea>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="main-orderbar rightside">
                                 <h2 class="main-heading">Finalize</h2>
                                 <div class="field formRow field-optional d-flex">
                                     
                               <div class="full_form">
                                <label class="left_form"><span class="FormFieldLabel">payment</label>
                                <div class="right_form">
                                    <Select  id="Card" name="Card" aria-required="false" class="Textbox Field200 FormField">
                                        <option value="stripe">Stripe</option>
                                        </select>
                                </div>
                                 </div>
                        
                                 <div class="full_form">
                                <label class="left_form"><span class="FormFieldLabel">Cardholder's Name:</label>
                                <div class="right_form">
                                    <input type="text" id="Card" name="Card" value="" aria-required="false" class="Textbox Field200 FormField">
                                </div>
                                 </div>
                                 
                                                               <div class="full_form">
                                <label class="left_form"><span class="FormFieldLabel">Credit Card No:</label>
                                <div class="right_form">
                                    <input type="text" id="Card" name="Card" value="" aria-required="false" class="Textbox Field200 FormField">
                                </div>
                                 </div>
                                                                <div class="full_form">
                                <label class="left_form"><span class="FormFieldLabel">CCV2 Value:</label>
                                <div class="right_form">
                                    <input type="text" id="Card" name="Card" value="" aria-required="false" class="Textbox Field200 FormField">
                                </div>
                                 </div>
                                 
                                 <h2>Expiration Date:</h2>
                                 
                                                                <div class="full_form">
                                <label class="left_form"><span class="FormFieldLabel">Month</label>
                                <div class="right_form">
                                    <input type="text" id="Card" name="Card" value="" aria-required="false" class="Textbox Field200 FormField">
                                </div>
                                 </div>
                                 
                                 
                                                                <div class="full_form">
                                <label class="left_form"><span class="FormFieldLabel">year</label>
                                <div class="right_form">
                                    <input type="text" id="Card" name="Card" value="" aria-required="false" class="Textbox Field200 FormField">
                                </div>
                                 </div>
                                     <input type="submit" name="submit" class="submit action-button" value="Submit" />
                    
                            </div>
                            </div>
                    <!--input type="submit" name="submit" class="submit action-button" value="Submit" />
                    <input type="button" name="previous" class="previous action-button" value="Previous" /-->
                </fieldset>
            </form>
        </div>
    </div> 
    <!-- jQuery -->
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> -->
    <!-- jQuery easing plugin -->
    <script src="js/jquery.easing.min.js" type="text/javascript"></script>
    <script>
        $(function() {

            //jQuery time
            var current_fs, next_fs, previous_fs; //fieldsets
            var left, opacity, scale; //fieldset properties which we will animate
            var animating; //flag to prevent quick multi-click glitches

            $(".next").click(function() {
                var x=$('form').serializeArray();
	console.log(x);
	/*$.each(x, function(i, field){
      $("#FormField[2][4]").append(field.value);
    });*/
	var x1=$('input[name="FormField[2][4]"]').val()+" "+$('input[name="FormField[2][41]"]').val();
	$(".FormField24").text(x1);
	var x2=$('input[name="FormField[2][6]"]').val();
	$(".FormField26").text(x2);
	var x3=$('input[name="FormField[2][7]"]').val();
	$(".FormField27").text(x3);
	var x4=$('input[name="FormField[2][8]"]').val()+"<br>"+$('input[name="FormField[2][9]"]').val();
	$(".FormField28").html(x4);
	var x5=$('input[name="FormField[2][10]"]').val();
	$(".FormField210").text(x5);
	var x6=$('input[name="FormField[2][13]"]').val();
	$(".FormField213").text(x6);
	var x7=$('#FormField_12').val();
	$(".FormField212").text(x7);
	var x8=$('#FormField_11').val();
	$(".FormField211").text(x8);
                if (animating) return false;
                animating = true;

                current_fs = $(this).parent();
                next_fs = $(this).parent().next();

                //activate next step on progressbar using the index of next_fs
                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

                //show the next fieldset
                next_fs.show();
                //hide the current fieldset with style
                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now, mx) {
                        //as the opacity of current_fs reduces to 0 - stored in "now"
                        //1. scale current_fs down to 80%
                        scale = 1 - (1 - now) * 0.2;
                        //2. bring next_fs from the right(50%)
                        left = (now * 50) + "%";
                        //3. increase opacity of next_fs to 1 as it moves in
                        opacity = 1 - now;
                        current_fs.css({
                            'transform': 'scale(' + scale + ')'
                        });
                        next_fs.css({
                            'left': left,
                            'opacity': opacity
                        });
                    },
                    duration: 800,
                    complete: function() {
                        current_fs.hide();
                        animating = false;
                    },
                    //this comes from the custom easing plugin
                    easing: 'easeInOutBack'
                });
            });

            $(".previous").click(function() {
                if (animating) return false;
                animating = true;

                current_fs = $(this).parent();
                previous_fs = $(this).parent().prev();

                //de-activate current step on progressbar
                $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

                //show the previous fieldset
                previous_fs.show();
                //hide the current fieldset with style
                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now, mx) {
                        //as the opacity of current_fs reduces to 0 - stored in "now"
                        //1. scale previous_fs from 80% to 100%
                        scale = 0.8 + (1 - now) * 0.2;
                        //2. take current_fs to the right(50%) - from 0%
                        left = ((1 - now) * 50) + "%";
                        //3. increase opacity of previous_fs to 1 as it moves in
                        opacity = 1 - now;
                        current_fs.css({
                            'left': left
                        });
                        previous_fs.css({
                            'transform': 'scale(' + scale + ')',
                            'opacity': opacity
                        });
                    },
                    duration: 800,
                    complete: function() {
                        current_fs.hide();
                        animating = false;
                    },
                    //this comes from the custom easing plugin
                    easing: 'easeInOutBack'
                });
            });

            $(".submit").click(function() {
                return false;
            })

        });

    </script>
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-36251023-1']);
        _gaq.push(['_setDomainName', 'jqueryscript.net']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'https://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();

    </script>
</body>

</html>
