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
    ui: 'tabmenu',

    buttons: {
        Auth: {
            text: 'Auth',
            cls: 'sfa-button-auth'
        },
        Companies: {
            text: 'Companies',
            cls: 'sfa-button-companies'
        },
        Company: {
            text: 'Vehicles',
            cls: 'sfa-button-vehicles'
        },
        Alerts: {
            text: 'Alerts',
            cls: 'sfa-button-alerts'
        },
        Users: {
            text: 'Users',
            cls: 'sfa-button-users'
        },
        SystemSettings: {
            text: 'Settings',
            cls: 'sfa-button-system-settings'
        },
        Contact: {
            text: 'Contact',
            cls: 'sfa-button-contact'
        }
    },

    initComponent: function () {
        var me = this;
        Ext.apply(this, {
            defaults: {
                xtype: 'button',
                ui: 'tab',
                scale: 'large',
                width: 60,
                handler: function () {
                    me.fireEvent('showPage', this.componentClass);
                }
            }
        });
        this.callParent();
    },

    applyButtons: function (buttons) {
        this.removeAll();
        Ext.each(buttons, function (button) {
            var config = this.buttons[button];
            if (! config) {
                config = {
                    text: button
                };
            }
            this.add({
                cls: config.cls,
                text: config.text,
                componentClass: button
            });
        }, this);
    },

    setActiveButton: function (name) {
        Ext.each(this.query('button'), function (button) {
            button.removeCls('x-btn-tab-large-pressed');
        });
        this.down('button[componentClass=' + name + ']').addCls('x-btn-tab-large-pressed');
    }

});
