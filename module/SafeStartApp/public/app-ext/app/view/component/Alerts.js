Ext.define('SafeStartExt.view.component.Alerts', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.panel.Alerts'
    ],
    xtype: 'SafeStartExtComponentAlerts',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    width: '100%',
    ui: 'transparent',

    initComponent: function() {
        Ext.apply(this, {
            items: [{
                xtype: 'SafeStartExtContainerTopNav',
                titleText: 'Users'
            }, {
                xtype: 'container',
                flex: 1,
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'SafeStartExtPanelAlerts',
                    cls: 'sfa-info-container',
                    companyId: this.companyId
                }]
            }]
        });
        this.callParent();
    }
});