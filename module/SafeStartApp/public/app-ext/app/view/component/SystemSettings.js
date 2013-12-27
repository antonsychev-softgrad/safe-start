Ext.define('SafeStartExt.view.component.SystemSettings', {
    extend: 'Ext.panel.Panel',

    xtype: 'SafeStartExtComponentSystemSettings',

    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.panel.ManageDefaultChecklist',
    ],

    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    width: '100%',
    ui: 'transparent',

    initComponent: function () {
        Ext.apply(this, {
            items: [{
                xtype: 'SafeStartExtContainerTopNav',
                titleText: 'System Settings'
            }, {
                xtype: 'container',
                flex: 1,
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'tabpanel',
                    cls: 'sfa-info-container sfa-system-settings',
                    height: '100%',
                    items: [{
                        xtype: 'SafeStartExtPanelManageDefaultChecklist'
                    }],
                    listeners: {
                    }
                }]
            }]
        });
        this.callParent();
    }
});