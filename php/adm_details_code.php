
<?php
/**
 * 파일명 : admdetails_code.php
 * 내용 : 계정관리 상세
 * 최초작성날짜 : 2023/06/22
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/06/22    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
         'code' => 500
       , 'msg' => ''
    );

    try {
        if(!preg_match("/".$_SERVER['HTTP_HOST']."/i",$_SERVER['HTTP_REFERER'])) {
            dieAndErrorMove("올바른 접근이 아닙니다.");
        }

        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $mode = get_request_param('mode', 'GET');
        $id = get_request_param('seq', 'GET');
        $M_ID = get_request_param('ID', 'GET');
        $M_NAME = get_request_param('NAME', 'GET');
        $M_MOBILE = get_request_param('MOBILE', 'GET');

        $S_id = $_SESSION['adm']['id'];

        $MENU_ACCESS = $_SESSION['adm']['menu_access'];
        $direct = false;

        $_db_id = '';
        $_db_name = '';
        $phone1 = '';
        $_db_email = '';
        $disabled = '';

        $table = 'adm'; // 관리자 테이블
        $arrMenu = array();
        $menu_html = '';
        $arrMenuAccess = array();
        $arrMobile  = array(
            0   => '',
            1   => '',
            2   => ''
        );

        if ($mode == 'MOD') {
            if ((!empty($S_id) && empty($id))) {
                $direct = true;
                $id = $S_id;
            }
            
            $disabled = 'disabled';

            $sql = "
                SELECT id
                    , name
                    , mobile
                    , email
                    , menu_access
                FROM {$table}
                WHERE id = :id";

            $name_sql = "서브관리자 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':id' => $id], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            $_db_id = _check_var($data['id']); // 아이디
            $_db_menu_access = _check_var($data['menu_access']); // 메뉴 권한
            $_db_name = _check_var($data['name']); // 이름
            $_db_mobile     = _check_var($data['mobile']); // 연락처
            $_db_email     = _check_var($data['email']); // 이메일

            //변수 정리
            if (!empty($_db_mobile)) {
                $_db_mobile = formatPhoneNumber($_db_mobile);
                $arrMobile  = explode('-', $_db_mobile);
            }

            $arrMenuAccess = (!empty($_db_menu_access)) ? explode(',', $_db_menu_access) : array();
        }

        foreach ($config['mobile'] as $val) {
            $selected = "";

            if (!empty($arrMobile[0])) {
                if ($val == $arrMobile[0]) {
                    $selected = 'selected';
                }
            }

            $phone1 .= '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
        }

        $sql = "
              SELECT seq
                   , parent_seq
                   , depth
                   , name
                   , (SELECT name FROM project_menu D WHERE M.parent_seq = D.seq AND D.depth = 0) as Mname
                   , page_type
                FROM project_menu M
               WHERE 1
                 AND TYPE_CD = 'ADM'
                 AND use_yn = 'y'
            ORDER BY depth ASC, sorting DESC";
        
        $name_sql = "메뉴리스트 리스트";
        $clefResult = $mysqldb->select($sql, null, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }
        $total = $clefResult->getCount();
        $list = $clefResult->getResultSet();

        foreach ($list as $data) {
            //DB 변수 정리
            $_db_m_seq = _check_var($data['seq']);
            $_db_m_parent_seq = _check_var($data['parent_seq']);
            $_db_m_depth = _check_var($data['depth']);
            $_db_m_name = _check_var($data['name']);
            $_db_m_page_type = _check_var($data['page_type']);
            $_db_m_Mname = _check_var($data['Mname']);
            
            //변수 정리
            $m_seq_idx = ($_db_m_depth) ? $_db_m_parent_seq : $_db_m_seq;
    
            $arrMenu[$m_seq_idx][$_db_m_seq] = array(
                'seq' => $_db_m_seq,
                'parent_seq' => $_db_m_parent_seq,
                'depth' => $_db_m_depth,
                'name' => $_db_m_name,
                'page_type' => $_db_m_page_type,
                'Mname' => $_db_m_Mname
            );
        }

        $count = 0;  

        $menu_html .= '
        
                <div class="form-group row">
                    <div class="col-md-4">
                        <div class="abc-checkbox abc-checkbox-info abc-checkbox-circle">
                            <input class="form-check-input" type="checkbox" id="C_checkall" name="checkall" data-type="collection">
                            <label class="form-check-label" for="C_checkall">
                    
                            <legend class="text-right"> 미술품 </legend>
                            </label>
                        </div>
                            <fieldset class="collection">';
        foreach ($arrMenu as $key => $val) {
            foreach ($arrMenu[$key] as $key2 => $val2) {
                if ($val2['page_type'] == PAGE1) {
                    $checked = (in_array($val2['seq'], $arrMenuAccess)) ? 'checked="checked"' : '';

                    $name = "";

                    if (!empty($val2['Mname'])) {
                        $name .=  $val2['Mname']. " - ";
                        $margin = "m-md";
                    } else {
                        $margin = "";
                    }

                    $name.= $val2['name'];

                    $menu_html .= <<<DIV
                                        <div class="abc-checkbox abc-checkbox-info abc-checkbox-circle">
                                            <input class="form-check-input" id="checkbox{$count}" type="checkbox" name="menu_seq[]" value="{$val2['seq']}" data-pseq="{$val2['parent_seq']}" data-depth="{$val2['depth']}" {$checked}>
                                            <label class="form-check-label {$margin}" for="checkbox{$count}">
                                                {$name}
                                            </label>
                                        </div>
                                    DIV;
                    $count++;
                }
            }
        }

                    $menu_html .= '
                                    </fieldset>
                                </div>';
                    $menu_html .= '
                                <div class="col-md-4">
                                    <div class="abc-checkbox abc-checkbox-info abc-checkbox-circle">
                                        <input class="form-check-input" type="checkbox" id="S_checkall" name="checkall" data-type="shop">
                                        <label class="form-check-label" for="S_checkall">
                                
                                        <legend class="text-right"> 샵 </legend>
                                        </label>
                                    </div>
                                        <fieldset class="shop">';
        foreach ($arrMenu as $key => $val) {
            foreach ($arrMenu[$key] as $key2 => $val2) {
                if ($val2['page_type'] == PAGE2) {
                    $checked = (in_array($val2['seq'], $arrMenuAccess)) ? 'checked="checked"' : '';

                    $name = "";

                    if (!empty($val2['Mname'])) {
                        $name .=  $val2['Mname']. " - ";
                        $margin = "m-md";
                    } else {
                        $margin = "";
                    }

                    $name.= $val2['name'];

                    $menu_html .= <<<DIV
                                        <div class="abc-checkbox abc-checkbox-info abc-checkbox-circle">
                                            <input class="form-check-input" id="checkbox{$count}" type="checkbox" name="menu_seq[]" value="{$val2['seq']}" data-pseq="{$val2['parent_seq']}" data-depth="{$val2['depth']}" {$checked}>
                                            <label class="form-check-label {$margin}" for="checkbox{$count}">
                                                {$name}
                                            </label>
                                        </div>
                                    DIV;

                    $count++;
                }
            }
        }

        $menu_html .= '     </fieldset>
                        </div>';

        $menu_html .= '
                        <div class="col-md-4">
                            <div class="abc-checkbox abc-checkbox-info abc-checkbox-circle">
                                <input class="form-check-input" type="checkbox" id="H_checkall" name="checkall" data-type="home">
                                <label class="form-check-label" for="H_checkall">
                        
                                <legend class="text-right"> 브랜드</legend>
                                </label>
                            </div>
                                <fieldset class="home">';
        foreach ($arrMenu as $key => $val) {
            foreach ($arrMenu[$key] as $key2 => $val2) {
                if ($val2['page_type'] == PAGE3) {
                    $checked = (in_array($val2['seq'], $arrMenuAccess)) ? 'checked="checked"' : '';

                    $name = "";

                    if (!empty($val2['Mname'])) {
                        $name .=  $val2['Mname']. " - ";
                        $margin = "m-md";
                    } else {
                        $margin = "";
                    }

                    $name.= $val2['name'];

                    $menu_html .= <<<DIV
                                        <div class="abc-checkbox abc-checkbox-info abc-checkbox-circle">
                                            <input class="form-check-input" id="checkbox{$count}" type="checkbox" name="menu_seq[]" value="{$val2['seq']}" data-pseq="{$val2['parent_seq']}" data-depth="{$val2['depth']}" {$checked}>
                                            <label class="form-check-label {$margin}" for="checkbox{$count}">
                                                {$name}
                                            </label>
                                        </div>
                                    DIV;

                    $count++;
                }
            }
        }
                    $menu_html .= '
                                    </fieldset>
                                </div>
                            </div>';

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'ID' => $M_ID
            , 'NAME' => $M_NAME
            , 'MOBILE' => $M_MOBILE
        );

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
 ?>