Ext.define('SafeStartExt.view.Viewport', {
    extend: 'Ext.container.Viewport',
    requires: [
        'Ext.layout.container.Border',
        'SafeStartExt.view.Main',
        'SafeStartExt.view.BottomNav'
    ],

    layout: {
        type: 'border'
    },

    items: [{
        xtype: 'SafeStartExtMain',
        region: 'center'
    }, {
        xtype: 'SafeStartExtBottomNav',
        region: 'south'
    }]
});
