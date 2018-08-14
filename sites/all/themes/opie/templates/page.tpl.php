<?php
// Мы рендерим сайдбар чтобы узнать, есть ли там блоки или нет.
$sidebar = render($page['sidebar_first']);
// В зависимости от того, есть ли сайдбар или нет, мы меняем класс у
// основного контента, чтобы растянуть его на всю ширину в случае отсутствия
// сайдбара.
if ($sidebar) {
    $main_content_class = 'grid-3-4';
} else {
    $main_content_class = 'grid-full';
}
if (!empty($title_color_style)) $title_color_style = 'style="background-color:#' . $title_color_style . '"';

$bg_image = $is_front ? '/sites/default/files/images/taxonomy/sliders/agrohimiya_pbg.jpg' : '/sites/all/themes/opie/images/backgrounds/common_bg.jpg';

global $user;
?>
<div id="page">
    <div id="page-wrapper">
        <header id="header">
            <div class="utility">
                <div class="user-menu">
                    <?php print $user_menu; ?>
                </div>
            </div>
            <div id="float-wrap">
                <div class="header-pane">
                    <a class="logo" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
                        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
                    </a>
                    <div id="mainmenu">
                        <?php print $primary_nav; ?>
                    </div>
                    <div id="services">
                        <div id="callback" class="popup-trigger">
                            <a class="call_back_post">
                                <i class="icon-phone"></i>
                            </a>
                            <div class="popup popup-top-center"><div class="popup-content"><? print t('Call back request');?></div></div>
                        </div>
                        <div id="send-message" class="popup-trigger">
                            <a href="#" onClick="MeTalk('openSupport'); return false;">
                                <i class="icon-message"></i>
                            </a>
                            <div class="popup popup-top-center"><div class="popup-content"><? print t('Online chat');?></div></div>
                        </div>
                        <div id="search" class="popup-trigger">
                            <i class="icon-search"></i>
                            <div class="popup popup-top-center"><div class="popup-content"><? print t('Search');?></div></div>
                        </div>
                        <span id="search-pane" class="hide-fade">
                            <form action="/search" id="search-api-header">
                                <input name="s" value="" maxlength="128" class="form-text" type="text" placeholder="<?php print $search_input_placeholder; ?>">
                                <div class="submit-wrapper"><input type="submit" value=""></div>
                            </form>
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <?php if (!$is_front): ?>
            <div id="navigation">
                <?php print $breadcrumb; ?>
                <?php print render($page['navigation']); ?>
            </div>
            <div class="navigation-place hide">
            </div>
        <?php endif; ?>

        <?php print $messages; ?>

        <?php if ($title && !$is_front && !$no_title): ?>
            <?php if (!empty($title_new)): ?>
            <div id="page-title-wrapper-new">
                <div class="title-header"<?php print isset($title_color_style) ? $title_color_style : ''; ?>><h1 id="page-title" property="dc:title"><?php print $title; ?></h1></div>
                <?php if ($title_description): ?>
                    <div class="title-description">
                        <div class="title-icon"><img src="<? print $title_image_url; ?>" alt=""></div>
                        <div><?php print $title_description; ?></div>
                    </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
                <div id="page-title-wrapper" <?php print isset($title_color_style) ? $title_color_style : ''; ?> >
                    <h1 id="page-title" property="dc:title"><?php print $title; ?></h1>
                    <?php if ($title_description): ?>
                        <h2 id="page-short-description"><?php print $title_description; ?></h2>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($page['slider']): ?>
            <div id="slider-container">
                <?php print render($page['slider']); ?>
            </div>
        <?php endif; ?>

        <div id="content-container">
            <?php if ($is_front): ?>
                <?php print render($page['highlighted']); ?>
                <?php print render($page['help']); ?>
                <?php print render($page['content_front']); ?>
            <?php else: ?>
                <div id="main-content" class="<?php print $main_content_class; ?> contextual-links-region left" role="main">
                    <?php print render($tabs); ?>
                    <?php print render($page['highlighted']); ?>
                    <?php print render($page['help']); ?>
                    <?php if ($action_links): ?>
                        <ul class="action-links"><?php print render($action_links); ?></ul>
                    <?php endif; ?>
                    <?php print render($page['content']); ?>
                </div>

                <?php if ($sidebar): ?>
                    <aside id="sidebar" class="grid-1-4 left" role="sidebar">
                        <?php print $sidebar; ?>
                    </aside>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>
<footer id="footer" role="footer" class="footer">
    <a class="logo" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
    </a>
    <div class="description"><?php print t('Production and selling of fertilizers'); ?><br />
        <span style="white-space: nowrap;"><?php print t('and plant protection products'); ?></span></div>
    <div class="social">
        <a class="vk" href="http://vk.com/public147827276" target="_blank" title="вконтакте"><i class="fa fa-vk" aria-hidden="true"></i></a>
        <a class="fb" href="http://www.facebook.com/kccc.ru" target="_blank" title="facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
        <a class="tw" href="https://twitter.com/kccc_ru" target="_blank" title="twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
        <a class="in" href="https://instagram.com/cg.kccc" target="_blank" title="instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
        <a class="yt" href="http://www.youtube.com/channel/UCFenAWL6Wa0iJzpVpsvn64w" target="_blank" title="youtube"><i class="fa fa-youtube-play" aria-hidden="true"></i></a>
    </div>
    <?php print render($page['footer']); ?>
</footer>

<img id="bg1" src="<? print $bg_image; ?>" />
<img id="bg2" src="" />
<div id="modalBackdrop"></div>