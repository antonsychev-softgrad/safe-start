Ext.define('SafeStartApp.view.pages.Companies', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Companies',
        'SafeStartApp.model.Company',
        'SafeStartApp.store.Companies',
        'SafeStartApp.view.forms.CompanySettings'
    ],

    mixins: ['SafeStartApp.store.mixins.FilterByField'],

    xtype: 'SafeStartCompaniesPage',

    config: {
        title: 'Companies',
        iconCls: 'team',
        styleHtmlContent: true,
        layout: 'hbox',
        cls: 'companies',
        items: [

        ]
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Companies');
        this.add({
            xtype: 'SafeStartCompaniesToolbar',
            docked: 'top',
            title: 'Companies'
        });

        this.companiesStore = Ext.create('SafeStartApp.store.Companies');
        this.companiesStore.loadData();

        this.add(this.getCompanyList());

        this.add(this.getInfoPanel());
    },

    getCompanyList: function() {
        var self = this;
        return {
            xtype: 'list',
                name: 'companies',
                itemTpl: '<div class="contact">{title}</div>',
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
                flex: 1,
                cls: 'sfa-left-container',
                store: this.companiesStore,
                items: [
                {
                    xtype: 'toolbar',
                    docked: 'top',

                    items: [
                        {
                            xtype: 'searchfield',
                            placeHolder: 'Search...',
                            listeners: {
                                scope: this,
                                clearicontap: function () {
                                    self.companiesStore.clearFilter();
                                },
                                keyup: function (field) {
                                    self.filterStoreDataBySearchFiled(self.companiesStore, field, 'title');
                                }
                            }
                        },
                        { xtype: 'spacer' },
                        {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'action',
                            cls:'sfa-search-reload',
                            iconCls: 'refresh',
                            handler: function() {
                                this.up('list[name=companies]').getStore().loadData();
                            }
                        }
                    ]
                }
            ]
        };
    },

    getInfoPanel: function() {
        return  {
            cls: 'sfa-info-container',
            xtype: 'panel',
            layout: 'card',
            flex: 2,
            minWidth: 150,
            name: 'company-info'
        };
    }
});