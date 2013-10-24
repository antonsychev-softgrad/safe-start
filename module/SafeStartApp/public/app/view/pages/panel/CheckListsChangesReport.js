Ext.define('SafeStartApp.view.pages.panel.CheckListsChangesReport', {
    extend: 'Ext.Panel',
    xtype: 'SafeStartCheckListsChangesReportPanel',
    alias: 'widget.SafeStartCheckListsChangesReportPanel',

    requires: [

    ],

    record: null,

    config: {
        cls: 'sfa-statistic',
        xtype: 'panel',
        title: 'CheckLists Changes',
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        name: 'changes',
        scrollable: true,
        minHeight: 300
    },

    initialize: function () {
        var self = this;
        this.callParent();
        this.setContentPanel();
    },

    setContentPanel: function () {
        var self = this;
        this.add(
            [
                {
                    xtype: 'toolbar',
                    docked: 'top',
                    cls: 'sfa-top-toolbar',
                    items: [
                        {
                            xtype: 'datepickerfield',
                            name: 'from',
                            label: 'From',
                            labelWidth: '',
                            picker: {
                                yearFrom: new Date().getFullYear() - 10,
                                yearTo: new Date().getFullYear()
                            }
                        },
                        {
                            xtype: 'datepickerfield',
                            name: 'to',
                            label: 'To',
                            labelWidth: '',
                            picker: {
                                yearFrom: new Date().getFullYear() - 10,
                                yearTo: new Date().getFullYear()
                            },
                            value: new Date()
                        },
                        {
                            xtype: 'button',
                            name: 'reload',
                            ui: 'action',
                            action: 'refresh',
                            iconCls: 'refresh',
                            handler: function () {
                                this.up('SafeStartCheckListsChangesReportPanel').updateDataView();
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    name: 'content'
                }
            ]
        );
    },

    loadData: function () {
        var now = new Date();
        this.down('datepickerfield[name=to]').setValue(now);

        var from = new Date();
        from.setYear(from.getFullYear() - 1);
        this.down('datepickerfield[name=from]').setValue(from);

        this.updateDataView();
    },

    chartAdded: false,
    updateDataView: function () {
        var self = this;
        var post = {};
        if (this.down('datepickerfield[name=from]').getValue()) post.from = this.down('datepickerfield[name=from]').getValue().getTime() / 1000;
        if (this.down('datepickerfield[name=to]').getValue()) post.to = this.down('datepickerfield[name=to]').getValue().getTime() / 1000;

        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datepickerfield[name=from]').getValue(), SafeStartApp.dateFormat),
            to: Ext.Date.format(this.down('datepickerfield[name=to]').getValue(), SafeStartApp.dateFormat)
        };

        SafeStartApp.AJAX('admin/getCheckListsChangesStatistic', post, function (result) {
            if (result.statistic) {
                self.updateContent(result.statistic);
            }
        });
    },

    updateContent: function (statistic) {
        var content = '<div class="top">';
        var color = '#D3D3D3';
        var k = 0;
        Ext.each(statistic, function (item) {
            if (k % 2 == 0)  color = '#D3D3D3';
            else color = '#fffff';
            k++;
            content += '<div class="sfa-statistic-item" style="background-color: ' + color + '">';
            content += k+') ';
            item.typeText = 'Text';
            switch (item.type) {
                case 'root':
                    item.typeText = 'CheckList Group';
                    break;
                case 'group':
                    item.typeText = 'Questions Group';
                    break;
                case 'radio':
                    item.typeText = 'YES|NO|N/A';
                    break;
                case 'text':
                    item.typeText = 'Text';
                    break;
            }
            switch (item.action) {
                case 'update':
                    content += 'Filed <b>' + item.prev_key + '</b> canged to <b>' + item.key + '</b> of <b>' + item.typeText + '</b> type at ' + Ext.Date.format(new Date(item.date * 1000), SafeStartApp.timeFormat + ' ' + SafeStartApp.dateFormat);
                    break;
                case 'delete':
                    content += 'Filed <b>' + item.key + '</b> of <b>' + item.typeText + '</b> type was deleted at ' + Ext.Date.format(new Date(item.date * 1000), SafeStartApp.timeFormat + ' ' + SafeStartApp.dateFormat);
                    break;
                case 'create':
                    content += 'New filed <b>' + item.key + '</b> of <b>' + item.typeText + '</b> type was created at ' + Ext.Date.format(new Date(item.date * 1000), SafeStartApp.timeFormat + ' ' + SafeStartApp.dateFormat);
                    break;
            }
            content += ' <i>company: <b>' + item.company_name + '</b></i>';
            content += '</div>';
        }, this);
        content += '</div>';
        this.down('panel[name=content]').setHtml(content);
    }

});