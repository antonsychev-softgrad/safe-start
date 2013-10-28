Ext.define('SafeStartExt.view.container.TopNav', {
    extend: 'Ext.container.Container',
    requires: [
        'Ext.button.Button'
    ],
    
    xtype: 'SafeStartExtContainerTopNav',
    cls: 'sfa-top-nav',

    layout: { 
        type: 'hbox',
        align: 'stretch'
    },

    height: 60,
    padding: '6 10 6 10',
    ui: 'dark',

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
                src: 'resources/img/logo-top.png'
            }, {
                xtype: 'button',
                ui: 'transparent',
                scale: 'medium',
                name: 'companyName'
            }, {
                xtype: 'box',
                flex: 1
            }, {
                xtype: 'button',
                ui: 'transparent',
                scale: 'medium',
                name: 'user',
                cls:'sfa-user',
                text: username,
                handler: function () {
                    this.fireEvent('showProfileAction');
                },
                scope: this
            }, {
                xtype: 'button',
                ui: 'transparent',
                scale: 'medium',
                cls:'sfa-logout',
                text: 'Logout',
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
    },

    setCompanyName: function (companyName) {
        this.down('button[name=companyName]').setText(companyName || 'Company');
    }
});
