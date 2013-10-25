Ext.define('SafeStartExt.view.panel.CompaniesList', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.view.View',
        'SafeStartExt.store.Companies'
    ],
    xtype: 'SafeStartExtPanelCompaniesList',
    cls:'sfa-companies-list-left-coll',
    layout: 'fit',
    ui: 'light-left',
    border: 0,
    title: 'Companies',

    initComponent: function () {
        var store = SafeStartExt.store.Companies.create({autoLoad: true});
        Ext.apply(this, {
            tbar: [{
                xtype: 'textfield',
                flex: 1,
                margin: '0 5 0 5',
                height: 22,
                listeners: {
                    change: function (textfield, value) {
                        store.clearFilter();
                        if (value) {
                            store.filter('title', value);
                        }
                    }
                }
            }, {
                iconCls: 'sfa-icon-refresh',
                scale: 'medium',
                handler: function () {
                    this.up('toolbar').down('textfield').setValue('');
                    store.loadData();
                }
            }],
            items: [{
                xtype: 'dataview',
                itemSelector: 'div.sfa-vehicle-item',
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class=sfa-vehicle-item>',
                    '{title}',
                    '</div>',
                    '</tpl>'
                ),
                store: store,
                listeners: {
                    itemclick: this.onCompanyClick,
                    scope: this
                }
            }]
        });
        this.callParent();
    },

    onCompanyClick: function (dataview, record) {
        this.fireEvent('changeCompanyAction', record);
    },

    getListStore: function () {
        return this.down('dataview').getStore();
    }

});
