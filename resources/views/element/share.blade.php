<script>
    function shared() {
        $.ajax({
            type: 'post',
            url: '{{route('share')}}',
            dataType: 'json',
            success:function (res) {
                if(res.code!=0){
                    Popup.init({
                        popHtml:'<p>'+res.message+'</p>',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                }
            }
        });
    }
</script>