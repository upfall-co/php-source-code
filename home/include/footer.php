<?php

use Clef\SiteConfig;

//site config
$_footer_data = SiteConfig::footer_data(PAGE3);
$_sns_data = SiteConfig::sns_data(PAGE3);
?>
<footer>

    <div class="footer__wrap">

        <div class="footer__info_container">

            <ul class="footer__info_wrap">
                <li><?php echo (!empty($_footer_data['company_name'])) ? "{$_footer_data['company_name']}" : ''; ?></li>
                <li>대표 <?php echo (!empty($_footer_data['rep_name'])) ? "{$_footer_data['rep_name']}" : ''; ?></li>
                <li><?php echo (!empty($_footer_data['addr'])) ? "{$_footer_data['addr']}" : ''; ?></li>
                <li><a href="<?php echo (!empty($_footer_data['mobile'])) ? "tel:{$_footer_data['mobile']}" : 'javascript:void(0);'; ?>">대표번호 <?php echo (!empty($_footer_data['mobile'])) ? "{$_footer_data['mobile']}" : ''; ?></a></li>
                <li><a href="<?php echo (!empty($_footer_data['email'])) ? "mailto:{$_footer_data['email']}" : 'javascript:void(0);'; ?>"><?php echo (!empty($_footer_data['email'])) ? "{$_footer_data['email']}" : ''; ?></a></li>
            </ul>

            <ul class="footer__info_wrap">

                <li>통신판매업신고번호 <?php echo (!empty($_footer_data['number'])) ? "{$_footer_data['number']}" : ''; ?></li>
                <li>사업자등록번호 <?php echo (!empty($_footer_data['company_rep_num'])) ? "{$_footer_data['company_rep_num']}" : ''; ?></li>
            </ul>

            <p class="copy">Copyright ⓒ 2023 piknic. All rights reserved.</p>

        </div>


        <ul class="footer__sns_wrap">
            <?php if (!empty($_sns_data['instargram'])) { ?>
                <li>
                    <a href="<?php echo (!empty($_sns_data['instargram'])) ? "{$_sns_data['instargram']}" : 'javascript:void(0);'; ?>" target="_blank"><img src="<?= artFoldName ?>/img/footer_sns_insta.png" alt="인스타그램"></a>
                </li>
            <?php } ?>
        </ul>

    </div>

</footer>