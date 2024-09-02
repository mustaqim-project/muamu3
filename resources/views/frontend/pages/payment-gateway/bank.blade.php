<div class="tab-pane fade" id="v-pills-bank" role="tabpanel"
aria-labelledby="v-pills-home-tab">
    <div class="row">
        <div class="col-xl-12 m-auto">
            <div class="wsus__payment_area">
                <div class="form-group">
                    <label for="bankSelection">Pilih Bank:</label>
                    <select class="form-control" id="bankSelection" name="bank">
                        <option value="">-- Pilih Bank --</option>
                        <option value="bca">Bank Central Asia (BCA)</option>
                        <option value="bri">Bank Rakyat Indonesia (BRI)</option>
                        <option value="mandiri">Bank Mandiri</option>
                        <option value="bni">Bank Negara Indonesia (BNI)</option>
                        <option value="btn">Bank Tabungan Negara (BTN)</option>
                        <option value="cimb">CIMB Niaga</option>
                        <option value="danamon">Bank Danamon</option>
                        <option value="permata">Bank Permata</option>
                        <option value="bjb">Bank Jabar Banten (BJB)</option>
                        <option value="jateng">Bank Jateng</option>
                        <option value="jatim">Bank Jatim</option>
                        <option value="mega">Bank Mega</option>
                        <option value="panin">Panin Bank</option>
                    </select>
                </div>
                <a class="nav-link common_btn text-center" href="{{route('user.bank.payment')}}" style="margin-top:30px">Proceed</a>
            </div>
        </div>
    </div>
</div>
