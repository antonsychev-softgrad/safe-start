Ext.define('SafeStartApp.view.pages.Companies', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Companies',
        'SafeStartApp.model.Company',
        'SafeStartApp.store.Companies',
        'SafeStartApp.view.forms.Company',
        'SafeStartApp.view.pages.panel.LeftContainer'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartCompaniesPage',

    config: {
        title: 'Companies',
        iconCls: 'team',
        styleHtmlContent: true,
        layout: 'hbox',
        cls: 'companies',
        tab: {
            action: 'companies'
        },
        items: [

        ]
    },

    initialize: function() {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Companies');
        this.add({
            xtype: 'SafeStartCompaniesToolbar',
            docked: 'top'
        });

        this.companiesStore = Ext.create('SafeStartApp.store.Companies');
        this.companiesStore.loadData();

        this.add(this.getCompanyList());

        this.add(this.getInfoPanel());
    },

    getCompanyList: function() {
        var self = this;
        return {
            xtype: 'SafeStartLeftContainer',
            flex: 1,
            items: [{
                xtype: 'container',
                layout: 'fit',
                items: [{
                    xtype: 'list',
                    name: 'companies',
                    itemTpl: '<div class="contact">{title}</div>',
                    showAnimation: {
                        type: 'pop'
                    },
                    hideAnimation: {
                        type: 'pop',
                        out: 'true'
                    },
                    cls: 'sfa-left-container',
                    margin: '0 20 0 0',
                    store: this.companiesStore,
                    items: [{
                        xtype: 'toolbar',
                        docked: 'top',
                        items: [{
                            iconCls: 'add',
                            cls: 'sfa-add-button',
                            ui: 'action',
                            text: 'Add Company',
                            action: 'add-company'
                        }, {
                            xtype: 'spacer',
                            flex: 1
                        }, {
                            iconCls: 'arrow_left',
                            height: 20,
                            cls: 'sfa-collapse',
                            iconMask: true,
                            handler: function (btn) {
                                var panel = this.up('SafeStartLeftContainer');
                                panel.toggleMenu();
                            }
                        }]
                    }, {
                        xtype: 'toolbar',
                        docked: 'top',
                        items: [{
                            xtype: 'searchfield',
                            flex: 1,
                            cls:'sfa-search',
                            placeHolder: 'Search...',
                            listeners: {
                                scope: this,
                                clearicontap: function() {
                                    self.companiesStore.clearFilter();
                                },
                                keyup: function(field) {
                                    self.filterStoreDataBySearchFiled(self.companiesStore, field, 'title');
                                }
                            }
                        }, {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'action',
                            cls: 'sfa-search-reload',
                            iconCls: 'refresh',
                            handler: function() {
                                this.up('list[name=companies]').getStore().loadData();
                            }
                        }]
                    }]
                }]
            }, {
                xtype: 'panel',
                cls: 'sfa-left-container',
                items: [{
                    xtype: 'toolbar',
                    items: [{
                        iconCls: 'arrow_right',
                        height: 20,
                        iconMask: true,
                        handler: function () {
                            var panel = this.up('SafeStartLeftContainer');
                            panel.toggleMenu();
                        }
                    }]
                }]
            }]
        };
    },
    getInfoPanel: function() {
        return {
            cls: 'sfa-info-container',
            xtype: 'panel',
            layout: 'card',
            flex: 2,
            minWidth: 150,
            name: 'company-info'
        };
    }
});