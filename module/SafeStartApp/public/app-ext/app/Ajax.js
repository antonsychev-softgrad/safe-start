Ext.define('SafeStartExt.Ajax', {    
    extend: 'Ext.data.Connection',

    singleton: true,

    baseHref: "/api/",


    request: function(options) {
        var me = this,
            url = options.url,
            data = options.data || {},
            meta = {},
            successCallback = options.success,
            failureCallback = options.failure,
            silent = options.silent;

        meta.requestId = 'WEB_' + this.getHash(12);

        if (! silent) {
            this.getViewport().setLoading(true);
        }
        options.url = this.baseHref + url;
        options.method = options.method || 'POST';
        options.params = Ext.encode({
            meta: meta,
            data: data
        });

        options.success = function (res) {
            var result;
            if (! silent) {
                me.getViewport().setLoading(false);
            }
            try {
                result = Ext.decode(res.responseText);
            } catch (e) {
                me.processFailure('Operation failed', failureCallback);
                return;
            }

            if (result.meta && result.meta.status == 200 && !parseInt(result.meta.errorCode, 10)) {
                if (typeof successCallback === 'function') {
                    successCallback(result.data || {});
                }
            } else {
                me.processFailure((result.data && result.data.errorMessage) || 'Operation failed', failureCallback);
            }
        };

        options.failure = function (res) {
            var result;
            if (! silent) {
                me.getViewport().setLoading(false);
            }
            try {
                result = Ext.decode(res.responseText);
            } catch (e) {
                me.processFailure('Operation failed', failureCallback);
                return;
            }
            me.processFailure((result.data && result.data.errorMessage) || 'Operation failed', failureCallback);
        };

        this.callParent([options]);
    },

    processFailure: function (message, callback) {
        Ext.Msg.alert({
            title: 'Operation failed',
            msg: message,
            buttons: Ext.Msg.OK
        });
        if (typeof callback === 'function') {
            callback();
        }
    },

    getHash: function (len, charSet) {
        len = len || 12;
        charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var randomString = '';
        for (var i = 0; i < len; i++) {
            var randomPoz = Math.floor(Math.random() * charSet.length);
            randomString += charSet.substring(randomPoz, randomPoz + 1);
        }
        return randomString;
    }, 
    
    getViewport: function () {
        return SafeStartExt.getApplication().getViewport();        
    }
});