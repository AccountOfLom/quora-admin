@include('vendor.ueditor.assets')

<style>
    #save-btn {
        left: 100px;
        top: 60px;
        position: absolute;
    }
</style>
{{--<div id="save-btn-parent">--}}
{{--    <button id="save-btn" type="button" class="btn btn-info">保存</button>--}}
{{--</div>--}}
<div class="container">
    <h3>{{$title}}</h3>
    <div class="row clearfix">

        <div class="col-md-4 column">
            <p>百度翻译</p>
            <script id="baidu" style="width:100%;" name="baidu_content" type="text/plain">{!! $baiduContent !!}</script>
        </div>

        <div class="col-md-4 column">
            <p>有道翻译</p>
            <script id="youdao" style="width:100%;" name="youdao_content" type="text/plain">{!! $youdaoContent !!}</script>
        </div>

        <div class="col-md-4 column">
            <p>原文</p>
            <script id="en" style="width:100%;" name="en_html" type="text/plain">{!! $enContent !!}</script>
        </div>
    </div>
</div>

<script type="text/javascript">

    let baidu = UE.getEditor('baidu', {
        toolbars: [['Source', 'Undo', 'Redo']]
    });
    baidu.ready(function () {
        baidu.execCommand('serverparam', '_token', '{{ csrf_token() }}');
    });

    let youdao = UE.getEditor('youdao', {
        toolbars: [['Source', 'Undo', 'Redo']]
    });
    youdao.ready(function () {
        youdao.execCommand('serverparam', '_token', '{{ csrf_token() }}');
    });

    let en = UE.getEditor('en', {
        toolbars: [['Source', 'Undo', 'Redo']]
    });
    en.ready(function () {
        en.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
    });

    $('#save-btn').click(function () {
        let content = baidu.getContent();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
            url: 'sort-reset',
            type: 'post',
            data: {content: content},
            success: function (data) {
                if (data.code == 200) {
                    $.pjax.reload('#pjax-container');
                    toastr.success('操作成功');
                } else {
                    toastr.warning(data.message);
                }
            },
            error: function (err) {
                toastr.warning(err);
            }
        });
    });


</script>
