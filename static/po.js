$(function(){
    // 侦测 ajax
    detectAjaxRequest();

    $('.general-print-box .__controlClass__').click(
        function()
        { $(this).css({'border':'1px solid #f80;'});}
    );
    /**/
    var _arrObj = $('.general-print-box .__controlClass__').parent();
    _arrObj.attr('title','点击显示或隐藏数组内容').css({'cursor':'pointer'}).addClass('general-print-bar');
    /*mouseover mouseout*/
    _arrObj.on({
            mouseenter :function()
            {
                $(this).addClass('general-print-bar-hover');
            },
            mouseleave :function()
            {
                $(this).removeClass('general-print-bar-hover');
            },
            click     :function()
            {
                var _this = $(this);
                $(this).next('dd').slideToggle('slow',function()
                {
                    // console.log('hhhhhhhh');
                    var _arrBar = _this.find('.js-control-showOrHide');
                    if ( !(_arrBar.find('span.array-tips')[0]) ) {
                        _arrBar.append('<span class="array-tips"> &nbsp; ...点击展开隐藏的内容...</span>');
                        _this.find('span.icon-hide').removeClass('icon-hide').addClass('icon-show');
                    } else {
                        _arrBar.find('span.array-tips').remove();
                        _this.find('span.icon-show').removeClass('icon-show').addClass('icon-hide');
                    }
                });}// toggle('slow') slideToggle('slow') fadeToggle('slow');
        }
    );

    $('.general-print-pos').find('.js-general-print-switch').attr('title','点击显示或隐藏本次打印数据');

    $('.general-print-pos').on('click','.js-general-print-switch,.js-general-pos-info',function()
    {
        // console.log('fffffffffffffff');
        var _posBar = $(this).parent();
        var check = _posBar.nextAll('.general-print-pos')[0];
        if (check) {
            var allPrintBox = _posBar.nextUntil('.general-print-pos','div');
        } else {
            var allPrintBox = _posBar.nextAll('.general-print-box');
        }
        if (allPrintBox.is(':visible')) {
            allPrintBox.hide('slow');
            _posBar.find('span.general-print-tips').text('本次打印数据已隐藏,请点击此处或者右侧开关按钮显示数据。');
        } else {
            allPrintBox.show('slow');
            _posBar.find('span.general-print-tips').text('');
        }

    });
});

function detectAjaxRequest()
{
    var reallyDetectAjax = __reallyDetectAjax__;

    if (!reallyDetectAjax) {
        return;
    }

    $(document).ajaxComplete(function(event,xhr,settings) {
        var css = 'background: #FCFBF1 ;'
            +'background: -moz-linear-gradient(top,#FCFBF1 0,#D7D5C6 100%);'
            +'background: -webkit-gradient(linear,left top,left bottom,color-stop(0%,#FCFBF1),color-stop(100%,#D7D5C6));'
            +'background: -webkit-linear-gradient(top,#FCFBF1 0,#D7D5C6 100%);'
            +'background: -o-linear-gradient(top,#FCFBF1 0,#D7D5C6 100%);'
            +'background: -ms-linear-gradient(top,#FCFBF1 0,#D7D5C6 100%);'
            +'background: linear-gradient(to bottom,#FCFBF1 0,#D7D5C6 100%);'
            +'filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#FCFBF1 ", endColorstr="#207ce5", GradientType=0);';
        var _response  = xhr.responseText;
        var _datas     = _response.slice(0,-1);
        var _time     = ' | Time: '+(new Date().toLocaleString());
        console.group('%c Ajax Print'+_time,'width:100%;border-left:3px solid #f60;'+css );
            try{
                _datas = eval('['+_datas+']'); // 将字符串拼接成类似json对象，在转换成json对象
            }catch(e)
            {
                console.log('%c'+ _response,'color:#499bea;width:100%;');
                console.groupEnd();
                return false;
            }
            for(var i in _datas )
            {
                console.group(_datas[i].position);
                    console.log('%c'+ _datas[i].content,'color:#499bea;width:100%;');
                console.groupEnd();
            }
        console.groupEnd();
        return false;
    });
}