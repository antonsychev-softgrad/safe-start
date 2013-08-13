Ext.define('SafeStartApp.view.pages.Companies', {
    extend: 'Ext.Container',

    requires: [
        'SafeStartApp.view.pages.toolbar.Main',
        'SafeStartApp.model.Company',
        'SafeStartApp.store.Companies'
    ],

    xtype: 'SafeStartCompaniesPage',

    config: {
        title: 'Companies',
        iconCls: 'team',

        styleHtmlContent: true,
        scrollable: true,

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
                name: 'info',
                scrollable: true,
                html: '<div><h2>Select company for see info</h2></div>'
            }
        ]
    },

    initialize: function () {
        var self = this;
        this.callParent();

        this.mainToolbar = Ext.create('SafeStartApp.view.pages.toolbar.Main');
        this.add({
            xtype: 'SafeStartMainToolbar',
            docked: 'top',
            title: 'Companies'
        });

        this.companiesStore = Ext.create('SafeStartApp.store.Companies');
        this.companiesStore.loadData();

        this.add({
            xtype: 'list',
            id: 'companies',
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
                                    var value = field.getValue(),
                                        store = self.companiesStore;
                                    store.clearFilter(!!value);
                                    if (value) {
                                        var searches = value.split(','),
                                            regexps = [],
                                            i, regex;

                                        //loop them all
                                        for (i = 0; i < searches.length; i++) {
                                            //if it is nothing, continue
                                            if (!searches[i]) continue;
                                            regex = searches[i].trim();
                                            regex = regex.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
                                            //if found, create a new regular expression which is case insenstive
                                            regexps.push(new RegExp(regex.trim(), 'i'));
                                        }
                                        store.filter(function (record) {
                                            var matched = [];
                                            //loop through each of the regular expressions
                                            for (i = 0; i < regexps.length; i++) {
                                                var search = regexps[i],
                                                    didMatch = search.test(record.get('title'));
                                                //if it matched the first or last name, push it into the matches array
                                                matched.push(didMatch);
                                            }
                                            return (regexps.length && matched.indexOf(true) !== -1);
                                        });
                                    }
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