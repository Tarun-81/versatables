define([
    'ko',
    'Magento_Ui/js/modal/alert',
    'underscore',
    'jquery',
    'uiComponent',
    'mage/url',
], function (ko, alert ,_,$, Component, urlBuilder) {
    'use strict';
    return Component.extend({
        availableTypes : ko.observableArray(window.mapping.Types),
        selectedType : ko.observable(),
        magentoFields : ko.observableArray([]),
        zohocrmFields : ko.observableArray([]),
        mappingResult : ko.observableArray([]),
        selectedField : ko.observable(),
        mappedField : ko.observableArray([]),
        saveMappingUrl : window.mapping.SaveMappingUrl,
        getMappingFieldUrl : window.mapping.GetMappingFieldUrl,
        defaults: {
            template: 'Magenest_ZohocrmIntegration/mapping',
            isTypeSelected : false,
        },

        initObservable: function () {
            this._super()
                .observe(['isTypeSelected']);
            return this;
        },

        getOptionValue : function () {
            var self = this;
            var type = self.selectedType();
            if(type != undefined) {
                self.isTypeSelected(true);
                var url = self.getMappingFieldUrl + '?type='+type;
                $.ajax({
                    type: "POST",
                    url: url,
                    beforeSend : function() {
                        $('body').trigger('processStart');
                    },
                    success: function (response) {
                        var responseObj = JSON.parse(response);
                        self.zohocrmFields([]);
                        self.magentoFields([]);
                        self.mappedField([]);
                        self.mappingResult([]);
                        self.selectedField();

                        description : ko.observable();
                        status = ko.observable(1);

                        var observableZohocrmFields = ko.observableArray([]);
                        var zohocrmFields = responseObj.zoho_fields;
                        for ( var skey in zohocrmFields) {
                            if(zohocrmFields.hasOwnProperty(skey)) {
                                observableZohocrmFields.push({key : zohocrmFields[skey], value : skey });
                            }
                        }

                        var mappedField = responseObj.mapped;
                        var magentoFields = responseObj.magento_fields;

                        for ( var mkey in magentoFields) {
                            if(magentoFields.hasOwnProperty(mkey)) {
                                var field = "";
                                var desc = "";
                                var stt = 1;
                                for (var i in mappedField) {
                                    if (mkey == mappedField[i]['magento_field']) {
                                        field = mappedField[i]['zoho_field'];
                                        desc = mappedField[i]['description'];
                                        stt = mappedField[i]['status'];
                                        break;
                                    }
                                }
                                self.magentoFields.push({
                                    key : magentoFields[mkey],
                                    value : mkey,
                                    zohocrmFields: observableZohocrmFields,
                                    selectedField: field,
                                    description: desc,
                                    status: stt
                                });
                            }
                        }

                        $('body').trigger('processStop');
                    },
                    fail: function(response) {
                        console.log(response);
                        $('body').trigger('processStop');
                    }
                });
            } else {
                self.isTypeSelected(false);
            }

        },

        saveMapping : function () {
            var self = this;
            self.mappingResult([]);
            $('.admin__table-secondary > tbody > tr').each(function () {
                var cells = $(this).find("td");
                var select = $(this).find('#select_zohocrm');
                var status = $(this).find('#select_status');
                var description = $(this).find("input");
                if(select.val()) {
                    self.mappingResult.push({key : cells.val(), value : select.val(),description : description.val(),status : status.val()});
                }

            });
            if(self.mappingResult().length) {
                $.ajax({
                    type: "POST",
                    url: self.saveMappingUrl,
                    data: {
                        type: self.selectedType(),
                        result: self.mappingResult(),
                        form_key: FORM_KEY
                    },
                    beforeSend: function () {
                        $('body').trigger('processStart');
                    },
                    success: function (response) {
                        if (response) {
                            alert({
                                title : 'Mapping data have been saved'
                            });
                            $('body').trigger('processStop');
                        }

                    },
                    error: function (response) {
                        console.log(response);
                        $('body').trigger('processStop');
                    }
                });
            } else {
                alert({
                    title: 'No field mapping has been selected.'
                });
                $('body').trigger('processStop');
            }
        }
    })
});
