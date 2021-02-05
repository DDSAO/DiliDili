
<div class="w-100 h-100 content" id="topUpPage">
    <div class="container-fluid">
        <div class="row my-2 p-2 d-flex flex-column" id="testAccount"><p>Test account: buyer@dilidili.com</p><p>password: 12345678</p></div>
        <div class="row my-2 justify-content-around"><div class="d-flex" id="options"></div></div>
        <div class="row my-2 justify-content-center"><button class="btn btn-primary" id="confirmTopUp" disabled>Confirm (Choose one plan first)</button></div>
        <div class="row my-2 justify-content-center"><div class="" id="pay"></div></div>
    </div>
</div>
<script defer src="https://www.paypal.com/sdk/js?client-id=Acdl-VQxOvN9M8_shy1VZujwGS31HePjZXr7fSckcCGif7rVRYYWPoJKlJsEJ3f7smiycyb2rRNiJ8K_&currency=AUD"></script>
<script>
var buttonConfig = { 
    
    onApprove: function(data, actions) {
      // This function captures the funds from the transaction.
        $("#confirmTopUp").attr('disabled',true).text('waiting confirmation from PayPal')
        $("#pay").css('display','none')

      
      return actions.order.capture().then(function(details) {
            fetch('<?echo base_url('dashboard/topup')?>',{
                method:"POST",
                header:{
                "content-type":"application/json"
                },
                body:JSON.stringify({
                    orderId: details.id,
                    uid:<?echo $id;?>,
                    amount:chosenPlan,
                })
            })
            .then(res=>res.json())
            .then(res=>{
                let url = "<?echo base_url('dashboard/topupresult')?>"+'?amount='+res.amount+'&currentMoney='+res.currentMoney
                window.location.replace(url);

            })
            .catch(e=>console.log(e));
        })
    }
}
</script>
<script>
    let values = [2, 5, 10, 100, 500]
    let costs = [2, 4.9, 9.5, 92, 450]
    var chosenPlan;
    function renderOption(value, price){
        return '<div class="option m-2 p-2" value="'+ value +'" price="'+ price +'">\
                    <div>values => '+ value + '<div>\
                    <div>prices => ' + price + '<div>\
                <div>'
    }
    values.forEach((e,i)=>{
        $("#options").append(renderOption(e,costs[i]))
        $(".option").click(function(){
            $('.chosenOption').removeClass('chosenOption')
            $(this).addClass('chosenOption')
            $('#confirmTopUp').attr('disabled',false).text('Confirm')
            
        })
    })

    $("#confirmTopUp").click(function(){
        let order = function(data,actions){
            chosenPlan = $(".chosenOption").attr('value')
            return actions.order.create({
                purchase_units: [{
                amount: {
                    currency: "AUD",
                    value: $(".chosenOption").attr('price'),
                },
                description:"Top up "+ $(".chosenOption").attr('value') +" coins for your DiliDili account",
                }]
            });
        }
        Object.defineProperty(order, "name", { value: "createOrder" });
        buttonConfig.createOrder = order

        paypal.Buttons(buttonConfig).render('#pay')
        $(this).replaceWith('')
    })
</script>
