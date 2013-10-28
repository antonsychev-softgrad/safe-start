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
            cls: 'sfa-button-auth'
        },
        Companies: {
            text: 'Companies',
            cls: 'sfa-button-companies'
        },
        Company: {
            text: 'Vehicles',
            disabled: true,
            cls: 'sfa-button-vehicles'
        },
        Alerts: {
            text: 'Alerts',
            disabled: true,
            cls: 'sfa-button-alerts'
        },
        Users: {
            text: 'Users',
            disabled: true,
            cls: 'sfa-button-users'
        },
        SystemSettings: {
            text: 'Settings',
            cls: 'sfa-button-system-settings'
        },
        SystemStatistic: {
            text: 'Statistic',
            cls: 'sfa-button-system-statistic'
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
                margin: '7 2 4 2',
                width: 60,
                handler: function () {
                    me.fireEvent('showPage', this.componentClass);
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
                text: config.text,
                disabled: config.disabled,
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
