define(['jquery'], function(jQuery){
    return function() {
        jQuery.widget(
            'mage.calendar',
            jQuery['mage']['calendar'],
            {
                _create: function () {
                    this.dateTimeFormat.date.yy = 'y';
                    this.dateTimeFormat.date.M = 'm';
                    this._super();
                }
            }
        );

        return jQuery['mage']['calendar'];
    };
});
