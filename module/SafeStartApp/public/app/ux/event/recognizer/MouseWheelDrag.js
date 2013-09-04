Ext.define('Ext.ux.event.recognizer.MouseWheelDrag', {
    extend: 'Ext.event.recognizer.Touch',
 
    requires: ['Ext.event.Dom'],
 
    handledEvents: ['dragstart', 'drag', 'dragend'],
 
    constructor: function() {
        this.callParent(arguments);
 
        this.onMouseWheel = Ext.Function.bind(this.onMouseWheel, this);
        this.fireDragEnd = Ext.Function.bind(this.fireDragEnd, this);
 
        document.addEventListener('mousewheel', this.onMouseWheel, true);
    },
 
    onMouseWheel: function(e) {
        var helper = Ext.event.publisher.Dom.prototype,
            target = helper.getElementTarget(e.target),
            targets = helper.getBubblingTargets(target),
            deltaX = e.hasOwnProperty('wheelDeltaX') ? e.wheelDeltaX : e.wheelDelta,
            deltaY = e.hasOwnProperty('wheelDeltaY') ? e.wheelDeltaY : e.wheelDelta,
            touches = [
                {
                    targets: targets
                }
            ],
            lastPoint, time;
 
        e = new Ext.event.Dom(e);
        time = e.time;
 
        this.lastEvent = e;
        this.lastTouches = touches;
 
        if (!this.startPoint) {
            this.startTime = time;
            this.startPoint = lastPoint = {
                x: e.pageX,
                y: e.pageY
            };
 
            this.previousPoint = lastPoint;
            this.previousTime = time;
 
            this.lastPoint = lastPoint;
            this.lastTime = time;
 
            this.fire('dragstart', e, touches, this.getInfo(e));
        }
 
        lastPoint = this.lastPoint;
 
        this.previousTime = this.lastTime;
        this.previousPoint = lastPoint;
 
        this.lastPoint = {
            x: lastPoint.x + deltaX,
            y: lastPoint.y + deltaY
        };
        this.lastTime = time;
 
        this.fire('drag', e, touches, this.getInfo(e));
 
        clearTimeout(this.dragEndTimer);
        this.dragEndTimer = setTimeout(this.fireDragEnd, 50);
    },
 
    fireDragEnd: function() {
        var e = this.lastEvent;
 
        this.fire('dragend', e, this.lastTouches, this.getInfo(e));
 
        this.startTime = 0;
        this.previousTime = 0;
        this.lastTime = 0;
 
        this.startPoint = null;
        this.previousPoint = null;
        this.lastPoint = null;
        this.lastMoveEvent = null;
        this.lastEvent = null;
        this.lastTouches = null;
    },
 
    getInfo: function(e, touch) {
 
        var slowCoefficient = .14,
            time = e.time,
            startPoint = this.startPoint,
            previousPoint = this.previousPoint,
            startTime = this.startTime,
            previousTime = this.previousTime,
            point = this.lastPoint,
            deltaX = (point.x - startPoint.x) * slowCoefficient,
            deltaY = (point.y - startPoint.y) * slowCoefficient,
            previousDeltaX = (point.x - previousPoint.x) * slowCoefficient,
            previousDeltaY = (point.y - previousPoint.y) * slowCoefficient;
 
        // Adjust points to lowered deltas
        point.x += deltaX;
        point.y += deltaY;
        previousPoint.x += previousDeltaX;
        previousPoint.y += previousDeltaY;
 
        return {
            flick: {
                velocity: {
                    x: 0,
                    y: 0
                }
            },
            touch: touch,
            startX: startPoint.x,
            startY: startPoint.y,
            previousX: previousPoint.x,
            previousY: previousPoint.y,
            pageX: point.x,
            pageY: point.y,
            deltaX: deltaX,
            deltaY: deltaY,
            absDeltaX: Math.abs(deltaX),
            absDeltaY: Math.abs(deltaY),
            previousDeltaX: previousDeltaX,
            previousDeltaY: previousDeltaY,
            time: time,
            startTime: startTime,
            previousTime: previousTime,
            deltaTime: time - startTime,
            previousDeltaTime: time - previousTime
        };
 
    }
});