@include('vendor.ueditor.assets')

<style>
    #save-btn {
        left: 100px;
        top: 60px;
        position: absolute;
    }
</style>
<div id="save-btn-parent">
    <button id="save-btn" type="button" class="btn btn-info">保存</button>
</div>
<div class="container">
    <h3>{{$title}}</h3>
    <div class="row clearfix">
        <div class="col-md-6 column">
            <script id="wx" style="width:100%;" name="wx_html" type="text/plain">{!! $wxHtml !!}</script>
        </div>
        <div class="col-md-6 column">
            <script id="en" style="width:100%;" name="en_html" type="text/plain">{!! $enHtml !!}</script>
        </div>
    </div>
</div>

<script type="text/javascript">
    let wx = UE.getEditor('wx', {
        toolbars: [['Source', 'Undo', 'Redo']]
    });
    wx.ready(function () {
        wx.execCommand('serverparam', '_token', '{{ csrf_token() }}');
    });
    let en = UE.getEditor('en', {
        toolbars: [['Source', 'Undo', 'Redo']]
    });
    en.ready(function () {
        en.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
    });

    $('#save-btn').click(function () {
        let content = wx.getContent();
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
