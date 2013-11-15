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
    border: 0,
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
                    border: 0,
                    layout: 'fit',
                    flex: 2,
                    ui: 'transparent',
                    padding: 30,
                    name: 'company-info'
                }]
            }]
        });
        this.callParent();
    },

    setCompanyId: function (companyId) {
        var me = this,
            store = this.down('SafeStartExtPanelCompaniesList').getListStore();

        if (store.getCount()) {
            this.setCompanyRecord(store.findRecord('id', companyId));
        } else {
            store.on('load', function () {
                me.setCompanyRecord(store.findRecord('id', companyId));
            }, this, {single: true});
        }
    },

    setCompanyRecord: function (record) {
        var dataView = this.down('SafeStartExtPanelCompaniesList').down('dataview');

        if (record) {
            dataView.select(record);
            this.fireEvent('changeCompanyAction', record);
        } else {
            this.fireEvent('companyNotFoundAction');
        }
    }
});
