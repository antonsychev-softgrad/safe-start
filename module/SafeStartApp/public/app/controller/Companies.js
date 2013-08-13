Ext.define('SafeStartApp.controller.Companies', {
    extend: 'Ext.app.Controller',
    mixins: ['SafeStartApp.controller.mixins.Form'],

    requires: [

    ],

    init: function(){

    },

    config: {
        control: {
            navMain: {
                itemsingletap: 'onCompanySelectAction'
            }
        },

        refs: {
            navMain: 'SafeStartCompaniesPage > list[name=companies]'
        }
    },


    onCompanySelectAction: function(element, index, target, record, e, eOpts) {
        var record = element.getStore().getAt(index);
        console.log(record);
    }

});