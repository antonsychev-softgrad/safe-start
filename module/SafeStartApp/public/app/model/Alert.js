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
            {name: 'alert_description', type: 'string'},
            {name: 'thumbnail', type: 'string'},
            {name: 'images', type: 'auto', defaultValue: []},
            {name: 'status', type: 'string', defaultValue: 'new'},
            {name: 'vehicle'}
        ],
        associations: [
            {type: 'hasMany', model: 'SafeStartApp.model.Comment', name: 'comments'},
            {type: 'hasOne', model: 'SafeStartApp.model.User', name: 'user'},
            {type: 'hasOne', model: 'SafeStartApp.model.Vehicle', name: 'vehicle'}
        ]
    }
});
