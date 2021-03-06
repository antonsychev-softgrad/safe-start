Ext.define('SafeStartApp.view.pages.panel.VehicleInspectionDetails', {
    extend: 'Ext.Panel',

    alias: 'widget.SafeStartVehicleInspectionDetails',

    requires: [

    ],

    warningMessages: {
        date_discrepancy_kms: 'Discrepancy Of Current Kms',
        date_incorrect: 'Inaccurate Current Hours Or Kms',
        date_discrepancy_hours: 'Discrepancy Of Current Hours'
    },

    config: {
        layout: 'card',
        margin: 10,
        items: [{
            xtype: 'panel',
            name: 'vehicle-inspection-details',
            cls: 'sfa-vehicle-inspection-details',
            scrollable: true,
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'middle'
            }
        }, {
            xtype: 'panel',
            cls: 'sfa-vehicle-inspection-details-map', 
            layout: 'fit',
            items: [{
                xtype: 'button',
                top: 20,
                left: 80,
                text: 'Back',
                handler: function (btn) {
                    var panel = btn.up('SafeStartVehicleInspectionDetails');
                    panel.setActiveItem(0);
                }
            }]
        }]
    },

    initialize: function () {
        this.callParent();
    },

    loadChecklist: function (vehicle, inspection) {
        var me = this;
        SafeStartApp.AJAX('vehicle/' + inspection.get('checkListId') + '/getchecklistdata', {}, function (result) {
            me.createView(vehicle, result.checklist, inspection);
        });
    },

    createView: function (vehicle, checklist, inspection) {
        var infoGroup = [],
            responsibleUser = vehicle.responsibleUsers().first(),
            cords,
            warnings = inspection.get('warnings') || [];

        this.down('panel[cls=sfa-vehicle-inspection-details]').removeAll();

        infoGroup.push(
            this.createContainer('Project number', vehicle.get('projectNumber') == '0' ? '-': vehicle.get('projectNumber')),
            this.createContainer('Project name', vehicle.get('projectName'))
        );
        if (inspection.get('operator_name')) {
            infoGroup.push(this.createContainer('Operator name', inspection.get('operator_name')));
        }


        // fix miliseconds
        var tempCreationDate = checklist.creationDate.date;
        var re = new RegExp(/\.\d+$/gi);
        var match = tempCreationDate.match(re);
        if(match !== null) {
            tempCreationDate = tempCreationDate.replace(re, '');
        }

        var inspectionDate = Ext.Date.format(
            Ext.Date.parse(tempCreationDate, 'Y-m-d H:i:s'),
            SafeStartApp.dateFormat + ' ' + SafeStartApp.timeFormat
        );
        infoGroup.push(this.createContainer('Date and Time', inspectionDate));
        if (checklist.gpsCoords) {
            cords = checklist.gpsCoords.split(';');
            if ((parseFloat(cords[0]) && parseFloat(cords[1]))) {
                infoGroup.push(this.createMapsContainer('Location', parseFloat(cords[0]), parseFloat(cords[1])));
            }
        }
        this.createGroup(infoGroup);

        this.createGroup([
            this.createContainer('Plant ID', vehicle.get('plantId')),
            this.createContainer('Model', vehicle.get('type'))
        ]);

        var warningsGroup = [];
        Ext.each(warnings, function (warning) {
            if (warning.text) {
                warningsGroup.push(this.createMessage(warning.text, 'sfa-alert-title'));
            } else if (this.warningMessages[warning.action]) {
                warningsGroup.push(this.createMessage(this.warningMessages[warning.action], 'sfa-alert-title'));
            }
        }, this);
        if (warningsGroup.length) {
            this.createGroup(warningsGroup);
        }

        //var serviceDueString = vehicle.get('serviceDueKm') + ' km '+ vehicle.get('serviceDueHours') + ' hours';
        var serviceDueString = '';
        if (inspection.get('serviceDueKm') > 0) {
            serviceDueString += inspection.get('serviceDueKm') + ' km';
        } else {
            serviceDueString += vehicle.get('serviceDueKm') + ' km';
        }
        if (inspection.get('serviceDueHours') > 0) {
            serviceDueString += ' ' + inspection.get('serviceDueHours') + ' hours';
        }else {
            serviceDueString += ' ' + vehicle.get('serviceDueHours') + ' hours';
        }

        var odometerString = '';
        if (inspection.get('odometerKms')) {
            odometerString += inspection.get('odometerKms') + ' km';
        }
        if (inspection.get('odometerHours')) {
            odometerString += ' ' + inspection.get('odometerHours') + ' hours';
        }
        this.createGroup([
            this.createContainer('Service due', serviceDueString),
            this.createContainer('Current odometer', odometerString)
        ]);

        this.up('SafeStartVehicleInspectionsPanel').down('button[action=print]').checklistId = checklist.hash;

        this.checkListId = checklist.id;
        this.vehicleId = vehicle.get('id');
        this.inspectionRecord = inspection;

        Ext.each(checklist.fieldsStructure, function (fieldGroup) {
            this.createFields(
                fieldGroup.fields, 
                checklist.fieldsData, 
                fieldGroup.triggerValue, 
                fieldGroup.groupName, 
                1
            );
        }, this);
    },

    createContainer: function (key, value, alert) {
        var cls = alert ? 'sfa-vehicle-details-container-alert'
            : 'sfa-vehicle-details-container';
        return {
            xtype: 'container',
            cls: cls,
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

    createMessage: function (text, cls) {
        return {
            xtype: 'container',
            cls: 'sfa-vehicle-details-container-alert' + (cls ? ' '+cls : ''),
            width: '100%',
            maxWidth: 700,
            html: text
        };
    },

    createMapsContainer: function (title, lat, lon) {
        var me = this;
        return {
            xtype: 'container',
            cls: 'sfa-vehicle-inspection-details-container2',
            layout: {
                type: 'hbox',
                align: 'left',
                pack: 'left'
            },
            width: '100%',
            maxWidth: 700,
            items: [{
                xtype: 'container',
                html: title 
            }, {
                xtype: 'spacer',
                flex: 1
            }, {
                xtype: 'button',
                ui: 'small',
                text: 'Open map',
                handler: function (btn) {
                    me.up('SafeStartVehicleInspectionsPanel').fireEvent('openMap', lat, lon);
                }
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
        try {
            this.down('panel[cls=sfa-vehicle-inspection-details]').add(item);
        } catch (e) {
            SafeStartApp.logException(e);
        }

    },

    createFields: function (fields, values, triggerValue, title, depth) {
        var items = [];
        Ext.each(fields, function (field) {
            var isAlert = false;
            switch (field.type) {
                case 'group':
                    items.push(this.createFields(field.items, values, field.triggerValue, field.fieldName, depth + 1));
                    break;
                case 'radio':
                case 'checkbox':
                    Ext.each(values, function (value) {
                        isAlert = false;
                        if (value.id == field.fieldId) {
                            if (field.triggerValue && field.triggerValue == value.value) {
                                isAlert = true;
                            }

                            if (RegExp(value.value, 'i').test('yes')) {
                                value.value = 'Yes';
                            }
                            if (RegExp(value.value, 'i').test('no')) {
                                value.value = 'No';
                            }
                            if (RegExp(value.value, 'i').test('n/a')) {
                                value.value = 'N/A';
                            }

                            items.push(this.createContainer(field.fieldName, value.value, isAlert));
                        }
                    }, this);
                    break;
                case 'text':
                    Ext.each(values, function (value) {
                        if (value.id == field.fieldId) {
                            items.push(this.createContainer(field.fieldName, value.value || '-'));
                        }
                    }, this);
                    break;
                case 'datePicker':
                    Ext.each(values, function (value) {
                        if (value.id == field.fieldId) {
                            if (value.value) {
                                var date = new Date(value.value * 1000);
                                if (! isNaN( date.getTime() ) ) {
                                    items.push(this.createContainer(field.fieldName, Ext.Date.format(date, SafeStartApp.dateFormat)));
                                } else {
                                    items.push(this.createContainer(field.fieldName, 'N/A'));
                                }
                            } else {
                                items.push(this.createContainer(field.fieldName, 'N/A'));
                            }
                        }
                    }, this);
                    break;
            }
        }, this);

        if (depth == 1) {
            title = title.toUpperCase();
        }

        if (items.length) {
            return this.createGroup(items, title, depth);
        }
    },

    createButtons: function (checkListId) {
        this.down('panel[cls=sfa-vehicle-inspection-details]').add({
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
