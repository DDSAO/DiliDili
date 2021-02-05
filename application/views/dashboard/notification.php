<div class="w-100 h-100 content " id="notificationPage">
    <div class=" w-100 p-2 d-flex flex-column-reverse justify-content-center align-items-center scroll-auto">  
    <?
    foreach($notification as $n) {
        echo 
        '
        <div class="w-75 m-2 px-2 py-4 roundBorder darkYellow d-flex justify-content-between">
            <div class=" text-center ">
                '.$n->text.'
            </div>
            <div style="">
                '.date('M,d,Y h:i:s',$n->time).'
            </div>
        </div>
        
        ';
    }
    ?>
    </div>
</div>
<script>
    $("#notification").click(function(){$(this).text('Notification')});
</script>
