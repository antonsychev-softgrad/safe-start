Ext.define('SafeStartApp.view.pages.panel.VehicleAlert', {
    extend: 'Ext.Container',

    alias: 'widget.SafeStartVehicleAlertPanel',
    xtype: 'SafeStartVehicleAlertPanel',

    requires: [

    ],

    record: false,

    config: {cls:'sfa-alert-information',
        title: 'Alert Information',
        baseCls: 'x-show-contact',
        layout: 'vbox',
        scrollable: true,
        record: null
    },

    initialize: function () {
        var self = this;
        this.uniqueId = Ext.id();
        this.callParent();
        this.add([
            {
                id: 'SafeStartVehicleAlertContent' + this.uniqueId,
                tpl: [
                    '<div class="top">',
                    '<div class="name">{vehicle.title}' +
                        '<span>{vehicle.plantId}</span>' +
                        '</div>' +
                        '<div class="name">{user.firstName} {user.lastName} at {title}' +
                        '<span>{alert_description}</span>' +
                        '<span>{description}</span></div>',
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
                        { rank: 'closed', title: 'Closed'}
                    ]
                }
            },
            {
                xtype: 'toolbar',
                items: [
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
                    '<div class="name">{user.firstName} {user.lastName} at <b>{update_date}</b><br/>',
                    '<span>{content}</span>',
                    '</div>',
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
        if (!newRecord) return;
        this.record = newRecord;
        this.down('#SafeStartVehicleAlertContent' + this.uniqueId).setData(this.record.raw);
        this.setComments(this.record.raw['comments']);
        var images = newRecord.get('images');
        if (images.length) {
            this.down('#SafeStartVehicleAlertImages' + this.uniqueId).show();
            Ext.each(images, function (imageHash) {
                this.down('#SafeStartVehicleAlertImages' + this.uniqueId).add({
                    xtype: 'image',
                    src: 'api/image/' + imageHash + '/1024x768'
                });
            }, this)
        } else {
            this.down('#SafeStartVehicleAlertImages' + this.uniqueId).hide();
        }
        this.down('#SafeStartVehicleAlertStatus' + this.uniqueId).setValue(newRecord.get('status'));
    },

    updateAction: function () {
        var self = this;
        var values = {};
        var vehicleId = this.record.raw['vehicle']['id'];
        values.status = this.down('#SafeStartVehicleAlertStatus' + this.uniqueId).getValue();
        values.new_comment = this.down('#SafeStartVehicleAlertNewComment' + this.uniqueId).getValue();
        SafeStartApp.AJAX('vehicle/' + vehicleId + '/alert/' + this.record.get('id') + '/update', values, function (result) {
            self.record.set('status', values.status);
            if (values.new_comment != '') {
                self.record.raw['comments'].push({
                    user: SafeStartApp.userModel.data,
                    content: values.new_comment,
                    update_date: Ext.Date.format(new Date(), SafeStartApp.dateFormat +' '+ SafeStartApp.timeFormat)
                });
            }
            self.setComments(self.record.raw['comments']);
            self.down('#SafeStartVehicleAlertNewComment' + self.uniqueId).setValue('');
        });
    },

    setComments: function(comments) {
        if (comments.length) {
            this.down('#SafeStartVehicleAlertComments' + this.uniqueId).show();
            this.down('#SafeStartVehicleAlertComments' + this.uniqueId).setData({comments: comments});
        } else {
            this.down('#SafeStartVehicleAlertComments' + this.uniqueId).hide();
        }
    },

    deleteAction: function () {
        var self = this;
        Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this alert?", function () {
            SafeStartApp.AJAX('vehicle/alert/' + self.record.get('id') + '/delete', {}, function (result) {
                self.getParent().pop();
                self.getParent().alertsStore.loadData();
            });
        });
    }

});