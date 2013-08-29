Ext.define('SafeStartApp.view.pages.nestedlist.Vehicles', {
    extend: 'Ext.dataview.NestedList',
    alias: 'widget.SafeStartNestedListVehicles',
    mixins: ['SafeStartApp.store.mixins.FilterByField'],
    // id: 'companyVehicles',
    name: 'vehicles',
    config: {
        minWidth: 150,
        maxWidth: 300,
        title: 'Vehicles',
        displayField: 'text',
        cls: 'sfa-left-container',
        flex: 1,
        getTitleTextTpl: function() {
            return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
        },
        getItemTextTpl: function() {
            return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
        }
    },

    initialize: function () {
        this.setItems([{
            xtype: 'toolbar',
            docked: 'top',
            items: [{
                xtype: 'searchfield',
                placeHolder: 'Search...',
                listeners: {
                    clearicontap: function() {
                        this.getStore().clearFilter();
                    },
                    keyup: function(field) {
                        this.filterStoreDataBySearchFiled(this.getStore(), field, 'text');
                        console.log(this.getStore().getCount());
                        //todo: fix searching
                        // this.setData(this.getStore().getData());
                    },
                    scope: this
                }
            }, {
                xtype: 'spacer'
            }, {
                xtype: 'button',
                name: 'reload',
                ui: 'action',
                iconCls: 'refresh',
                cls: 'sfa-search-reload',
                handler: function() {
                    this.up('nestedlist[name=vehicles]').getStore().loadData();
                }
            }]
        }]);
    }
});