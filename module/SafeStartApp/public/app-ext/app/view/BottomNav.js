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

    height: 56,
    ui: 'dark',

    defaults: {
        xtype: 'button',
        ui: 'tab',
        scale: 'large',
        hidden: true,
        width: 60
    },

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'button',
                text: 'Auth',
                menuItem: 'Auth',
                cls: 'sfa-bottomnav-button-auth',
                handler: function () {
                    this.fireEvent('showPage', 'Auth');
                },
                scope: this
            }, {
                xtype: 'button',
                text: 'Vehicles',
                menuItem: 'Company',
                cls: 'sfa-bottomnav-button-vehicles',
                handler: function () {
                    this.fireEvent('showPage', 'Company');
                },
                scope: this
            }, {
                xtype: 'button',
                text: 'Contact',
                menuItem: 'Contact',
                cls: 'sfa-bottomnav-button-contact',
                handler: function () {
                    this.fireEvent('showPage', 'Contact');
                },
                scope: this
            }]
        });
        this.callParent();
    }
});
