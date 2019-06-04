/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
/*global byteConvert*/
define([
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'jquery/file-uploader'
], function ($, mageTemplate, alert) {
    'use strict';

    $.widget('mage.downloadsUploader', {

        options: {
        },

        /**
         *
         * @private
         */
        _create: function () {
            var
                self = this,
                progressTmpl = mageTemplate('[data-template="uploader"]'),
                inputTmpl = mageTemplate('[data-template="inputter"]');

            this.element.parent().parent().css('float', 'none').css('width', '100%');

            this.element.find('input[type=file]').fileupload({
                dataType: 'json',
                formData: {
                    'form_key': window.FORM_KEY
                },
                dropZone: '[data-tab-panel=image-management]',
                sequentialUploads: true,
                //acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */
                add: function (e, data) {
                    /*
                     One special callback is the add callback, as it provides a submit method for the data argument, that will start the file upload
                     */
                    var
                        fileSize,
                        tmpl,
                        filesCanBeUploaded = true;

                    $('table#files-to-upload').show();
                    $('.total-progressbar-container .total-progressbar').css('width', '0');

                    $.each(data.files, function (index, file) {
                        fileSize = typeof file.size == 'undefined' ?
                            $.mage.__('We could not detect a size.') :
                            byteConvert(file.size);

                        data.fileId = Math.random().toString(33).substr(2, 18);

                        tmpl = progressTmpl({
                            data: {
                                name: file.name,
                                size: fileSize,
                                id: data.fileId
                            }
                        });

                        $(tmpl).appendTo(self.element.find("table#files-to-upload tbody"));

                        if (file.size > self.options.maxFileSize) {
                            filesCanBeUploaded = false;
                            self.showError(data.fileId, $.mage.__("This file is too big. Maximum allowed size is") + " " + (self.options.maxFileSize / 1024) + "K");
                        }
                    });

                    if (filesCanBeUploaded) {
                        $(this).fileupload('process', data).done(function () {
                            data.submit();
                        });
                    }
                },

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */
                done: function (e, data) {
                    var tmpl;

                    if (data.result && !data.result.error) {
                        self.element.trigger('addItem', data.result);

                        tmpl = inputTmpl({
                            data: {
                                realname: data.files[0].name,
                                name: data.result.file,
                                id: data.fileId
                            }
                        });

                        $(tmpl).appendTo(self.element);

                        self.showSuccess(data.fileId);
                    } else {
                        self.showError(data.fileId, data.result.error);
                        /*
                        alert({
                            content: $.mage.__('We don\'t recognize or support this file extension type.')
                        });
                        */
                    }
                },

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */

                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10),
                        progressSelector = '#' + data.fileId + ' .progressbar-container .progressbar';

                    self.element.find(progressSelector).css('width', progress + '%');
                },

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */

                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    //$('.total-progressbar-container').css('border-width', '1px').css('border-style', 'solid').css('border-color', '#CCCCCC');
                    $('.total-progressbar-container .total-progressbar').css('width', progress + '%');
                    $('.total-upload-progress').css('visibility', 'visible');
                    if (progress > 99) {
                        setTimeout(
                            function () {
                                $('.total-upload-progress').css('visibility', 'hidden');
                            },
                            1000
                        );
                    }
                },

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */
                fail: function (e, data) {
                    var progressSelector = '#' + data.fileId;

                    self.element.find(progressSelector).removeClass('upload-progress').addClass('upload-failure')
                        .delay(2000)
                        .hide('highlight')
                        .remove();
                }
            });

        },
        /**
         * @param {String} fileId
         */
        showSuccess: function (fileId) {
            $("#" + fileId + " .progressbar-container").hide();

            var removeBtn = $("#" + fileId + " .buton.btn-remove");
            removeBtn.css("visibility", "visible");
            removeBtn.click(function () {
                var fileId = $(this).parent().parent().attr("id");
                $("#hidden-input-" + fileId).remove();   // Remove hidden input field.
                $("#" + fileId).remove();   // Remove table row.
                if (0 == $("table#files-to-upload tbody > tr").size()) {
                    $("table#files-to-upload").hide();
                }
            });
            $("#" + fileId + " span.sign-icon.admin-icon-ok").show();
        },

        /**
         * @param {String} fileId
         * @param {String} errorMessage
         */
        showError: function (fileId, errorMessage) {
            $("#" + fileId + " .progressbar-container").hide();

            $("#" + fileId + " span.sign-icon.admin-icon-error").show();
            if (errorMessage) {
                $("#" + fileId + " .buttons-or-info").text(errorMessage);
            }
        }

    });

    return $.mage.downloadsUploader;
});
