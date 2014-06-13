Ext.define('SafeStartExt.view.panel.InspectionInfo', {
    extend: 'Ext.panel.Panel',
    requires: [
    ],
    xtype: 'SafeStartExtPanelInspectionInfo',
    cls:'sfa-previous-inspections-info',
    autoScroll: true,
    border: 0,

    initComponent: function () {
        this.callParent();
    },

    setInspectionInfo: function (inspection, data) {
        var infoGroup = [],
            vehicle = this.vehicle,
            checklist = data.checklist;

        data = [];

        infoGroup.push(
            this.createContainer('Project number', vehicle.get('projectNumber')),
            this.createContainer('Project name', vehicle.get('projectName'))
        );

        var inspectionDate = Ext.Date.format(
            Ext.Date.parse(checklist.creationDate.date, 'Y-m-d H:i:s'), 
            SafeStartExt.dateFormat + ' ' + SafeStartExt.timeFormat
        );
        infoGroup.push(this.createContainer('Date and Time', inspectionDate));

        data.push(this.createGroup([
            this.createContainer('Plant ID', vehicle.get('plantId')),
            this.createContainer('Type of vehicle', vehicle.get('type'))
        ]));

        var serviceDueString = vehicle.get('serviceDueKm') + ' km '+ vehicle.get('serviceDueHours') + ' hours';
        var odometerString = '';
        if (inspection.get('odometer_kms') > 0) {
            odometerString += inspection.get('odometer_kms') + ' km';
        }
        if (inspection.get('odometer_hours') > 0) {
            odometerString += ' ' + inspection.get('odometer_hours') + ' hours';
        }
        data.push(this.createGroup([
            this.createContainer('Service due', serviceDueString),
            this.createContainer('Current odometer', odometerString)
        ]));

        Ext.each(inspection.get('warnings'), function (warning) {
            if (Ext.isString(warning.text)) {
                data.push(this.createGroup([], warning.text, 1, 'warning'));
            } else {
                switch (warning.action) {
                    case 'date_incorrect':
                        data.push(this.createGroup([], 'Inaccurate Current Hours Or Kms', 1, 'warning'));
                        break;
                    case 'service_due':
                        data.push(this.createGroup([], 'Due For Service', 1, 'warning'));
                        break;
                    default:
                        break;
                }
            }
        }, this);

        this.checkListId = checklist.id;
        this.vehicleId = vehicle.get('id');
        this.inspectionRecord = inspection;

        Ext.each(checklist.fieldsStructure, function (fieldGroup) {
            var group = this.createFields(
                fieldGroup.fields, 
                checklist.fieldsData, 
                fieldGroup.triggerValue, 
                fieldGroup.groupName, 
                1
            );
            if (group) {
                data.push(group);
            }
        }, this);

        this.removeAll();
        this.add({
            xtype: 'component',
            border: 0,
            layout: 'fit',
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                    '<div class="sfa-group" style="max-width: 700px;">',
                        '<div class="sfa-group-title sfa-group-title-{type}" style="margin-top: 5px; text-align: center">',
                            '{title}',
                        '</div>',
                        '<div class="sfa-group-content">',
                            '<tpl for="items">',
                                '<tpl if="Ext.isArray(items)">',
                                    '{[ this.recurse(values)]}',
                                '<tpl else>',
                                    '<div class="sfa-group-item sfa-group-item-{type}">',
                                        '<div class="sfa-group-item-key" style="display: inline-block; width: 80%">',
                                            '{key}',
                                        '</div>',
                                        '<div class="sfa-group-item-key" style="text-align: right;display: inline-block; width: 20%">',
                                            '<tpl if="Ext.isEmpty(value)">',
                                                'N/A',
                                            '<tpl else>',
                                                '{value}',
                                            '</tpl>',
                                        '</div>',                                
                                    '</div>',
                                '</tpl>',
                            '</tpl>',
                        '</div>',
                    '</div>',
                '</tpl>',
                {
                    recurse: function (values) {
                        return this.apply(values);
                    }
                }
            ),
            data: data
        });
    },

    createContainer: function (key, value, alert) {
        return {
            type: alert ? 'warning' : 'normal',
            items: null,
            key: key,
            value: value
        };
    },

    createGroup: function (items, title, depth, type) {
        return {
            type: Ext.isString(type) ? type : (depth === 1 ? 'top' : 'sub'),
            title: title,
            items: items
        };
    },

    createFields: function (fields, values, triggerValue, title, depth) {
        var items = [];
        Ext.each(fields, function (field) {
            var isAlert = false;
            switch (field.type) {
                case 'group':
                    var group = this.createFields(field.items, values, field.triggerValue, field.fieldName, depth + 1);
                    if (group) {
                        items.push(group); 
                    }
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
                                    items.push(this.createContainer(field.fieldName, Ext.Date.format(date, SafeStartExt.dateFormat)));
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
    }

});
