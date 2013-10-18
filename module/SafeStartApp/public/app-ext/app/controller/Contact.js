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
        this.getViewport().setLoading(true);
        Ext.Ajax.request({
            url: '/api/info/contact',
            params: Ext.encode({data: data}),
            method: 'POST',
            success: function () {
                this.getViewport().setLoading(false);
                Ext.Msg.alert('Message', 'Message send');
            },
            failure: function () {
            },
            scope: this
        });
    }
});
