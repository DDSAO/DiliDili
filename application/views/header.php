<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DiliDili (｡◕∀◕｡)</title>
    <meta name="description" content="A video sharing website!proudly created by haosun">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<? echo base_url("img/favicon.ico")?>" type="image/x-icon">
    <link rel="stylesheet" href="<? echo base_url("css/bootstrap.min.css")?>" type="text/css">
    <link rel="stylesheet" href="<? echo base_url("css/style.css")?>" type="text/css">
    <link rel="stylesheet" href="<? echo base_url("css/jquery.Jcrop.min.css")?>" type="text/css">
    <script src="<? echo base_url("js/jquery-3.4.1.min.js")?>"></script>
    <script src="<? echo base_url("js/bootstrap.bundle.min.js")?>"></script>
    <script src="<? echo base_url("js/js.cookie.min.js")?>"></script>
    <script>
        $(document).ready(function(){
            if (<? echo $login ?>) {
                getPemission()
                setInterval(() => {
                    moniterNotification()
                }, 1000);
            }

            function getPemission() {
                if (!("Notification" in window)) {
                    alert("This browser does not support desktop notification");
                }

                else if (Notification.permission === "granted") {
                    // If it's okay let's create a notification
                    var notification = new Notification("You already turned on notification (｡･∀･)ﾉﾞ");
                }
                //else if (Notification.permission !== 'denied') {
                else {
                    Notification.requestPermission(function (permission) {
                        if (permission === "granted") {
                            var notification = new Notification("Notification is on (｡･∀･)ﾉ!",{tag:1});
                        }
                    });
                }   
            }
            function moniterNotification(){
                fetch('<?echo isset($id) ? base_url('dashboard/getNewNotification/'.$id) : '';?>',{
                    method:'get'
                }).then(res=>res.json())
                .then(e=>{ 
                    if (e.hasNotification === 1){
                        var notification = new Notification(e.text,{tag:1})
                    }
                })
            }
        })
</script>
</head>

    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        


