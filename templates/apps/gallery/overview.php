<?php
$ovText = array();
$i18n = new MOD_i18n('apps/gallery/overview.php');
$ovText = $i18n->getText('ovText');

if ($statement) {
    $request = PRequest::get()->request;
    $requestStr = implode('/', $request);
    $matches = array();
    if (preg_match('%/page(\d+)%', $requestStr, $matches)) {
        $page = $matches[1];
        $requestStr = preg_replace('%/page(\d+)%', '', $requestStr);
    } else {
        $page = 1;
    }
    $p = PFunctions::paginate($statement, $page);
    $statement = $p[0];
    foreach ($statement as $d) {
    	echo '
<div class="img">
    <a href="gallery/show/image/'.$d->id.'"><img src="gallery/thumbimg?id='.$d->id.'" alt="image"/></a>
    <h3>'.$d->title.'</h3>
    <p class="small">'.$d->width.'x'.$d->height.'; '.$d->mimetype.'; '.$ovText['uploaded_by'].': <a href="gallery/show/user/'.$d->user_handle.'">'.$d->user_handle.'</a>.</p>
        ';
        if ($User = APP_User::login()) {
//        	echo '
//    <p class="small"><a href="gallery/edit/image/'.$d->id.'">'.$ovText['edit'].'</a></p>
//            ';
        }
        echo '
</div>';
    }
    $pages = $p[1];
    $maxPage = $p[2];
    $currentPage = $page;
    $request = $requestStr.'/page%d';
    require TEMPLATE_DIR.'misc/pages.php';
}
?>