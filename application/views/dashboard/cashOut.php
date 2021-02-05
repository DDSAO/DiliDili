<div class="w-100 h-100 content" id="cashOutPage">

    <div class="container">
        <div class="row my-2 pl-4 ">
            <p class="ml-0">You have <?echo $money?> dollars in your account! </p>
        </div>
        <div class="row my-2 pl-4 ">
            <button class="btn btn-primary">Banking Detail</button>
        </div>
        <div class="row my-2 pl-4 d-flex flex-column">
            <p class="ml-0">Account Name:<span id="accountName"></span></p>
            <p class="ml-0">Bank Name:<span id="bankName"></span></p>
            <p class="ml-0">BSB:<span id="bsb"></span></p>
            <p class="ml-0">Account Number:<span id="accountNumber"></span></p>
        </div>
        <div class="row my-2 pl-4 ">
            <div class="col col-6"><button class="btn btn-primary" id="edit" disabled>Edit (Not available)</button></div>
            <div class="col col-6"><button class="btn btn-success" id="cashOut" disabled>Cash out (Not available)</button></div>
        </div>
    </div>
</div>