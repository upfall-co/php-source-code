<?php
    define("SUB", "02");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";

    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/category_code.php';

    $PROGRAM_CD = get_request_param('PROGRAM_CD', 'GET');
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="prog__sec1 blank blank--t3">

      <div class="prog_s1_container wrapper">

        <div class="subpage__title_container">
          <?php if(empty($search_text)) { ?>
            <h3 class="title">program</h3>
          <?php } else { ?>
            <h3 class="title"><?=$search_text?></h3>
          <?php } ?>
        </div>

        <!-- 250402 고정값으로 변경 -->
        <ul class="gall__container gall__container--t2">
            <!-- exhibition-associated 리스트 -->
            <?php if ($PROGRAM_CD == 'EXAS') { ?>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_01&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb01.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>사운드워크 컬렉티브 & 패티 스미스 : 끝나지 않을 대화 Soundwalk Collective & Patti Smith : Correspondences</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_02&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb02.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>우에다 쇼지 모래극장 Ueda Shoji Theatre of the Dunes</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_03&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb03.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>달리기 : 새는 날고 물고기는 헤엄치고 인간은 달린다 Running : Birds fly, Fish swim, Humans run</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_04&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb04.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>회사 만들기 Entrepreneurship</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_05&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb05.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>프랑수아 알라르 : 비지트 프리베 Visite Privée par François Halard</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_06&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb06.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>국내여행 Grand Tour Korea</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_07&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb07.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>사울 레이터 : 창문을 통해 어렴풋이 Saul Leiter : Through the Blurry Window</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_08&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb08.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>정원 만들기 Gardening</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_09&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb09.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>명상 Mindfulness</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_10&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb10.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>페터 팝스트 Peter Pabst : White Red Pink Green</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_11&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb11.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>재스퍼 모리슨 Jasper Morrison : Thingness</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_12&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb12.jpg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>류이치 사카모토 : 라이프, 라이프 Ryuichi Sakamoto : Life, Life</p>
                        </div>
                    </a>
                </li>
            <?php } else { ?>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_13&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb13.jpeg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>소설극장 Reading Theater</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_14&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb14.jpeg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>무성영화극장 Silent Film & Live</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_15&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb15.jpeg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>음악감상회 Listening Session</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_16&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb16.jpeg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>희곡극장 Play Theater</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="/home/sub02/program.php?cate=PROGRAM_17&PROGRAM_CD=<?=$PROGRAM_CD?>" class="thumb_container">
                        <figure>
                            <img src="/home/img/sub04/thumb17.jpeg" alt="썸네일">
                        </figure>
                        <div class="title_wrap">
                            <p>정원학교 Garden School</p>
                        </div>
                    </a>
                </li>
            <?php } ?>
        </ul>
      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>