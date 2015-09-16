Ext.define('SafeStartExt.controller.Contact', {
    extend: 'Ext.app.Controller',

    refs: [{
        selector: 'viewport',
        ref: 'viewport'            
    }],

    init: function () {
        this.control({
            'SafeStartExtComponentContact': {
                contactAction: this.contactAction
            } 
        });
    },

    contactAction: function (data) {
        SafeStartExt.Ajax.request({
            url: 'info/contact',
            data: data,
            success: function () {
                Ext.Msg.alert('Message', 'Message send');
            }
        });
    }
});
