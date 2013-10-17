Ext.define('SafeStartExt.view.Viewport', {
    extend: 'Ext.container.Viewport',
    requires: [
        'Ext.layout.container.Fit',
        'SafeStartExt.view.Main'
    ],

    layout: {
        type: 'fit'
    },

    items: [{
        xtype: 'SafeStartExtMain'
    }]
});
