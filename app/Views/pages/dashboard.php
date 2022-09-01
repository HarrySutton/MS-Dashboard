<div class="container">


    <div class="card-deck">

        <a href="/holiday">
            <div class="card" style="max-width:250px;">
                <img src="/library/images/holiday_icon.png" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Holiday</h5>
                </div>
            </div>
        </a>

        <a href="/invoices">
            <div class="card" style="max-width:250px;">
                <img src="/library/images/invoice_icon.png" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Invoices</h5>
                </div>
            </div>
        </a>

        <?php if($permiss == 2){ ?>
        <a href="/admin">
            <div class="card" style="max-width:250px;">
                <img src="/library/images/admin_icon.png" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Administrator</h5>
                </div>
            </div>
        </a>
        <?php } ?>

        <a href="/officebooking">
            <div class="card" style="max-width:250px;">
                <img src="/library/images/invoice_icon.png" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Office Booking</h5>
                </div>
            </div>
        </a>

    </div>


</div>