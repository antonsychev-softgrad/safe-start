Ext.define('SafeStartApp.view.pages.panel.VehicleAlert', {
    extend: 'Ext.Container',

    alias: 'widget.SafeStartVehicleAlertPanel',
    xtype: 'SafeStartVehicleAlertPanel',

    requires: [

    ],

    record: false,

    config: {
        title: 'Alert Information',
        baseCls: 'x-show-contact',
        layout: 'vbox',

        items: [
            {
                id: 'SafeStartVehicleAlertContent',
                tpl: [
                    '<div class="top">',
                    '<div class="headshot" style="background-image:url({thumbnail});"></div>',
                    '<div class="name">{user.firstName} {user.lastName} at {title}<span>{alert_description}</span></div>',
                    '<br/><div class="name"><span>{description}</span></div>',
                    '</div>'
                ].join('')
            },
            {
                xtype: 'selectfield',
                id: 'SafeStartVehicleAlertStatus',
                label: 'Status',
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
                id: 'SafeStartVehicleAlertImages',
                height: 400
            },
            {
                xtype: 'textareafield',
                id: 'SafeStartVehicleAlertNewComment',
                label: 'New Comment',
                maxRows: 4
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
            }
        ],

        record: null
    },

    updateRecord: function (newRecord) {
        if (!newRecord) return;
        this.record = newRecord;
        this.down('#SafeStartVehicleAlertContent').setData(newRecord.data);
        var images = newRecord.get('images');
        if (images.length) {
            Ext.each(images, function(imageHash) {
                this.down('#SafeStartVehicleAlertImages').add({
                    xtype: 'image',
                    src: 'api/image/' + imageHash + '/1024x768'
                });
            }, this)
        }
        this.down('#SafeStartVehicleAlertStatus').setValue(newRecord.get('status'));
    },

    updateAction: function() {
        var values = [];
        SafeStartApp.AJAX('vehicle/1/alert/' + this.record.get('id') + '/update', values, function (result) {

        });
    }

});