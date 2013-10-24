Ext.define('SafeStartApp.model.Alert', {
    extend: "Ext.data.Model",
    config: {
        fields: [
            {name: 'id', type: 'int', defaultValue: 0},
            {name: 'title', type: 'string'},
            {name: 'description', type: 'string'},
            {name: 'creationDate', mapping: 'creation_date', type: 'auto', convert: function (v, model) {
                var date = new Date(v * 1000);
                return Ext.Date.format(date, SafeStartApp.dateFormat + ' ' + SafeStartApp.timeFormat);
            }},
            {name: 'alertDescription', mapping: 'alert_description', type: 'string'},
            {name: 'thumbnail', type: 'string'},
            {name: 'images', type: 'auto', defaultValue: []},
            {name: 'status', type: 'string', defaultValue: 'new'},
            {name: 'refreshedTimes', mapping: 'refreshed_times', type: 'int', defaultValue: 0},
            {name: 'history', type: 'auto',  convert: function (v, model) {
                if (! (v && v.length)) {
                    return [];
                }
                return v;
            }},
            {name: 'vehicle'}
        ],
        associations: [
            {type: 'hasMany', model: 'SafeStartApp.model.Comment', name: 'comments'},
            {type: 'hasOne', model: 'SafeStartApp.model.User', name: 'user'},
            {type: 'hasOne', model: 'SafeStartApp.model.Vehicle', name: 'vehicle'}
        ]
    }
});
