Ext.define('SafeStartExt.view.panel.CompanyInfo', {
    extend: 'Ext.view.View',
    requires: [
    ],
    xtype: 'SafeStartExtPanelCompanyInfo',
    layout: {
        type: 'vbox'
    },
    ui: 'light',
    itemSelector: 'table tr',

    initComponent: function () {
        Ext.apply(this, {
            tpl: new Ext.XTemplate(
                '<table style="min-width: 400px; font-size: 18px; color: #344; margin: 10px">',
                '<tpl for=".">',
                '<tr">',
                    '<td>{key}</td>',
                    '<td>{value}</td>',
                '</tr>',
                '</tpl>',
                '</table>'
            ),
            store: {
                proxy: {
                    type: 'memory'
                },
                fields: ['key', 'value']
            }
        });
        this.callParent();
    },

    setCompanyInfo: function (company) {
        var expiryDate = Ext.Date.format(
            company.get('expiry_date'), 
            SafeStartExt.dateFormat + ' ' + SafeStartExt.timeFormat
        );

        this.getStore().loadData([{
            key: 'Company Name:', 
            value: company.get('title')
        }, {
            key: 'Responsible Name', 
            value: company.get('name')
        }, {
            key: 'Responsible Email:', 
            value: company.get('email')
        }, {
            key: 'Company Address:', 
            value: company.get('address')
        }, {
            key: 'Company Phone:', 
            value: company.get('phone')
        }, {
            key: 'Company Info:', 
            value: company.get('description')
        }, {
            key: 'Limited Access', 
            value: company.get('restricted') ? 'Yes': 'No'
        }, {
            key: 'Number of users:', 
            value: company.get('max_users') 
        }, {
            key: 'Number of vehicles:',
            value: company.get('max_vehicles') 
        }, {
            key: 'Expiry Date:',
            value: expiryDate 
        }]);
    }
});
