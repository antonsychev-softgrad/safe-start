Ext.define('SafeStartApp.view.pages.nestedlist.Vehicles', {
    extend: 'Ext.dataview.NestedList',
    alias: 'widget.SafeStartNestedListVehicles',
    xtype: 'SafeStartNestedListVehicles',
    mixins: ['SafeStartApp.store.mixins.FilterByField'],
    name: 'vehicles',
    config: {
        minWidth: 150,
        maxWidth: 300,
        showAnimation: {
            type: 'slide',
            direction: 'right',
            duration: 100
        },
        hideAnimation: {
            type: 'slide',
            direction: 'left',
            duration: 200
        },
        title: 'Vehicles',
        displayField: 'text',
        cls: 'sfa-left-container',
        flex: 1,
        getTitleTextTpl: function () {
            return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
        },
        getItemTextTpl: function () {
            return '{' + this.getDisplayField() + '}<tpl if="leaf !== true"> -> </tpl>';
        },
        listeners: {
            activeitemchange: function (nestedlist) {
                var toolbar = this.down('toolbar');
                if (toolbar) {
                    toolbar.hide();
                }
            },
            back: function () {
                if(this._backButton._hidden) {
                    this.down('toolbar').show();
                }
            }
        }
    },

    filterVehiclesByName: function (value) {
        this.setFilterValue('text', value);
        this.updateNestedListStore();
    },

    syncStores: function () {
        this.filters = [];
        this.updateNestedListStore();
    },

    updateNestedListStore: function () {
        var filters = this.filters;
        var store = this.getStore();
        var vehiclesStore = this.vehiclesStore;
        var records = [];
        if (! filters['text']) {
            records = Ext.clone(vehiclesStore.getRoot().childNodes);
            Ext.each(records, function (record) {
                store.getRoot().appendChild(record);
            });
            this.goToNode(store.getRoot());
            return;
        }
        records = Ext.clone(store.getRoot().childNodes);
        Ext.each(records, function (record) {
            vehiclesStore.getRoot().appendChild(record);
            // record.appendChild(Ext.clone(record.childNodes));
        });
        records = Ext.clone(vehiclesStore.getRoot().childNodes);

        Ext.each(records, function (record) {
            record.childNodes = Ext.clone(record.childNodes);
            var match = true;
            var regExp;
            var property = 'text';
            regExp = RegExp('.*' + filters[property] + '.*', 'i');
            if (! (record.get(property) && regExp.test(record.get(property).toString()))) {
                match = false;
            }
            if (match) {
                store.getRoot().appendChild(record);
            }
        }, this);

        this.goToNode(store.getRoot());
    },

    getVehiclesStore: function () {
        return this.vehiclesStore;
    },

    setFilterValue: function (key, value) {
        this.filters[key] = value;
    },

    getFilters: function (key, value) {
        return this.filters;
    },

    initialize: function() {
        var me = this;
        this.filters = {};
        this.vehiclesStore = this.config.vehiclesStore;

        this.vehiclesStore.on('beforeload', function (store) {
            store.removeAll();
            me.getStore().removeAll();
        });

        this.vehiclesStore.on('load', function (store, records) {
            me.updateNestedListStore();
        });

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
                    keyup: function(field, e) {
                        this.filterVehiclesByName(field.getValue());
                    },
                    scope: this
                }
            }, {
                xtype: 'spacer'
            }, {
                xtype: 'button',
                name: 'reload',
                ui: 'action',
                action: 'refresh',
                iconCls: 'refresh',
                cls: 'sfa-search-reload',
                handler: function() {
                    var nestedlist = this.up('nestedlist');
                    this.up('toolbar').down('searchfield').setValue('');
                    nestedlist.filterVehiclesByName('');
                    nestedlist.vehiclesStore.loadData();
                }
            }]
        }]);
    }
});