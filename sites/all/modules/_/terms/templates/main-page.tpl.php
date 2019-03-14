<?php if ($terms): ?>
<?php endif; ?>
<div class="terms">
    <div class="filter">
        <ul class="letters">
            <?php foreach ($terms as $key_l => $letter): ?>
                <li class="letter"><a href="#<?php print translit($key_l); ?>"><?php print $key_l; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php foreach ($terms as $key_l => $litera): ?>
        <div class="litera-row">
            <div id="<?php print translit($key_l); ?>" class="litera"><?php print $key_l; ?></div>
            <div class="items">
                <?php foreach ($litera as $key=>$term): ?>
                    <div class="item">
                        <div class="mean-wrap">
                            <div class="mean">
                                <div class="head"><?php print $term['ru']; ?></div>
                                <span class="sub"><?php print $term['sub']; ?></span>
                                <?php print $term['mean']; ?>
                                <span class="close">×</span>
                            </div>
                        </div>
                        <h3><a class="ru"><?php print $term['ru']; ?></a></h3>
                        <div class="en"><?php print $term['en']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="delimiter"></div>
    <?php endforeach; ?>
</div>