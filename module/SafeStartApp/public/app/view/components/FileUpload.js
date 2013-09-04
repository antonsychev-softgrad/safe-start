Ext.define('SafeStartApp.view.components.FileUpload', {
    extend: 'Ext.ux.Fileup',
    alias: 'widget.imageupload',
    
    doUpload: function(file) {
        var me = this;        
        var http = new XMLHttpRequest();
        
        if (http.upload && http.upload.addEventListener) {
            
            http.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    var percentComplete = (e.loaded / e.total) * 100; 
                    me.setBadgeText(percentComplete.toFixed(0) + '%');
                }
            };
            
            http.onreadystatechange = function (e) {
                if (this.readyState === 4) {
                    var response = me.decodeResponse(this);
                    if(Ext.Array.indexOf(me.getDefaultSuccessCodes(), parseInt(this.status, 10)) !== -1 ) {
                        if (response && response.meta && response.meta.status == 200 && !parseInt(response.meta.errorCode, 10)) {
                            me.fireEvent('success', me, response.data, this, e);
                        } else {
                            me.fireEvent('failure', 'Unknown error', response, this, e);
                        }
                    } else {
                        me.fireEvent('failure', me, this.status + ' ' + this.statusText, {}, this, e);
                    }
                    me.changeState('browse');
                }
            };
            
            http.upload.onerror = function(e) {
                me.fireEvent('failure', me, this.status + ' ' + this.statusText, {}, this, e);
            };
        }
        
        http.open('POST', me.getUrl());
        
        if (me.getSignRequestEnabled()) {
            me.signRequest(http, function(http) {
              http.send(me.getForm(file));
            });
        } else {
            http.send(me.getForm(file));
        }
    },
    
});