Ext.define('SafeStartApp.view.pages.Companies', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
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

        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top',
            btnTitle: 'Companies'
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
                    margin: '',
                    store: this.companiesStore,
                    items: [{
                        xtype: 'toolbar',
                        docked: 'top',
                        items: [{ 
                            xtype: 'spacer'
                        }, {
                            iconCls: 'add',
                            cls: 'sfa-add-button',
                            ui: 'action',
                            text: 'Add Company',
                            action: 'add-company'
                        }, {
                            xtype: 'spacer'
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
                margin: '0',
                items: [{
                    xtype: 'toolbar',
                    items: []
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
