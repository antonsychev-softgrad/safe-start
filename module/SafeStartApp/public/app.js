//<debug>
Ext.Loader.setPath({
    'Ext': 'touch/src',
    'Ext.ux': 'app/ux'
});
//</debug>
SafeStartApp = SafeStartApp || {
    version: "1.0",

    baseHref: "http://safe-start.dev/api/",

    defMenu: [
        'Auth',
        'Contact'
    ]

};

(function () {
    var defaultConfig = {
        dateFormat: 'd/m/Y',
        timeFormat: 'H:i',
        logoRedirectUrl: 'http://safestartinspections.com/'
    };
    Ext.Object.each(defaultConfig, function (property, value) {
        if (! SafeStartApp.hasOwnProperty(property)) {
            SafeStartApp[property] = value;
        }
    });

}());

Ext.apply(SafeStartApp, {
    userModel: {},
    companyModel: {},
    mainMenuLoaded: false,
    fnErrorCodes: [4006],
    AJAX: function (url, data, successCalBack, failureCalBack, silent) {
        var self = this;
        var meta = {
            requestId: 'WEB_' + this.getHash(12)
        };
        data = data || {test: 1};
        if (!silent) {
            Ext.Viewport.setMasked({xtype: 'loadmask', zIndex: 20, message: 'Please wait! Working...'});
        }
        Ext.Ajax.request({
            url: this.baseHref + url,
            params: Ext.encode({
                meta: meta,
                data: data
            }),
            success: function (response) {
                if (!silent) Ext.Viewport.setMasked(false);
                var result = Ext.decode(response.responseText);
                if (result.meta && result.meta.status == 200 && !parseInt(result.meta.errorCode)) {
                    if (successCalBack && typeof successCalBack == 'function') successCalBack(result.data || {});
                } else {
                    if (!self.useErrorFn(parseInt(result.meta.errorCode))) {
                        self.showRequestFailureInfoMsg(result, failureCalBack);
                    } else {
                        self.processOtherFailure(result, failureCalBack);
                    }
                }
            },
            failure: function (response) {
                Ext.Viewport.setMasked(false);
                self.showRequestFailureInfoMsg(Ext.decode(response.responseText), failureCalBack);
            }
        });
    },

    useErrorFn: function(code) {
        var use = (-1 != this.fnErrorCodes.indexOf(code)) ? true: false;
        return use;
    },

    processOtherFailure: function(result, failureCalBack) {

        var func = Ext.emptyFn();
        if (failureCalBack && typeof failureCalBack == 'function') func = failureCalBack;

        if (result.meta && result.meta.status == 200 && parseInt(result.meta.errorCode, 10)) {
            var errorCode = result.meta.errorCode;
            switch (errorCode) {
                case 4006:
                    Ext.Msg.show({
                        title: 'You Have Reached Your Subscription Limit!',
                        message: 'If you would like to add more vehicles to your Safe Start account please purchase additional vehicle subscriptions.',
                        buttons: //Ext.MessageBox.YESNO,
                            [
                                {text: 'Buy Now', itemId: 'yes', ui: 'action'},
                                {text: 'No Thanks', itemId: 'no'}
                            ],
                        fn: function (btn) {
                            if (btn === 'yes') {
                                window.open('http://safestartinspections.com/pricing/','_blank');
                            }
                            return;
                        }
                    });
                    break;
                default:
                    break;
            }
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

    showRequestFailureInfoMsg: function (result, failureCalBack) {
        var func = Ext.emptyFn();
        if (failureCalBack && typeof failureCalBack == 'function') func = failureCalBack;
        var errorMessage = '';
        if (result.data && result.data.errorMessage) errorMessage = result.data.errorMessage;
        this.showFailureInfoMsg(errorMessage, func);
    },

    showFailureInfoMsg: function (msg, failureCalBack) {
        msg = msg || 'Operation filed';
        Ext.Msg.alert("Server response error", msg, failureCalBack);
    },

    showInfoMsg: function (msg) {
        msg = msg || 'Info message'
        Ext.Msg.alert("Info", msg, Ext.emptyFn());
    },

    loadMainMenu: function () {
        this.AJAX('web-panel/getMainMenu', {}, function (result) {
            SafeStartApp.userModel.setData(result.userInfo || {});
            if (SafeStartApp.userModel.getAssociatedData().company)  SafeStartApp.companyModel.setData(SafeStartApp.userModel.getAssociatedData().company);
            SafeStartApp.setViewPort(result.mainMenu || null);
            SafeStartApp.mainMenuLoaded = true;
            Ext.Viewport.fireEvent('mainMenuLoaded');
        });
    },

    setViewPort: function (menu) {
        Ext.Viewport.removeAll(true);

        this.currentMenu = [];

        Ext.each(menu || this.defMenu, function (item) {
            SafeStartApp.currentMenu.push(
                {
                    xclass: 'SafeStartApp.view.pages.' + item
                }
            )
        }, this);

        Ext.define('SafeStartApp.view.ViewPort', {
            extend: 'SafeStartApp.view.Main',
            xtype: 'SafeStartViewPort',
            config: {
                tabBarPosition: 'bottom',
                items: SafeStartApp.currentMenu
            }
        });

        Ext.Viewport.add({ xtype: 'SafeStartViewPort' });
    },

    colors: ["#115fa6", "#94ae0a", "#a61120", "#ff8809", "#ffd13e", "#a61187", "#24ad9a", "#7c7474", "#a66111"],

    getBaseColors: function (index) {
        if (index == null) {
            return this.colors.slice();
        } else {
            return this.colors[index];
        }
    },

    logException: function (e) {
        if (typeof console === 'object') {
            console.error(e.name + ': ' +  e.message);
        }
        if (typeof qbaka === 'object') {
            qbaka.reportException(e);    
        }
    },

    logMessage: function (message) {
        if (typeof console === 'object') {
            console.error(message);
        }
        if (typeof qbaka === 'object') {
            qbaka.report(message);    
        }
    }
});

Ext.application({
    name: 'SafeStartApp',

    requires: [
        'Ext.MessageBox',
        'Ext.ux.event.recognizer.MouseWheelDrag',
        'Ext.event.recognizer.Drag',
        'Ext.event.recognizer.Tap',
        'Ext.event.recognizer.DoubleTap',
        'Ext.event.recognizer.SingleTouch',
        'Ext.event.recognizer.MultiTouch',
        'Ext.event.recognizer.Touch',
        'Ext.event.recognizer.LongPress',
        'Ext.event.recognizer.Swipe',
        'Ext.event.recognizer.Pinch',
        'Ext.event.recognizer.Rotate',
        'Ext.event.recognizer.EdgeSwipe',
        'Ext.event.publisher.TouchGesture',
        'Ext.event.publisher.ComponentDelegation',
        'Ext.event.publisher.ComponentPaint',
        'Ext.event.publisher.ElementPaint',
        'Ext.event.publisher.ElementSize',
        'Ext.util.PaintMonitor',
        'Ext.util.paintmonitor.CssAnimation',
        'Ext.util.paintmonitor.OverflowChange',
        'Ext.util.paintmonitor.Abstract',
        'Ext.util.SizeMonitor',
        'Ext.util.sizemonitor.Default',
        'Ext.util.sizemonitor.Scroll',
        'Ext.util.sizemonitor.OverflowChange',
        'Ext.util.sizemonitor.Abstract',
        'Ext.mixin.Templatable',
        'Ext.chart.series.ItemPublisher'
    ],

    views: [
        'Main',
        'pages.Auth',
        'pages.Contact',
        'pages.Companies',
        'pages.Company',
        'pages.Users',
        'pages.Alerts',
        'pages.SystemSettings',
        'pages.CompanySettings',
        'pages.SystemStatistic'
    ],

    controllers: [
        'Main',
        'Auth',
        'Contact',
        'Companies',
        'Users',
        'Company',
        'CompanyVehicles'
    ],

    icon: {
        '57': 'resources/icons/Icon.png',
        '72': 'resources/icons/Icon~ipad.png',
        '114': 'resources/icons/Icon@2x.png',
        '144': 'resources/icons/Icon~ipad@2x.png'
    },

    isIconPrecomposed: true,

    startupImage: {
        '320x460': 'resources/startup/320x460.jpg',
        '640x920': 'resources/startup/640x920.png',
        '768x1004': 'resources/startup/768x1004.png',
        '748x1024': 'resources/startup/748x1024.png',
        '1536x2008': 'resources/startup/1536x2008.png',
        '1496x2048': 'resources/startup/1496x2048.png'
    },

    eventPublishers: {
        touchGesture: {
            recognizers: {
                mousewheeldrag: {
                    xclass: 'Ext.ux.event.recognizer.MouseWheelDrag'
                }
            }
        }
    },

    launch: function () {
        var self = this;

        if (Ext.Viewport.self.getName() === 'Ext.viewport.Default') {
            Ext.Viewport.applyAutoBlurInput(false);
            Ext.Viewport.doBlurInput = Ext.Function.bind(function (e) {
                var target = e.target,
                    focusedElement = this.focusedElement;
                if (focusedElement && focusedElement.nodeName.toUpperCase() != "BODY" && !this.isInputRegex.test(target.tagName)) {
                    delete this.focusedElement;
                    if (typeof focusedElement == 'object' && typeof focusedElement.blur == 'function') {
                        focusedElement.blur();
                    }
                }
            }, Ext.Viewport);
            var clickEvent = (Ext.feature.has.Touch) ? "touchstart" : "mousedown";
            Ext.Viewport.addWindowListener(clickEvent, Ext.Viewport.doBlurInput, false)
        }

        if (Ext.os.deviceType == 'Desktop') {
            Ext.override(Ext.scroll.View, {
                doHideIndicators: function() {
                    return this.changeIndicatorsState();
                },

                showIndicators: function () {
                    return this.changeIndicatorsState();
                },

                changeIndicatorsState: function () {
                    var indicators = this.getIndicators();

                    if (this.hasOwnProperty('indicatorsHidingTimer')) {
                        clearTimeout(this.indicatorsHidingTimer);
                        delete this.indicatorsHidingTimer;
                    }
                    if (this.isAxisEnabled('x')) {
                        if (indicators.x._length != indicators.x.barLength) {
                            indicators.x.show();
                        } else {
                            indicators.x.hide();
                        }
                    }
                    if (this.isAxisEnabled('y')) {
                        if (indicators.y._length != indicators.y.barLength) {
                            indicators.y.show();
                        } else {
                            indicators.y.hide();
                        }
                    }
                }
            });

            Ext.override(Ext.behavior.Scrollable, {
                onComponentPainted: function (component) {
                    var me = this;
                    this.scrollView.getScroller().on('maxpositionchange', function () {
                        setTimeout( function () {
                            me.scrollView.showIndicators();
                        });
                    });
                    me.scrollView.showIndicators();
                }
            });
        }

        // Destroy the #appLoadingIndicator element
        Ext.fly('appLoadingIndicator').destroy();

        // Load current user menu and update view port
        SafeStartApp.userModel = Ext.create('SafeStartApp.model.User');
        SafeStartApp.companyModel = Ext.create('SafeStartApp.model.Company');
        SafeStartApp.loadMainMenu();
    },

    onUpdated: function () {
        Ext.Msg.confirm(
            "Application Update",
            "This application has just successfully been updated to the latest version. Reload now?",
            function (buttonId) {
                if (buttonId === 'yes') {
                    window.location.reload();
                }
            }
        );
    }
});
