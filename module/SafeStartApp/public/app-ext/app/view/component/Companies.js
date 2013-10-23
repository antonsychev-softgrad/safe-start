Ext.define('SafeStartExt.view.component.Companies', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.panel.CompaniesList',
        'SafeStartExt.view.panel.CompanyInfo'
    ],
    xtype: 'SafeStartExtComponentCompanies',
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
                titleText: 'Companies'
            }, {
                xtype: 'container',
                layout: {
                    type: 'hbox',
                    align: 'stretch'
                },
                flex: 1,
                items: [{
                    xtype: 'SafeStartExtPanelCompaniesList',
                    flex: 1,
                    maxWidth: 250
                }, {
                    xtype: 'SafeStartExtPanelCompanyInfo',
                    flex: 2
                }]
            }]
        });
        this.callParent();
    }
});
