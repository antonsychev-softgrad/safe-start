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
        scrollable: true,
        cls: 'inside',

        layout: {
            type: 'card',
            animation: {
                type: 'slide',
                direction: 'left',
                duration: 250
            }
        },

        items: [
            {
                cls: 'card',
                xtype: 'panel',
                name: 'company-info',
                scrollable: true,
                html: '<div><h2>Select company for see info</h2></div>'
            }
        ]
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Companies');
        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top',
            title: 'Companies'
        });

        this.companiesStore = Ext.create('SafeStartApp.store.Companies');
        this.companiesStore.loadData();

        this.add({
            xtype: 'list',
            name: 'companies',
            itemTpl: '<div class="contact">{title}</div>',
            docked: 'left',
            width: 300,
            store: this.companiesStore,
            items: [
                {
                    xtype: 'toolbar',
                    docked: 'top',

                    items: [
                        { xtype: 'spacer' },
                        {
                            xtype: 'searchfield',
                            placeHolder: 'Search...',
                            listeners: {
                                scope: this,
                                clearicontap: function () {
                                    self.companiesStore.clearFilter();
                                },
                                keyup: function (field) {
                                   return self.filterStoreDataBySearchFiled(self.companiesStore, field, 'title');
                                }
                            }
                        },
                        { xtype: 'spacer' }
                    ]
                }
            ]
        });
    }
});