Ext.define('SafeStartApp.view.pages.toolbar.Main', {

    extend: 'Ext.Toolbar',
    xtype: 'SafeStartMainToolbar',
    requires: [
        'SafeStartApp.view.forms.UserProfile'
    ],

    config: {
        cls: 'sfa-main-toolbar',
        btnTitle: '',
        scrollable: {
            direction: 'horizontal',
            indicators: false            
        }
    },

    initialize: function () {
        this.setItems([{
            xtype: 'container',
            width: 300,
            cls:'sfa-top-logo-bg',
            layout: {
                type: 'hbox',
                align: 'middle'
            },
            items: [{
                xtype: 'image',
                margin: '0 auto',
                height: 45,
                width: 172,
                src: 'resources/img/logo-top.png',
                cls: 'sfa-logo-top',
                listeners: {
                    tap: function () {
                        window.location.href = SafeStartApp.logoRedirectUrl;
                    }
                }
            }]
        }, {
            xtype: 'spacer'
        }, {
            name: 'btn-title',
            ui: 'action',
            text: this.config.btnTitle || ''
        }, {
            xtype: 'spacer'
        }, {
            iconCls: 'user',
            ui: 'action',
            action: 'update_profile',
            text: SafeStartApp.userModel.getFullName()
        }, {
            iconCls: 'action',
            ui: 'action',
            text: 'Logout',
            action: 'logout'
        }]);

        this.callParent();
    },

    applyBtnTitle: function (title) {
        var btn = this.down('button[name=btn-title]');
        if (btn) {
            btn.setText(title);
        }
    }

});