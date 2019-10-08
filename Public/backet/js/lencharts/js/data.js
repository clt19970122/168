
$(function () {
    var obj = {
        id:001,
        data:{
            name:'特区建发',
            plait:20,
            onduty:10,
            type:"organization",
            Type:'1',
        },
        children:[
            {
                id: 001,
                data: {
                    name: '总经理',
                    person: '陈志明',
                    type: "position"
                    // Type: '1',
                },
                children: [
                    {
                        id: 001,
                        data: {
                            name: '特区建发',
                            plait: 20,
                            onduty: 10,
                            type: "organization",
                            Type: '1'
                        },
                        children: [

                        ]
                    }, {
                        id: 001,
                        data: {
                            name: '特区建发',
                            plait: 20,
                            onduty: 10,
                            type: "organization"

                        },
                        children: [

                        ]
                    },
                ]
            },
            {
                id: 001,
                data: {
                    name: '特区建发',
                    plait: 20,
                    onduty: 10,
                    type: "organization"
                },
                children: [
                    {
                        id: 001,
                        data: {
                            name: '特区建发',
                            plait: 20,
                            onduty: 10,
                            type: "organization"
                        },
                        children: [
                            {
                                id: 001,
                                data: {
                                    name: '特区建发',
                                    plait: 20,
                                    onduty: 10,
                                    type: "organization"
                                },
                                children: [

                                ]
                            }, {
                                id: 001,
                                data: {
                                    name: '特区建发',
                                    plait: 20,
                                    onduty: 10,
                                    type: "organization"
                                },
                                children: [

                                ]
                            },
                        ]
                    },
                    {
                        id: 001,
                        data: {
                            name: '特区建发',
                            plait: 20,
                            onduty: 10,
                            type: "organization"
                        },
                        children: [
                            {
                                id: 001,
                                data: {
                                    name: '特区建发',
                                    plait: 20,
                                    onduty: 10,
                                    type: "organization"
                                },
                                children: [

                                ]
                            }, {
                                id: 001,
                                data: {
                                    name: '特区建发',
                                    plait: 20,
                                    onduty: 10,
                                    type: "organization"
                                },
                                children: [

                                ]
                            },
                        ]
                    }
                ]
            },
            {
                id: 001,
                data: {
                    name: '特区建发',
                    plait: 20,
                    onduty: 10,
                    type: "organization"
                },
                children: [
                    {
                        id: 001,
                        data: {
                            name: '特区建发',
                            plait: 20,
                            onduty: 10,
                            type: "organization"
                        },
                        children: [

                        ]
                    },
                ]
            },
        ]
    }

    $('.orgWrap').lenChart({
        data:obj,
        drag:true,
        renderdata:function(data,$dom){
            var value = data.data;
            if (value && Object.keys(value).length) {
                var $name = $('<div class="name"></div>')
                !!(value.name) && $name.text(value.name);
                $dom.append($name)
                //组织
                if (value.type && value.type === 'organization') {
                    //编制
                    var $plait = $('<div><span>编制:</span><span class="num"></span></div>')
                    !!(value.plait) && $plait.find('.num').text(value.plait)
                    //在职
                    var $onduty = $('<div><span>在职:</span><span class="num"></span></div>')
                    !!(value.onduty) && $onduty.find('.num').text(value.onduty)
                    //缺编
                    var $short = $('<div><span>缺编:</span><span class="num"></span></div>')
                    if (value.plait && value.onduty) {
                        $short.find('.num').text(value.plait - value.onduty)
                    }
                    var $wrap = $('<div class="numWrap"></div>')
                    $wrap.append($plait, $onduty, $short);
                    $dom.addClass('organization')
                    $dom.append($wrap)
                } else {
                    //岗位
                    var $person = $('<div class="person"></div>');
                    !!(value.person) && $person.text(value.person)
                    $dom.addClass('position')
                    $dom.append($person)
                }
                var icon = '<i class="icon plusMinus icon-minus-sign"></i>';
                var $icon = $(icon);
                if (data.children && data.children.length) {
                    $dom.append($icon);
                }

            }
        },
        callback:function(){
        }
    })

})