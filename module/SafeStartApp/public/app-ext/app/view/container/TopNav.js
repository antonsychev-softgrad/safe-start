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
        xtype: 'button'
    },

    initComponent: function () {
        var user = SafeStartExt.getApplication().getUserRecord();
        var username = user.get('firstName') + ' ' + user.get('lastName');
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
                ui: 'transparent',
                scale: 'medium',
                name: 'user',
                text: username,
                handler: function () {
                    this.fireEvent('showProfileAction');
                },
                scope: this
            }, {
                xtype: 'button',
                ui: 'transparent',
                scale: 'medium',
                text: 'Logout',
                //iconCls: 'logout',
                handler: function () {
                    this.fireEvent('logoutAction');
                },
                scope: this
            }]
        });
        this.callParent();
    },

    setUsername: function (username) {
        this.down('button[name=user]').setText(username);
    }
});
