Ext.define('SafeStartExt.view.component.CompanySettings', {
    extend: 'Ext.panel.Panel',

    xtype: 'SafeStartExtComponentCompanySettings',

    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.panel.CompanyAdditionalInfo',
        'SafeStartExt.view.panel.CompanyOtherUsers'
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
                titleText: 'Company Settings'
            }, {
                xtype: 'container',
                flex: 1,
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'tabpanel',
                    cls: 'sfa-info-container sfa-system-settings sfa-vehicles-tabpanel',
                    height: '100%',
                    items: [{
                        xtype: 'SafeStartExtPanelCompanyAdditionalInfo'
                    }, {
                        xtype: 'SafeStartExtPanelCompanyOtherUsers'
                    }],
                    listeners: {
                    }
                }]
            }]
        });
        this.callParent();
    }
});