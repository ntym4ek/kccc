<?php
  $before = array_shift($content['measurements']);
?>

<article class="season node full">

  <div class="node-author">
    <img typeof="foaf:Image" src="<?php print $content['author']['photo']; ?>" alt="Представитель ООО ТД Кирово-Чепецкая Химическая Компания в регионе <?php print $content['region'];?>" class="img-circle" loading="lazy">
    <div class="author-body">
      <div class="author-title"><a href="/user/<?php print $content['author']['id']; ?>"><?php print $content['author']['full_name']; ?></a></div>
      <div class="author-subtitle"><?php print $content['author']['staff']['role_full']; ?></div>
    </div>
  </div>

  <div class="content row">
    <div class="column before col-md-6">
      <header class="col-title">
        До обработки
      </header>
      <div class="block b1">
        <div class="image">
          <div class="date">Состояние поля на <?php print $before['date']; ?></div>
          <a href="<?php print $before['image_field_full']; ?>" class="fancybox" title="Состояние поля <?php print $content['culture'];?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
            <img typeof="foaf:Image" src="<?php print $before['image_field_thumb']; ?>" property="dc:image" class="img-responsive" alt="Состояние поля <?php print $content['culture'];?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" loading="lazy">
          </a>
        </div>
        <div class="text c1">
          <?php if (!empty($before['comment_short'])): ?>
            <div class="cc1"><?php print $before['comment_short']; ?></div>
            <div class="cc2"><?php print $before['comment']; ?>&nbsp;<span class="s1">свернуть</span></div>
          <?php else: ?>
            <div class="cc1"><?php print $before['comment']; ?></div>
          <?php endif; ?>
        </div>
        <div class="text">
          <?php $ho_photo_message = false; ?>
          <table>
            <?php if ($content['weeds_d'] || $content['weeds_o']): ?>
              <tr class="head"><th width="62%">Сорное растение</th><th width="30%">Фаза</th><th width="8%"></th></tr>
              <?php if ($content['weeds_d']): ?>
                <tr class="title dom" align="center"><td colspan="3">Доминантные</td></tr>
                <?php foreach($before['hobjects'] as $hobject): ?>
                  <?php if ($hobject['type'] == 'weed' && $hobject['dominant']): ?>
                    <tr class="dom">
                      <td><a href="/<?php print $hobject['link']; ?>" target="_blank"><?php print $hobject['name']; ?></a></td>
                      <td><?php print $hobject['phase']; ?></td>
                      <td>
                        <?php if (!empty($hobject['photo'])): ?>
                          <a href="<?php print $hobject['photo']; ?>" title="<?php print $hobject["photo_title"]; ?><?php print ($hobject["is_handbook_photo"] ? '': ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания'); ?>" class="fancybox<?php print ($hobject["is_handbook_photo"] ? '': ' is-own'); ?>">
                            <i class="fa fa-camera" aria-hidden="true"></i>
                          </a>
                          <?php $ho_photo_message = true; ?>
                        <?php else: ?>
                          -
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
              <?php if ($content['weeds_o']): ?>
                <tr class="title" align="center"><td colspan="3">Прочие</td></tr>
                <?php foreach($before['hobjects'] as $hobject): ?>
                  <?php if ($hobject['type'] == 'weed' && !$hobject['dominant']): ?>
                    <tr>
                      <td><a href="/<?php print $hobject['link']; ?>" target="_blank"><?php print $hobject['name']; ?></a></td>
                      <td><?php print $hobject['phase']; ?></td>
                      <td>
                        <?php if (!empty($hobject['photo'])): ?>
                          <a href="<?php print $hobject['photo']; ?>" title="<?php print $hobject["photo_title"]; ?><?php print ($hobject["is_handbook_photo"] ? '': ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания'); ?>" class="fancybox<?php print ($hobject["is_handbook_photo"] ? '': ' is-own'); ?>">
                            <i class="fa fa-camera" aria-hidden="true"></i>
                          </a>
                          <?php $ho_photo_message = true; ?>
                        <?php else: ?>
                          -
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            <?php endif; ?>
          </table>
          <?php if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
        </div>
      </div>
      <div class="block">
        <?php if (!empty($before['image_culture_thumb'])): ?>
          <div class="image">
            <div class="date">Состояние культуры на <?php print $before['date']; ?></div>
            <a href="<?php print $before['image_culture_full']; ?>" class="fancybox" title="Состояние культуры <?php print $content['culture'];?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
              <img typeof="foaf:Image" src="<?php print $before['image_culture_thumb']; ?>" property="dc:image" class="img-responsive" alt="Состояние культуры <?php print $content['culture'];?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" loading="lazy">
            </a>
          </div>
          <div class="text">
            <div class="b2"><span>Состояние: </span><?php print $before['condition']; ?></div>
            <div class="b2"><span>Фаза: </span><?php print $before['phase']; ?></div>
          </div>
        <?php endif; ?>
        <?php if ($content['pests']): ?>
          <div class="text">
            <?php $ho_photo_message = false; ?>
            <table>
              <tr class="head"><th>Вредители</th><th width="30%"></th><th width="8%"></th></tr>
              <?php foreach($before['hobjects'] as $hobject): ?>
                <?php if ($hobject['type'] == 'pest'): ?>
                  <tr>
                    <td><a href="/<?php print $hobject['link']; ?>" target="_blank"><?php print $hobject['name']; ?></a></td>
                    <td></td>
                    <td>
                      <?php if (!empty($hobject['photo'])): ?>
                        <a href="<?php print $hobject['photo']; ?>" title="<?php print $hobject["photo_title"]; ?><?php print ($hobject["is_handbook_photo"] ? '': ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания'); ?>" class="fancybox<?php print ($hobject["is_handbook_photo"] ? '': ' is-own'); ?>">
                          <i class="fa fa-camera" aria-hidden="true"></i>
                        </a>
                        <?php $ho_photo_message = true; ?>
                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </table>
            <?php if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
          </div>
        <?php endif; ?>
        <?php if ($content['fungis']): ?>
          <div class="text">
            <?php $ho_photo_message = false; ?>
            <table>
              <tr class="head"><th>Болезни</th><th width="30%"></th><th width="5%"></th></tr>
              <?php foreach($before['hobjects'] as $hobject): ?>
                <?php if ($hobject['type'] == 'disease'): ?>
                  <tr>
                    <td><a href="/<?php print $hobject['link']; ?>" target="_blank"><?php print $hobject['name']; ?></a></td>
                    <td></td>
                    <td>
                      <?php if ($hobject['photo']): ?>
                        <a href="<?php print $hobject['photo']; ?>" title="<?php print $hobject["photo_title"]; ?><?php print ($hobject["is_handbook_photo"] ? '': ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания'); ?>" class="fancybox<?php print ($hobject["is_handbook_photo"] ? '': ' is-own'); ?>">
                          <i class="fa fa-camera" aria-hidden="true"></i>
                        </a>
                        <?php $ho_photo_message = true; ?>
                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </table>
            <?php if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <div class="column after col-md-6">
      <header class="col-title">
        После обработки
      </header>

      <?php if ($content['measurements']): ?>
        <ul class="nav nav-tabs" role="tablist">
          <?php foreach($content['measurements'] as $key => $measurement): ?>
            <li role="presentation"<?php if (!$key) print ' class="active"'; ?>>
              <a href="#tabp<?php print $key; ?>" aria-controls="tabp<?php print $key; ?>" role="tab" data-toggle="tab">
                <?php print $measurement['days_after']; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="tab-content">
          <?php foreach($content['measurements'] as $key => $measurement): ?>
            <div role="tabpanel" class="tab-pane fade<?php if (!$key) print ' in active'; ?>" id="tabp<?php print $key; ?>">
              <div class="tab-pane-wrapper">

                <div class="block b1">

                  <div class="image">
                    <div class="date">Состояние поля на <?php print $measurement['date']; ?></div>
                    <a href="<?php print $measurement['image_field_full']; ?>" class="fancybox" title="Состояние поля <?php print $content['culture'];?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                      <img typeof="foaf:Image" src="<?php print $measurement['image_field_thumb']; ?>" property="dc:image" class="img-responsive" alt="Состояние поля <?php print $content['culture'];?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" loading="lazy">
                    </a>
                  </div>
                  <div class="text c1">
                    <?php if (!empty($measurement['comment_short'])): ?>
                      <div class="cc1"><?php print $measurement['comment_short']; ?></div>
                      <div class="cc2"><?php print $measurement['comment']; ?>&nbsp;<span class="s1">свернуть</span></div>
                    <?php else: ?>
                      <div class="cc1"><?php print $measurement['comment']; ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="text">
                    <?php $ho_photo_message = false; ?>
                    <table>
                      <?php if ($content['weeds_d'] || $content['weeds_o']): ?>
                        <tr class="head"><th width="62%">Сорное растение</th><th width="30%">% гибели</th><th width="8%"></th></tr>
                        <?php if ($content['weeds_d']): ?>
                          <tr class="title dom" align="center"><td colspan="3">Доминантные</td></tr>
                          <?php foreach($measurement['hobjects'] as $hobject): ?>
                            <?php if ($hobject['type'] == 'weed' && $hobject['dominant']): ?>
                              <tr class="dom">
                                <td><a href="/<?php print $hobject['link']; ?>" target="_blank"><?php print $hobject['name']; ?></a></td>
                                <td align="center"><?php print $hobject['percent']; ?></td>
                                <td>
                                  <?php if ($hobject['photo']): ?>
                                    <a href="<?php print $hobject['photo']; ?>" title="<?php print $hobject["photo_title"]; ?><?php print ($hobject["is_handbook_photo"] ? '': ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания'); ?>" class="fancybox<?php print ($hobject["is_handbook_photo"] ? '': ' is-own'); ?>">
                                      <i class="fa fa-camera" aria-hidden="true"></i>
                                    </a>
                                    <?php $ho_photo_message = true; ?>
                                  <?php else: ?>
                                    -
                                  <?php endif; ?>
                                </td>
                              </tr>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($content['weeds_o']): ?>
                          <tr class="title" align="center"><td colspan="3">Прочие</td></tr>
                          <?php foreach($measurement['hobjects'] as $hobject): ?>
                            <?php if ($hobject['type'] == 'weed' && !$hobject['dominant']): ?>
                              <tr>
                                <td><a href="/<?php print $hobject['link']; ?>" target="_blank"><?php print $hobject['name']; ?></a></td>
                                <td align="center"><?php print $hobject['percent']; ?></td>
                                <td>
                                  <?php if ($hobject['photo']): ?>
                                    <a href="<?php print $hobject['photo']; ?>" title="<?php print $hobject["photo_title"]; ?><?php print ($hobject["is_handbook_photo"] ? '': ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания'); ?>" class="fancybox<?php print ($hobject["is_handbook_photo"] ? '': ' is-own'); ?>">
                                      <i class="fa fa-camera" aria-hidden="true"></i>
                                    </a>
                                    <?php $ho_photo_message = true; ?>
                                  <?php else: ?>
                                    -
                                  <?php endif; ?>
                                </td>
                              </tr>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      <?php endif; ?>
                    </table>
                    <?php if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
                  </div>
                </div>

                <div class="block">
                  <?php if (!empty($measurement['image_culture_thumb'])): ?>
                    <div class="image">
                      <div class="date">Состояние культуры на <?php print $measurement['date']; ?></div>
                      <a href="<?php print $measurement['image_culture_full']; ?>" class="fancybox" title="Состояние культуры <?php print $content['culture'];?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                        <img typeof="foaf:Image" src="<?php print $measurement['image_culture_thumb']; ?>" property="dc:image" class="img-responsive" alt="Состояние культуры <?php print $content['culture'];?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" loading="lazy">
                      </a>
                    </div>
                    <div class="text">
                      <div class="b2"><span>Состояние: </span><?php print $measurement['condition']; ?></div>
                      <div class="b2"><span>Фаза: </span><?php print $measurement['phase']; ?></div>
                    </div>
                  <?php endif; ?>
                  <?php if ($content['pests']): ?>
                    <div class="text">
                      <?php $ho_photo_message = false; ?>
                      <table>
                        <tr class="head"><th>Вредители</th><th width="30%">% гибели</th><th width="5%"></th></tr>
                        <?php foreach($measurement['hobjects'] as $hobject): ?>
                          <?php if ($hobject['type'] == 'pest'): ?>
                            <tr>
                              <td><a href="/<?php print $hobject['link']; ?>" target="_blank"><?php print $hobject['name']; ?></a></td>
                              <td align="center"><?php print $hobject['percent']; ?></td>
                              <td>
                                <?php if ($hobject['photo']): ?>
                                  <a href="<?php print $hobject['photo']; ?>" title="<?php print $hobject["photo_title"]; ?><?php print ($hobject["is_handbook_photo"] ? '': ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания'); ?>" class="fancybox<?php print ($hobject["is_handbook_photo"] ? '': ' is-own'); ?>">
                                    <i class="fa fa-camera" aria-hidden="true"></i>
                                  </a>
                                  <?php $ho_photo_message = true; ?>
                                <?php else: ?>
                                  -
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </table>
                      <?php if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
                    </div>
                  <?php endif; ?>
                  <?php if ($content['fungis']): ?>
                    <div class="text">
                      <?php $ho_photo_message = false; ?>
                      <table>
                        <tr class="head"><th>Болезни</th><th width="30%">% гибели</th><th width="5%"></th></tr>
                        <?php foreach($measurement['hobjects'] as $hobject): ?>
                          <?php if ($hobject['type'] == 'disease'): ?>
                            <tr>
                              <td><a href="/<?php print $hobject['link']; ?>" target="_blank"><?php print $hobject['name']; ?></a></td>
                              <td align="center"><?php print $hobject['percent']; ?></td>
                              <td>
                                <?php if ($hobject['photo']): ?>
                                  <a href="<?php print $hobject['photo']; ?>" title="<?php print $hobject["photo_title"]; ?><?php print ($hobject["is_handbook_photo"] ? '': ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания'); ?>" class="fancybox<?php print ($hobject["is_handbook_photo"] ? '': ' is-own'); ?>">
                                    <i class="fa fa-camera" aria-hidden="true"></i>
                                  </a>
                                  <?php $ho_photo_message = true; ?>
                                <?php else: ?>
                                  -
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </table>
                      <?php if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
                    </div>
                  <?php endif; ?>
                </div>

              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php else: ?>
        <div class="no-content">
          Контроль<br />не проводился
        </div>
      <?php endif; ?>
    </div>

    <div class="column processings col-sm-12">
      <header class="col-title">
        Проведённые обработки
      </header>

      <?php if ($content['processings']): ?>
        <ul class="nav nav-tabs" role="tablist">
          <?php foreach($content['processings'] as $key => $processing): ?>
            <li role="presentation"<?php if (!$key) print ' class="active"'; ?>>
              <a href="#tab<?php print $key; ?>" aria-controls="tab<?php print $key; ?>" role="tab" data-toggle="tab">
                <?php print $processing['date']; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="tab-content">
          <?php foreach($content['processings'] as $key => $processing): ?>
            <div role="tabpanel" class="tab-pane fade<?php if (!$key) print ' in active'; ?>" id="tab<?php print $key; ?>">
              <div class="tab-pane-wrapper row">
                <div class="image col-md-6">
                  <a href="<?php print $processing['image_full']; ?>" class="fancybox" title="Процесс обработки поля <?php print $content['culture'];?> препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                    <img typeof="foaf:Image" src="<?php print $processing['image_full']; ?>" property="dc:image" class="img-responsive" alt="Процесс обработки поля <?php print $content['culture'];?> препаратами ООО ТД Кирово-Чепецкая Химическая Компания" loading="lazy">
                  </a>
                  <div class="date">Обработка <?php print $processing['date'] . (strpos($processing['time'], 'после') === false ? ' в ' : ' ') . $processing['time']; ?></div>
                </div>
                <div class="conditions col-md-6">
                  <dl>
                    <dt>Кислотность почвы:  <dd><?php print $processing['acidity']; ?>
                    <dt>Влажность почвы:    <dd><?php print $processing['humidity']; ?> %
                    <dt>Температура, ночь:  <dd><?php print $processing['t_night']; ?> град. С
                    <dt>Температура, день:  <dd><?php print $processing['t_day']; ?> град. С
                    <dt>Скорость ветра:     <dd><?php print $processing['wind']; ?> м/c
                    <dt>Осадки:             <dd><?php print $processing['precipitation']; ?>
                    <dt>Механизм внесения:  <dd><?php print $processing['mechanism']; ?>
                  </dl>
                </div>

                <div class="preparations col-md-12">
                  <?php foreach($processing['preparations'] as $prep) :?>
                    <div class="preparation">
                      <div class="image">
                        <a href="<?php print $prep['url']; ?>" target="_blank">
                          <img typeof="foaf:Image" src="<?php print $prep['image_medium']; ?>" property="dc:image" class="img-responsive" alt="<?php print $prep["title"]; ?> - препарат ООО ТД Кирово-Чепецкая Химическая Компания" loading="lazy">
                        </a>
                      </div>
                      <div class="info">
                        <div class="title"><a href="<?php print $prep['url']; ?>" target="_blank"><?php print $prep['title']; ?></a></div>
                        <div class="ingredients"><span><?php print $prep['ingredients']; ?></span></div>
                        <div class="description"><?php print $prep['description']; ?></div>
                      </div>
                      <?php if(!empty($prep['rate'])): ?>
                        <div class="rate">
                          <div>Норма расхода</div>
                          <div><?php print $prep['rate']; ?><span><?php print $prep['unit_short']; ?>/<?php print $prep['unit_field']; ?></span></div>
                        </div>
                      <?php endif; ?>
                      <?php if(!empty($prep['consumption'])): ?>
                        <div class="consumption">
                          <div>Расход рабочей жидкости</div>
                          <div><?php print $prep['consumption']; ?><span>л/<?php print $prep['unit_field']; ?></span></div>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="no-content">
          Нет данных
        </div>
      <?php endif; ?>
    </div>
  </div>

</article>

