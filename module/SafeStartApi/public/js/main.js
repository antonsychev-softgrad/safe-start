$(document).ready(function(){
    jsonParser.init();
});
var JSONParser = function(){
    this.options = {
        url: 'js/json/demo.json',
        tabLevel: 4,
        wrapperId: 'test'
    };
    this.init = function(){
        var self = this,
            $self = $(this);
        this.render(this.options.url);
        this.addEvents();


    };
    this.addEvents = function(){
        var self = this,
            $self = $(this);
        $self.on('afterRender',function(){
            console.log('event afterRender');
        })
    };
    this.render = function(url){
        var self = this,
            $self = $(this);
        var html = this.getHtml(url);
        $('#' + this.options.wrapperId).html( html );
        $self.trigger('afterRender')
    };
    this.getJSON = function(url){
        var JSON;
        $.ajax({
            dataType: "json",
            url: url,
            async: false,
            data: {},
            success: function(data){
                JSON = data
            },
            error: function(){
                JSON = false
            }
        });
        return JSON;
    };
    this.getHtml = function(url){
        this.options.tabLevel--;
        if ( this.options.tabLevel < 0 ) {
            alert('обычный способ разбора объекта')
        } else {
            console.log('создание табов');

            var json = this.getJSON(url);

            var html = '',
                tabs = '';
            for (var key in json) {
                // формирование заголовка и если есть url - загрузка через рекурсию
                if (typeof json[key] === "string" || typeof json[key] === "number" ) {
                    switch (key) {
                        case 'title': html += '<h1 class="title">'+ json[key] +'</h1>';
                            break;
                        case 'description': html += '<p class="description">'+ json[key] +'</p>';
                            break;
//                        case 'url': html += this.getHtml(json[key]);
//                            break;
                        default : html += '<div>'+ json[key] +'</div>';
                    }
                }

                // формирование табов
                if (this.options.tabLevel >= 0 && typeof json[key] === "object") {
//                    tabs += '<li><a href="#aaa">' + key + '</a></li>'
                    //for includes property:
                    for (var o in json[key]) {
                        tabs += '<li><a href="#aaa">' + o + '</a>';
                        if ( o === 'url'){
                            tabs += this.getHtml(json[key][o]) // recursion
                        }
                        tabs += '</li>'
                    }
                }
            }
            // обвернуть табы UL, если они есть и добавить в конец строки
            if (tabs !== '') { html += '<ul>' + tabs + '</ul>'}
        }
        return html;

    };

};

var jsonParser = new JSONParser();


