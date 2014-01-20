Ext.define('SafeStartExt.view.panel.CompaniesList', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.view.View',
        'SafeStartExt.store.Companies'
    ],
    xtype: 'SafeStartExtPanelCompaniesList',
    cls: 'sfa-left-coll',
    layout: 'fit',
    ui: 'light-left',
    overflowY: 'auto',
    border: 0,

    initComponent: function() {
        var store = SafeStartExt.store.Companies.create({
            autoLoad: true
        });
        Ext.apply(this, {
            tbar: {
                xtype: 'toolbar',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                items: [{
                    text: 'Add company',
                    cls: 'sfa-add-button',
                    handler: function() {
                        this.up('SafeStartExtPanelCompaniesList').fireEvent('addCompanyAction');
                    }
                }, {
                    xtype: 'container',
                    layout: 'hbox',
                    items: [{
                        xtype: 'textfield',
                        cls: 'search',
                        flex: 1,
                        margin: '0 5 0 5',
                        height: 22,
                        listeners: {
                            change: function(textfield, value) {
                                store.clearFilter();
                                if (value) {
                                    store.filter('title', value);
                                }
                            }
                        }
                    }, {
                        xtype: 'button',
                        iconCls: 'sfa-icon-refresh',
                        scale: 'medium',
                        handler: function() {
                            this.up('toolbar').down('textfield').setValue('');
                            store.load();
                        }
                    }]
                }]
            },
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

    onCompanyClick: function(dataview, record) {
        this.fireEvent('changeCompanyAction', record);
    },

    getListStore: function() {
        return this.down('dataview').getStore();
    }

});