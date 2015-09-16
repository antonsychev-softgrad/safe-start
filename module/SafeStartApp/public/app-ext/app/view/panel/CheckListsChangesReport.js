Ext.define('SafeStartExt.view.panel.CheckListsChangesReport', {
    extend: 'Ext.panel.Panel',
    xtype: 'SafeStartExtPanelCheckListsChangesReport',

    requires: [],

    record: null,

    cls: 'sfa-statistic',
    title: 'Checklist Changes',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    name: 'changes',
    autoScroll: true,
    minHeight: 300,
    
    listeners: {
        afterrender: function () {
            this.loadData();
        }
    },

    initComponent: function() {
        var self = this;
        this.callParent();
        this.setContentPanel();
    },

    setContentPanel: function() {
        var self = this;
        var now = new Date();
        var prevYear = new Date();
        prevYear.setFullYear(now.getFullYear()-1);
        this.add(
            [{
                xtype: 'container',
                docked: 'top',
                defaults: {
                    margin: 5
                },
                layout: 'hbox',
                items: [{
                    xtype: 'datefield',
                    name: 'from',
                    fieldLabel: 'From',
                    value: prevYear,
                    labelWidth: 40,
                    cls: 'sfa-datepicker sfa-from'
                }, {
                    xtype: 'datefield',
                    name: 'to',
                    fieldLabel: 'To',
                    cls: 'sfa-datepicker',
                    labelWidth: 20,
                    value: now
                }, {
                    xtype: 'button',
                    name: 'reload',
                    ui: 'blue',
                    scale: 'medium',
                    action: 'refresh',
                    text: 'Refresh',
                    handler: function() {
                        this.up('SafeStartExtPanelCheckListsChangesReport').updateDataView();
                    }
                }]
            }, {
                xtype: 'panel',
                name: 'content'
            }]
        );
    },

    loadData: function() {
        var now = new Date();
        this.down('datefield[name=to]').setValue(now);

        var from = new Date();
        from.setYear(from.getFullYear() - 1);
        this.down('datefield[name=from]').setValue(from);

        this.updateDataView();
    },

    chartAdded: false,
    updateDataView: function() {
        var self = this;
        var post = {};
        if (this.down('datefield[name=from]').getValue()) post.from = this.down('datefield[name=from]').getValue().getTime() / 1000;
        if (this.down('datefield[name=to]').getValue()) post.to = this.down('datefield[name=to]').getValue().getTime() / 1000;

        var data = {};
        data.period = {
            from: Ext.Date.format(this.down('datefield[name=from]').getValue(), SafeStartExt.dateFormat),
            to: Ext.Date.format(this.down('datefield[name=to]').getValue(), SafeStartExt.dateFormat)
        };

        SafeStartExt.Ajax.request({
            url: 'admin/getCheckListsChangesStatistic', 
            data: post, 
            success: function(result) {
                if (result.statistic) {
                    self.updateContent(result.statistic);
                }
            }
        });
    },

    updateContent: function(statistic) {
        var content = '<div class="top">';
        var color = '#D3D3D3';
        var k = 0;
        Ext.each(statistic, function(item) {
            if (k % 2 == 0) color = '#D3D3D3';
            else color = '#fffff';
            k++;
            content += '<div class="sfa-statistic-item" style="background-color: ' + color + '">';
            content += k + ') ';
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
                    content += 'Filed <b>' 
                        + item.prev_key + '</b> canged to <b>' 
                        + item.key + '</b> of <b>'
                        + item.typeText + '</b> type at ' 
                        + Ext.Date.format(new Date(item.date * 1000), SafeStartExt.timeFormat + ' ' + SafeStartExt.dateFormat);
                    break;
                case 'delete':
                    content += 'Filed <b>' 
                        + item.key + '</b> of <b>' 
                        + item.typeText + '</b> type was deleted at ' 
                        + Ext.Date.format(new Date(item.date * 1000), SafeStartExt.timeFormat + ' ' + SafeStartExt.dateFormat);
                    break;
                case 'create':
                    content += 'New filed <b>' 
                        + item.key + '</b> of <b>'
                        + item.typeText + '</b> type was created at ' 
                        + Ext.Date.format(new Date(item.date * 1000), SafeStartExt.timeFormat + ' ' + SafeStartExt.dateFormat);
                    break;
            }
            content += ' <i>company: <b>' + item.company_name + '</b></i>';
            content += '</div>';
        }, this);
        content += '</div>';
        this.down('panel[name=content]').update(content);
    }
});
