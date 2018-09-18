<?php
    // получить список препаратов данного термина
    $query = db_select('node', 'n')->distinct();
    $query->innerJoin('field_data_field_pd_category', 'pc', 'n.nid = pc.entity_id');
    $query->leftJoin('field_data_title_field', 'tf', 'n.nid = tf.entity_id');
    $query->condition('pc.field_pd_category_tid', $tid);
    $query->condition('n.status', 1);
    $query->condition('tf.language', $GLOBALS['language']->language);
    $query->fields('n', array('nid'));
    $query->orderby('tf.title_field_value', 'ASC');
    $preps = $query->execute()->fetchAll();
?>

<div class="taxonomy teaser tid-<?php print $tid; ?>">
    <h4><span class="tax-icon"></span><a href="<?php print url('taxonomy/term/' . $tid); ?>"><?php print $name; ?></a></h4>
    <ul>
        <?php foreach($preps as $prep): ?>
            <?php
            // todo перейти на использование get_product_info
            $product_info = get_product_agro_title($prep->nid);
            $product_title = $product_info['formulation'] ? $product_info['title'] . ', ' . $product_info['formulation'] : $product_info['title'];
            ?>
            <li><i class="fa fa-chevron-right"></i><a href="<?php print url('node/' . $prep->nid); ?>"><?php print $product_title; ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>