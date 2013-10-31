Ext.define('SafeStartExt.view.component.Companies', {
    extend: 'Ext.panel.Panel',
    requires: [
        'SafeStartExt.view.container.TopNav',
        'SafeStartExt.view.panel.CompaniesList',
        'SafeStartExt.view.panel.CompanyInfo',
        'SafeStartExt.view.form.Company'
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
                    cls: 'sfa-info-container',
                    xtype: 'panel',
                    type: 'vbox',
                    flex: 2,
                    ui: 'transparent',
                    name: 'company-info'
                }]
            }]
        });
        this.callParent();
    }
});
