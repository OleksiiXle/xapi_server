var collect={
    runner:[],
    counter:0,
    def : null,
    countDone:function(){--collect.counter; if (collect.counter == 0) collect.def.resolve();},
    run:function(setup){
        this.def = jQuery.Deferred();
        this.counter = setup.length;//Object.keys(setup).length;
        $.each(setup,function(index,obj){
            //console.log(typeof(obj));
            if (typeof(obj) == 'object')
                collect.runner.push($.get.apply($.get,obj).done(collect.countDone)); else
            if (typeof(obj) == 'function')
                collect.countDone();
        });
        return this.def.promise();
    },
    clear:function (){
        this.runner = [];
        this.def = null;
    }
};

function initTrees() {
    var widgets = $(".xtree");
    var arr =[];
    widgets.each(function () {
        var treeName = this.id;
        window[treeName] = Object.create(MENU_TREE);
        var qq = window[treeName];
        arr.push(qq.init(treeName));
    });
    collect.run(arr);

}

function clickItemFunction(tree_id, selected_id) {
    // alert('clickItemFunction ' + tree_id + ' ' + selected_id);
    $.ajax({
        url: '/adminx/menux/get-menux',
        type: "GET",
        data: {
            'id' : selected_id
        },
        beforeSend: function() {
            preloader('show', 'mainContainer', 0);
        },
        complete: function(){
            preloader('hide', 'mainContainer', 0);
        },
        success: function(response){
            $("#menuInfo").html(response);
        },
        error: function (jqXHR, error, errorThrown) {
            console.log(error);
            console.log(errorThrown);
            console.log(jqXHR);
            //   errorHandler(jqXHR, error, errorThrown);
        }
    });

}


