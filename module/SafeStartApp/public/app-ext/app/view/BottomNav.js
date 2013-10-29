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

    height: 59,
    ui: 'tabmenu',

    buttons: {
        Auth: {
            text: 'Auth',
            iconCls: 'sfa-icon-auth'
        },
        Companies: {
            text: 'Companies',
            iconCls: 'sfa-icon-companies'
        },
        Company: {
            text: 'Vehicles',
            disabled: true,
            iconCls: 'sfa-icon-vehicles'
        },
        Alerts: {
            text: 'Alerts',
            disabled: true,
            iconCls: 'sfa-icon-alerts'
        },
        Users: {
            text: 'Users',
            disabled: true,
            iconCls: 'sfa-icon-users'
        },
        SystemSettings: {
            text: 'Settings',
            iconCls: 'sfa-icon-settings'
        },
        SystemStatistic: {
            text: 'Statistic',
            iconCls: 'sfa-icon-statistic'
        },
        Contact: {
            text: 'Contact',
            iconCls: 'sfa-icon-contact'
        }
    },

    initComponent: function () {
        var me = this;
        Ext.apply(this, {
            defaults: {
                xtype: 'button',
                ui: 'tab',
                scale: 'large',
                margin: '5 2 3 2',
                enableToggle: true,
                width: 60,
                handler: function () {
                    me.fireEvent('redirectTo', this.componentClass);
                    return false;
                }
            }
        });
        this.callParent();
    },

    enableAll: function () {
        this.items.each(function (button) {
            button.enable();
        });
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
                iconCls: config.iconCls,
                iconAlign: 'top',
                text: config.text,
                disabled: config.disabled,
                componentClass: button
            });
        }, this);
    },

    setActiveButton: function (name) {
        Ext.each(this.query('button'), function (button) {
            button.toggle(false);
            console.log(button);
        });
        this.down('button[componentClass=' + name + ']').toggle(true);
    }

});
