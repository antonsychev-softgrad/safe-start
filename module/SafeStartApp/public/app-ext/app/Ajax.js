Ext.define('SafeStartExt.Ajax', {    
    extend: 'Ext.data.Connection',

    singleton: true,

    baseHref: "/api/",
    fnErrorCodes: [4006],

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
        url += '';
        options.url = '';
        if (url[0] !== '/') {
            options.url += this.baseHref;
        }
        options.url += url;
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
                if (typeof failureCallback === 'function') {
                    failureCallback.bind(this, result);
                }
                if (!me.useErrorFn(parseInt(result.meta.errorCode, 10))) {
                    me.processFailure((result.data && result.data.errorMessage) || 'Operation failed', failureCallback);
                } else {
                    me.processOtherFailure(result, failureCallback);
                }
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

            if (typeof failureCallback === 'function') {
                failureCallback.bind(this, result);
            }
            me.processOtherFailure(result, failureCallback);
        };

        this.callParent([options]);
    },

    useErrorFn: function(code) {
        var use = (-1 != this.fnErrorCodes.indexOf(code)) ? true: false;
        return use;
    },

    processOtherFailure: function(result, callback) {
        if (result.meta && parseInt(result.meta.errorCode, 10)) {
            var errorCode = result.meta.errorCode || result.meta.status;
            if(result.meta.status == 200) {
                switch (errorCode) {
                    case 4006:
                        Ext.Msg.show({
                            title:'You Have Reached Your Subscription Limit!',
                            msg: 'If you would like to add more vehicles to your Safe Start account please purchase additional vehicle subscriptions.',
                            buttons: Ext.Msg.YESNO,
                            buttonText:{
                                yes: 'Buy Now',
                                no: 'No Thanks'
                            },
                            fn: function(btn) {
                                if(btn === 'yes') {
                                    window.open('http://safestartinspections.com/pricing/','_blank');
                                }
                            }
                        });
                        break;
                    default:
                        break;
                }
            } else {
                switch (errorCode) {
                    case 401:
                        window.location.reload(true);
                        break;
                    default:
                        break;
                }
            }
        }
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
        var i, randomPoz;

        for (i = 0; i < len; i++) {
            randomPoz = Math.floor(Math.random() * charSet.length);
            randomString += charSet.substring(randomPoz, randomPoz + 1);
        }
        return randomString;
    }, 
    
    getViewport: function () {
        return SafeStartExt.getApplication().getViewport();
    }
});
