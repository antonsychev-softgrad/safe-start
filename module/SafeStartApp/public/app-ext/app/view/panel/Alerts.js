Ext.define('SafeStartExt.view.panel.Alerts', {
    extend: 'Ext.panel.Panel',
    xtype: 'SafeStartExtPanelAlerts',

    requires: [
        'SafeStartExt.store.Alerts',
        'SafeStartExt.view.Carousel'
    ],
    layout: {
        type: 'hbox',
        align: 'stretch'
    },

    initComponent: function () {
        var store = SafeStartExt.store.Alerts.create({vehicleId: this.vehicle.get('id')});
        Ext.apply(this, {
            items: [{
                xtype: 'panel',
                title: 'Alerts',
                ui: 'light-left',
                flex: 1,
                border: 0,
                maxWidth: 250,
                cls: 'sfa-previous-inspections-left-coll',
                overflowY: 'auto',
                items: [{
                    xtype: 'dataview',
                    itemSelector: 'div.sfa-alert-item',
                    tpl: new Ext.XTemplate(
                        '<tpl for=".">',
                        '<div class="sfa-alert-item">',
                            '<div class="sfa-alert-icon" style="background-image: url({thumbnail});">',
                            '</div>',
                            '<div class="sfa-alert-info">',
                                '<div class="sfa-alert-title">{alertDescription}</div>',
                                '<div class="sfa-alert-vehicle">{[values[\'SafeStartExt.model.Vehicle\'].title]}<b>{[values[\'SafeStartExt.model.Vehicle\'].plantId]}</b></div>',
                                '<div class="sfa-alert-user">added by {[values[\'SafeStartExt.model.User\'].firstName]} {[values[\'SafeStartExt.model.User\'].lastName]} at {creationDate}</div>',
                            '</div>',
                        '</div>',
                        '</tpl>'
                    ),
                    store: store,
                    listeners: {
                        select: this.onSelect,
                        deselect: this.onDeselect, 
                        scope: this
                    }
                }]
            }, {
                xtype: 'panel',
                layout: {
                    type: 'vbox'
                },
                name: 'alert-details',
                padding: '10 20',
                hidden: true,
                items: [{
                    xtype: 'dataview',
                    name: 'alert',
                    itemTpl: new Ext.XTemplate(
                        '<div class="sfa-alert-info">',
                        '<div>Vehicle: vehicle.title (<b>{vehicle.plantId}</b>)</div>',
                        '<div>Fault: <b>{alertDescription}</b></div>',
                        '<div>Description: {description} </div>',
                        '<div>Added by: {user.firstName} {user.lastName} at {creationDate}</div>',
                        '<tpl for="history">',
                            '<div>{action} by: {username} at {date} </div>',
                        '</tpl>',
                        '</div>'
                    )
                }, {
                    xtype: 'container',
                    width: 500,
                    layout: {
                        type: 'hbox'
                    },
                    items: [{
                        xtype: 'combobox',
                        fieldLabel: 'Status',
                        width: 430,
                        labelWidth: 130,
                        name: 'status',
                        store: {
                            proxy: {
                                type: 'memory'
                            },
                            fields: ['key', 'value'],
                            data: [{
                                key: 'New',
                                value: 'new'
                            }, {
                                key: 'Completed',
                                value: 'completed'
                            }]
                        },
                        displayField: 'key',
                        valueField: 'value',
                        editable: false,
                        queryMode: 'local'
                    }, {
                        xtype: 'button',
                        width: 70,
                        text: 'Update'
                    }]
                }, {
                    xtype: 'container',
                    name: 'previous-alerts',
                    tpl: [
                        '<div class="sfa-alert-comments">',
                        '<h3>Previous alerts:</h3>',
                        '<tpl for="alerts">',
                        '<div class="sfa-item">',
                        '<div class="alert">Added by {user} at {date} <br/>',
                        '</div></div>',
                        '</tpl>',
                        '</div>'
                    ].join('')

                }, {
                    xtype: 'carousel',
                    name: 'carousel',
                    height: 400
                }, {
                    xtype: 'container',
                    name: 'comments',
                    tpl: [
                        '<div class="sfa-alert-comments">',
                        '<h3>Comments:</h3>',
                        '<tpl for="comments">',
                        '<div class="sfa-item">',
                        '<div class="name">{user.firstName} {user.lastName} at <b>{update_date}</b><br/>',
                        '</div>',
                        '<span class="sfa-comment-content">{content}</span><div class="clear"></div></div>',
                        '</tpl>',
                        '</div>'
                    ].join('')
                }, {
                    xtype: 'textarea',
                    width: 500,
                    labelWidth: 130,
                    fieldLabel: 'Comment',
                    name: 'comment'
                }, {
                    xtype: 'container',
                    width: 500,
                    layout: {
                        type: 'vbox',
                        align: 'right'
                    },
                    items: [{
                        xtype: 'button',
                        text: 'Add',
                        right: 0
                    }]
                }],
                bbar: {
                    xtype: 'container',
                    layout: {
                        type: 'hbox'
                    },
                    items: [{
                        xtype: 'button',
                        name: 'delete',
                        text: 'Delete',
                        ui: 'red',
                        scale: 'medium'
                    }]
                },
                flex: 2
            }],
            listeners: {
                afterrender: function () {
                    store.load();
                }
            }
        });

        this.callParent();
    },
    onSelect: function (selModel, record) {
        var panel = this.down('panel[name=alert-details]');
        var data = record.getData();
        var history = data.history || [];
        var i = history.length - 1; 
        var refreshedFlag = false;
        var alert;
        var date;
        var alertsHistory = {
            alerts: []
        };
        data.history = [];
        for (i; i >= 0; i--) {
            if (history[i].action === 'alert_refreshed') {
                refreshedFlag = true;
                alert = {
                    user: history[i].user.firstName + ' ' + history[i].user.lastName
                };
                date = new Date(history[i].date * 1000);
                alert.date = Ext.Date.format(date, SafeStartExt.dateFormat + ' ' + SafeStartExt.timeFormat);
                alertsHistory.alerts.push(alert);
            } else if (! refreshedFlag) {
                alert = {
                    username: history[i].user.firstName + ' ' + history[i].user.lastName
                };
                date = new Date(history[i].date * 1000);
                alert.date = Ext.Date.format(date, SafeStartExt.dateFormat + ' ' + SafeStartExt.timeFormat);

                switch (history[i].action) {
                    case 'alert_reopened':
                        alert.action = 'Reopened';
                        break;
                    case 'alert_closed':
                        alert.action = 'Completed';
                        break;
                }
                data.history.push(alert);
            }
        }
        data.history.reverse();
        data.user = record.getUser().getData();

        data.vehicle = record.getVehicle().getData();
        // var alertData = [{
        //     vehicle: {
        //         title: 'Title',
        //         plantId: 'PlantId'
        //     },
        //     alertDescription: 'Alert description',
        //     description: 'Description',
        //     user: {
        //         firstName: 'Firstname',
        //         lastName: 'Lastname'
        //     },
        //     creationDate: 'dd/mm/yyyy',
        //     history: [
        //         {action: 'Action', username: 'Firstname Lastname', date: 'dd/mm/yyyy'}
        //     ]
        // }];
        panel.show();
        this.down('combobox[name=status]').select(data.status);

        console.log(data);
        this.setComments(data.comments);

        this.down('dataview[name=alert]').update(data);
        if (alertsHistory.alerts.length) {
            this.down('container[name=previous-alerts]').show();
            this.down('container[name=previous-alerts]').update(alertsHistory);
        } else {
            this.down('container[name=previous-alerts]').hide();
        }

        console.log(data);
        var images = data.images || [];
        var carousel = this.down('carousel[name=carousel]');
        if (images.length) {
            carousel.show();
            Ext.each(images, function (imageHash) {
                carousel.add({
                    xtype: 'image',
                    src: '/api/image/' + imageHash + '/1024x768'
                });
            }, this);
        } else {
            carousel.hide();
        }

    },

    onDeselect: function (selModel, record) {
        var panel = this.down('panel[name=alert-details]');
        panel.hide();
    },


    setComments: function(comments) {
        var container = this.down('container[name=comments]');
        if (comments && comments.length) {
            container.show();
            container.update({comments: comments});
        } else {
            container.hide();
        }
    }
});