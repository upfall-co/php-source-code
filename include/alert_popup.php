<div id = "alert_popup" class="popup only_txt_popup alert_popup">
    <div class="x_btn" onclick="cust_popClose('cancel', '${id}');"><img src="/img/icon/pop_x_btn.png" alt="닫기"></div>

    <div class="only_txt">
        <p>${content}</p>
    </div>

    <div class="pop_btn_wrap one_btn">
        <div class="border_btn" onclick="cust_popClose('${callback1}', '${id}')">${button1}</div>
    </div>
</div>