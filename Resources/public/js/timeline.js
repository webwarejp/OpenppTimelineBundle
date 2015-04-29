$(document).ready(function(){
  // proximityはbottomイベントを発生させる位置を調整する値です
  $(window).bottom({proximity: 0.01});
  // 今回はwindowオブジェクトを指定していますが
  // scrollイベントが機能する要素(frameなど)を指定することも可能です
  $(window).bind('bottom', function() {
    // ↓↓ ここ以降にイベントが発生した時に実行する処理を書きます
    var obj = $(this);
    // bottomイベントは、スクロールされる度に
    // 何度も発生してしまいます
    // そのため、このloadingというフラグを使って
    // 重複処理を避けているようです
    if (!obj.data('loading')) {
      obj.data('loading', true);
      // 読み込み中の画像を表示
      $('#loadimg').html('<img src="/bundles/openpptimeline/image/loading.gif" />');
      setTimeout(function() {
        // ここにajax()やgetJSON()など
        // 実際に次のコンテンツを取得する処理を記述します
        $.ajax({
            type:'GET',
            url: Routing.generate('openpp_timeline_get') + '/' + $('#timeline').attr('data-id') + '/' + parseInt($('#timeline').attr('data-page')+1),
            dataType: 'json',
            success: function(data) {
                $(data.results).each(function(key, value) {
                    $('#timeline').append(value); 
                 });
                $('#timeline').attr('data-page', parseInt($('#timeline').attr('data-page'))+1);
                // 読み込み中の画像を削除
                $('#loadimg').html('');
                obj.data('loading', false);
                if (data.lastPage < $('#timeline').attr('data-page')) {
                    $(window).unbind('bottom');
                }
            },
            error: function (err, status, thrown) {
                alert('An unexpeded error occured.'+'('+err.status+' '+thrown.message+')');
                // 読み込み中の画像を削除
                $('#loadimg').html('');
                obj.data('loading', false);
            }
        });
      }, 1500);
    }
    return false;
  });
});