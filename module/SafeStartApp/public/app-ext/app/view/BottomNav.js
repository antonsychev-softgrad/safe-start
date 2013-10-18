Ext.define('SafeStartExt.view.BottomNav', {
    extend: 'Ext.container.Container',
    requires: [
        'Ext.button.Button'
    ],
    
    alias: 'widget.SafeStartExtBottomNav',
    cls: 'sfa-bottomnav',

    layout: { 
        type: 'hbox',
        pack: 'center',
        align: 'stretch'
    },

    height: 50,
    ui: 'dark',

    defaults: {
        xtype: 'button',
        width: 60
    },

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'button',
                text: 'Auth',
                cls: 'sfa-bottomnav-button-auth',
                handler: function () {
                    this.fireEvent('showAuth');
                },
                scope: this
            }, {
                xtype: 'button',
                hidden: true,
                text: 'Vehicles',
                cls: 'sfa-bottomnav-button-vehicles',
                handler: function () {
                    this.fireEvent('showVehicles');
                },
                scope: this
            }, {
                xtype: 'button',
                text: 'Contact',
                cls: 'sfa-bottomnav-button-contact',
                handler: function () {
                    this.fireEvent('showContact');
                },
                scope: this
            }]
        });
        this.callParent();
    }
});
