Ext.define('SafeStartApp.view.pages.panel.VehicleInspectionDetails', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleInspectionDetails',

    requires: [

    ],

    config: {
        scrollable: true,
        margin: 10,
        layout: {
            type: 'vbox',
            align: 'center',
            pack: 'middle'
        },
        defaults: {
        },
        name: 'vehicle-inspection-details',
        cls: 'sfa-vehicle-inspection-details'
    },

    initialize: function () {

        this.callParent();
    },

    loadChecklist: function (vehicle, checklistId) {
        var me = this;
        SafeStartApp.AJAX('vehicle/' + checklistId + '/getchecklistdata', {}, function (result) {
            me.createView(vehicle, result.checklist);
        });
    },

    /*
     * fields = 
    */
    createView: function (vehicle, checklist) {
        this.removeAll();

        this.createGroup([
            this.createContainer('Project number', vehicle.get('projectNumber')),
            this.createContainer('Project name', vehicle.get('projectName')),
            this.createContainer('Operators name', ''), // TODO:
            this.createContainer('Date and Time', checklist.creationDate.date),
            this.createContainer('Location', checklist.gpsCoords)
        ]);

        this.createGroup([
            this.createContainer('Plant ID/Registration', vehicle.get('plantId')),
            this.createContainer('Type of vehicle', vehicle.get('type')),
            this.createContainer('Registration expiry', vehicle.get('registration'))
        ]);

        var serviceDueString = vehicle.get('serviceDueKm') + ' km '+ vehicle.get('serviceDueHours') + ' hours';
        this.createGroup([
            this.createContainer('Service due', serviceDueString),
            this.createContainer('Current odometer', '') // TODO:
        ]);

        this.createButtons(checklist.id);

        Ext.each(checklist.fieldsStructure, function (fieldGroup) {
            this.createFields(fieldGroup.fields, checklist.fieldsData, fieldGroup.groupName, 1);
        }, this);
    },

    createContainer: function (key, value) {
        return {
            xtype: 'container',
            cls: 'sfa-vehicle-inspection-details-container',
            layout: {
                type: 'hbox',
                align: 'left',
                pack: 'left'
            },
            width: '100%',
            maxWidth: 700,
            items: [{
                xtype: 'container',
                html: key
            }, {
                xtype: 'spacer',
                flex: 1
            }, {
                xtype: 'container',
                html: value
            }]
        };
    },

    createGroup: function (items, title, depth) {
        if (title) {
            if (depth == 1) {
                items.unshift({
                    xtype: 'container',
                    cls: 'sfa-vehicle-inspection-details-title',
                    html: title
                });
            } else {
                items.unshift({
                    xtype: 'container',
                    cls: 'sfa-vehicle-inspection-details-subtitle',
                    html: title
                });
            }
        }
        var item = {
            xtype: 'container',
            margin: '0 0 10 0',
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'middle'
            },
            width: '100%',
            items: items
        };
        if (depth > 1) {
            return item;
        }
        this.add(item);
    },

    createFields: function (fields, values, title, depth) {
        var items = [];
        Ext.each(fields, function (field) {
            switch (field.type) {
                case 'group':
                    items.push(this.createFields(field.items, values, field.fieldName, depth + 1));
                    break;
                case 'radio':
                case 'text':
                case 'checkbox':
                    Ext.each(values, function (value) {
                        if (value.id == field.fieldId) {
                            items.push(this.createContainer(field.fieldName, value.value));
                        }
                    }, this);
                    break;
                case 'datePicker':
                    Ext.each(values, function (value) {
                        if (value.id == field.fieldId) {
                            var date = new Date(value.value);
                            items.push(this.createContainer(field.fieldName, Ext.Date.format(date, 'Y-m-d H:i:s')));
                        }
                    }, this);
                    break;
            }
        }, this);

        if (items.length) {
            return this.createGroup(items, title, depth);
        }
    },

    createButtons: function (checkListId) {
        this.add({
            xtype: 'toolbar',
            margin: '0 0 10 0',
            items: [{
                ui: 'confirm',
                action: 'print',
                checkListId: checkListId,
                text: 'Print'
            }]
        });
    }

});
