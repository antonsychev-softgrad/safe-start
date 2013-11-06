Ext.define('SafeStartApp.view.pages.panel.VehicleAlert', {
    extend: 'Ext.Container',

    alias: 'widget.SafeStartVehicleAlertPanel',
    xtype: 'SafeStartVehicleAlertPanel',

    requires: [

    ],

    record: false,

    config: { 
        cls:'sfa-alert-information',
        title: 'Alert Information',
        baseCls: 'x-show-contact',
        scrollable: true,
        layout: 'vbox',
        record: null,
        defaults: {
            maxWidth: 700
        }
    },

    initialize: function () {
        this.uniqueId = Ext.id();
        this.callParent();
        this.add([
            {
                // id: 'SafeStartVehicleAlertContent' + this.uniqueId,
                name: 'alert-content',
                tpl: [
                    '<div class="sfa-alert-info">',
                        '<div>Vehicle: {vehicle.title} (<b>{vehicle.plantId}</b>)</div>',
                        '<div>Fault: <b>{alertDescription}</b></div>',
                        '<div>Description: {description} </div>',
                        '<div>Added by: {user.firstName} {user.lastName} at {creationDate}</div>',
                        '<tpl for="history">',
                            '<div>{action} by: {username} at {date} </div>',
                        '</tpl>',
                    '</div>'
                ].join('')
            },
            {
                xtype: 'selectfield',
                id: 'SafeStartVehicleAlertStatus' + this.uniqueId,
                label: 'Status',
                cls:'sfa-atatus',
                valueField: 'rank',
                displayField: 'title',
                store: {
                    data: [
                        { rank: 'new', title: 'New'},
                        { rank: 'closed', title: 'Completed'}
                    ]
                }
            },
            {
                xtype: 'toolbar',
                items: [
                    { xtype: 'spacer' },
                    {
                        xtype: 'button',
                        text: 'Update',
                        name: 'save-data',
                        ui: 'confirm',
                        handler: function () {
                            this.up('SafeStartVehicleAlertPanel').updateAction();
                        }
                    }
                ]
            },
            {
                // id: 'SafeStartVehicleAlertHistory' + this.uniqueId,
                name: 'alert-history',
                tpl: [
                    '<div class="sfa-alert-comments">',
                    '<h3>Previous alerts:</h3>',
                    '<tpl for="alerts">',
                    '<div class="sfa-item">',
                    '<div class="alert">Added by {user} at <b>{date} </b><br/>',
                    '</div></div>',
                    '</tpl>',
                    '</div>'
                ].join('')
            },
            {
                xtype: 'carousel',
                direction: 'horizontal',
                id: 'SafeStartVehicleAlertImages' + this.uniqueId,
                height: 400
            },
            {
                id: 'SafeStartVehicleAlertComments' + this.uniqueId,
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
            },
            {
                xtype: 'textareafield',
                id: 'SafeStartVehicleAlertNewComment' + this.uniqueId,
                label: 'New Comment',
                maxRows: 4
            },
            {
                xtype: 'toolbar',
                items: [
                    {
                        xtype: 'button',
                        text: 'Delete',
                        name: 'delete-data',
                        ui: 'decline',
                        iconCls: 'delete',
                        handler: function () {
                            this.up('SafeStartVehicleAlertPanel').deleteAction();
                        }
                    },
                    { xtype: 'spacer' },
                    {
                        xtype: 'button',
                        text: 'Save',
                        name: 'save-data',
                        ui: 'confirm',
                        handler: function () {
                            this.up('SafeStartVehicleAlertPanel').updateAction();
                        }
                    }
                ]
            }
       ] );
    },

    updateRecord: function (newRecord) {
        if (!newRecord) {
            return;
        }
        this.record = newRecord;

        var alertContent = {
            vehicle: newRecord.get('vehicle'),
            alertDescription: newRecord.get('alertDescription'),
            description: newRecord.get('description'),
            user: newRecord.get('user'),
            creationDate: newRecord.get('creationDate'),
            history: []
        };

        var history = Ext.Array.from(newRecord.get('history'));

        var refreshedFlag = false;
        var alertsHistory = {
            alerts: []
        };
        var alert = {};
        var date;
        var i = history.length - 1; 
        for (i; i >= 0; i--) {
            if (history[i].action === 'alert_refreshed') {
                refreshedFlag = true;
                alert = {
                    user: history[i].user.firstName + ' ' + history[i].user.lastName
                };
                date = new Date(history[i].date * 1000);
                alert.date = Ext.Date.format(date, SafeStartApp.dateFormat + ' ' + SafeStartApp.timeFormat);
                alertsHistory.alerts.push(alert);
            } else if (! refreshedFlag) {
                alert = {
                    username: history[i].user.firstName + ' ' + history[i].user.lastName
                };
                date = new Date(history[i].date * 1000);
                alert.date = Ext.Date.format(date, SafeStartApp.dateFormat + ' ' + SafeStartApp.timeFormat);

                switch (history[i].action) {
                    case 'alert_reopened':
                        alert.action = 'Reopened';
                        break;
                    case 'alert_closed':
                        alert.action = 'Completed';
                        break;
                }
                alertContent.history.push(alert);
            }
        }
        alertContent.history.reverse();
        this.alertContent = alertContent;


        this.down('container[name=alert-content]').setData(alertContent);

        if (alertsHistory.alerts.length) {
            this.down('container[name=alert-history]').setData(alertsHistory);
            this.down('container[name=alert-history]').show();
        } else {
            this.down('container[name=alert-history]').hide();
        }


        this.setComments(this.record.raw.comments);
        var images = newRecord.get('images');
        if (images.length) {
            this.down('#SafeStartVehicleAlertImages' + this.uniqueId).show();
            Ext.each(images, function (imageHash) {
                this.down('#SafeStartVehicleAlertImages' + this.uniqueId).add({
                    xtype: 'image',
                    src: 'api/image/' + imageHash + '/1024x768'
                });
            }, this);
        } else {
            this.down('#SafeStartVehicleAlertImages' + this.uniqueId).hide();
        }
        this.down('#SafeStartVehicleAlertStatus' + this.uniqueId).setValue(newRecord.get('status'));
    },

    updateAction: function () {
        var self = this;
        var values = {};
        var vehicleId = this.record.raw.vehicle.id;
        var status = this.down('#SafeStartVehicleAlertStatus' + this.uniqueId).getValue();
        var action = '';
        if (this.record.get('status') != status) {
            switch (status) {
                case 'new':
                    action = 'Reopened';
                    break;
                case 'closed':
                    action = 'Completed';
                    break;
            }
            
            this.alertContent.history.push({
                username: SafeStartApp.userModel.getFullName(),
                action: action,
                date: Ext.Date.format(new Date(), SafeStartApp.dateFormat + ' ' + SafeStartApp.timeFormat)
            });
            this.down('container[name=alert-content]').setData(this.alertContent);
        }
        values.status = this.down('#SafeStartVehicleAlertStatus' + this.uniqueId).getValue();
        values.new_comment = this.down('#SafeStartVehicleAlertNewComment' + this.uniqueId).getValue();
        SafeStartApp.AJAX('vehicle/' + vehicleId + '/alert/' + this.record.get('id') + '/update', values, function (result) {
            self.record.set('status', values.status);
            if (values.new_comment != '') {
                self.record.raw.comments.push({
                    user: SafeStartApp.userModel.data,
                    content: values.new_comment,
                    update_date: Ext.Date.format(new Date(), SafeStartApp.dateFormat +' '+ SafeStartApp.timeFormat)
                });
            }
            self.setComments(self.record.raw.comments);
            self.down('#SafeStartVehicleAlertNewComment' + self.uniqueId).setValue('');
        });
    },

    setComments: function(comments) {
        if (comments && comments.length) {
            this.down('#SafeStartVehicleAlertComments' + this.uniqueId).show();
            this.down('#SafeStartVehicleAlertComments' + this.uniqueId).setData({comments: comments});
        } else {
            this.down('#SafeStartVehicleAlertComments' + this.uniqueId).hide();
        }
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this alert?", function (btn) {
            if (btn == 'yes') {
                SafeStartApp.AJAX('vehicle/alert/' + self.record.get('id') + '/delete', {}, function (result) {
                    self.getParent().pop();
                    self.getParent().alertsStore.loadData();
                });
            }
        });
    }

});
