Ext.define('SafeStartExt.view.container.TopNav', {
    extend: 'Ext.container.Container',
    requires: [
        'Ext.button.Button'
    ],
    
    alias: 'widget.SafeStartExtContainerTopNav',
    cls: 'sfa-topnav',

    layout: { 
        type: 'hbox',
        align: 'stretch'
    },

    height: 60,
    padding: '6 10 6 10',

    defaults: {
        xtype: 'button',
        width: 60
    },

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'image',
                width: 172,
                height: 45,
                src: '/resources/img/logo-top.png'
            }, {
                xtype: 'box',
                flex: 1
            }, {
                xtype: 'button',
                text: 'Logout',
                handler: function () {
                    this.fireEvent('logoutAction');
                },
                scope: this
            }]
        });
        this.callParent();
    }
});
