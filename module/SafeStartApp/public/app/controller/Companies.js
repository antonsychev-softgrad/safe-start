Ext.define('SafeStartApp.controller.Companies', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [

    ],

    config: {
        control: {
            nav: {
                tap: 'onCompanySelectAction'
            }
        },

        refs: {
            nav: '#companies'
        }
    },

    onCompanySelectAction: function(component, list, index) {
        var record = list.getStore().getAt(index);
        console.log(record);
    }

});