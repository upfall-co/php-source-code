<?php
    use Clef\SiteConfig;

    //site config
    $_footer_data = SiteConfig::footer_data(PAGE2);
    $_sns_data = SiteConfig::sns_data(PAGE2);
?>
<footer id="footer" class="footer_main close">
    <div class="wrapper">
        <div id="footer_toggle" class="close">
            <span>INFO</span>
            <img src="<?= artFoldName ?>/img/main/footer_arrow.svg" alt="푸터 토글버튼">
        </div>
        <div class="footer_lt">
            <ul>
                <li><?php echo (!empty($_footer_data['company_name'])) ? "{$_footer_data['company_name']}" : ''; ?></li>
                <li>대표 <?php echo (!empty($_footer_data['rep_name'])) ? "{$_footer_data['rep_name']}" : ''; ?></li>
                <li><?php echo (!empty($_footer_data['addr'])) ? "{$_footer_data['addr']}" : ''; ?></li>
                <li><a href="<?php echo (!empty($_footer_data['mobile'])) ? "tel:{$_footer_data['mobile']}" : 'javascript:void(0);'; ?>">대표번호 <?php echo (!empty($_footer_data['mobile'])) ? "{$_footer_data['mobile']}" : ''; ?></a></li>
                <li><a href="<?php echo (!empty($_footer_data['email'])) ? "mailto:{$_footer_data['email']}" : 'javascript:void(0);'; ?>"><?php echo (!empty($_footer_data['email'])) ? "{$_footer_data['email']}" : ''; ?></a></li>
            </ul>
            <ul>
                <li>통신판매업신고번호 <?php echo (!empty($_footer_data['number'])) ? "{$_footer_data['number']}" : ''; ?></li>
                <li>사업자등록번호 <?php echo (!empty($_footer_data['company_rep_num'])) ? "{$_footer_data['company_rep_num']}" : ''; ?></li>
            </ul>
            <p class="copy">Copyright ⓒ 2023 CLEF. All rights reserved.</p>
        </div>
        <div class="footer_rt">
            <?php if (!empty($_sns_data['instargram'])) { ?>
                <ul>
                    <li><a href="<?php echo (!empty($_sns_data['instargram'])) ? "{$_sns_data['instargram']}" : 'javascript:void(0);'; ?>" target="_blank"><img src="<?= artFoldName ?>/img/footer_sns_insta.png" alt="인스타그램"></a></li>
                </ul>
            <?php } ?>
        </div>
    </div>
</footer>