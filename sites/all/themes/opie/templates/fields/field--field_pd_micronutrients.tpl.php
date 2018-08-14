<?
    $mn_elements = array();
    foreach ($element['#items'] as $key => $item) {
        $fc_item = $items[$key]['entity']['field_collection_item'][$item['value']];
        $mn_element = $fc_item['field_pd_mn_element']['#items'][0]['taxonomy_term'];
        $mn_elements[] = array(
            'element' => array(
                'name' => $mn_element->name,
                'html' => $mn_element->field_mn_html['und'][0]['value'],
                'number' => empty($mn_element->field_mn_number) ? '' : $mn_element->field_mn_number['und'][0]['value'],
            ),
            'percent' => $fc_item['field_pd_mn_percent'][0]['#markup'],
        );
    }
?>

<div class="field fc-micronutrients">
    <h3><? print $element['#title']; ?></h3>
    <p><?  print $element['#description']; ?></p>
    <? foreach ($mn_elements as $item): ?>
    <div class="fc-micronutrient">
        <div class="fc-mn-percent"><? print $item['percent']; ?>%</div>
        <div class="fc-mn-number"><? print $item['element']['number']; ?></div>
        <div class="fc-mn-element"><? print $item['element']['html']; ?></div>
        <div class="fc-mn-name"><? print $item['element']['name']; ?></div>
    </div>
    <? endforeach; ?>
</div>