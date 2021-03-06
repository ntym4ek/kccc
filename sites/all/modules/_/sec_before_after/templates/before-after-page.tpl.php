<?
//$before = $content['measurements'][0];
$before = array_shift($content['measurements']);
?>

<article class="season node full">

  <div class="node-author">
    <img src="<? print $content['author']['photo']; ?>" alt="Представитель ООО ТД Кирово-Чепецкая Химическая Компания в регионе <? print $content['region'];?>" class="img-circle">
    <div class="author-body">
      <div class="author-title"><? print $content['author']['surname'] . ' ' . $content['author']['name'] . ' ' . $content['author']['name2']; ?></div>
      <div class="author-subtitle"><? print $content['author']['role']; ?></div>
    </div>
  </div>

  <div class="content row">
    <div class="column before col-md-6">
      <header class="col-title">
        До обработки
      </header>
      <div class="block b1">
        <div class="image">
          <div class="date">Состояние поля на <? print $before['date']; ?></div>
          <a href="<? print $before['image_field_full']; ?>" class="fancybox" title="Состояние поля <? print $content['culture'];?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
            <img src="<? print $before['image_field_thumb']; ?>" property="dc:image" class="img-responsive" alt="Состояние поля <? print $content['culture'];?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
          </a>
        </div>
        <div class="text c1">
          <? if (!empty($before['comment_short'])): ?>
            <div class="cc1"><? print $before['comment_short']; ?></div>
            <div class="cc2"><? print $before['comment']; ?>&nbsp;<span class="s1">свернуть</span></div>
          <? else: ?>
            <div class="cc1"><? print $before['comment']; ?></div>
          <? endif; ?>
        </div>
        <div class="text">
          <? $ho_photo_message = false; ?>
          <table>
            <? if ($content['weeds_d'] || $content['weeds_o']): ?>
              <tr class="head"><th width="62%">Сорное растение</th><th width="30%">Фаза</th><th width="8%"></th></tr>
              <? if ($content['weeds_d']): ?>
                <tr class="title dom" align="center"><td colspan="3">Доминантные</td></tr>
                <? foreach($before['hobjects'] as $hobject): ?>
                  <? if ($hobject['type'] == 'weed' && $hobject['dominant']): ?>
                    <tr class="dom">
                      <td><a href="/<? print $hobject['link']; ?>" target="_blank"><? print $hobject['name']; ?></a></td>
                      <td><? print $hobject['phase']; ?></td>
                      <td>
                        <? if (!empty($hobject['photo'])): ?>
                          <a href="<? print $hobject['photo']; ?>" title="<? print $hobject["name"]; ?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" class="fancybox">
                            <i class="fa fa-camera" aria-hidden="true"></i>
                          </a>
                          <? $ho_photo_message = true; ?>
                        <? else: ?>
                          -
                        <? endif; ?>
                      </td>
                    </tr>
                  <? endif; ?>
                <? endforeach; ?>
              <? endif; ?>
              <? if ($content['weeds_o']): ?>
                <tr class="title" align="center"><td colspan="3">Прочие</td></tr>
                <? foreach($before['hobjects'] as $hobject): ?>
                  <? if ($hobject['type'] == 'weed' && !$hobject['dominant']): ?>
                    <tr>
                      <td><a href="/<? print $hobject['link']; ?>" target="_blank"><? print $hobject['name']; ?></a></td>
                      <td><? print $hobject['phase']; ?></td>
                      <td>
                        <? if (!empty($hobject['photo'])): ?>
                          <a href="<? print $hobject['photo']; ?>" title="<? print $hobject["name"]; ?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" class="fancybox">
                            <i class="fa fa-camera" aria-hidden="true"></i>
                          </a>
                          <? $ho_photo_message = true; ?>
                        <? else: ?>
                          -
                        <? endif; ?>
                      </td>
                    </tr>
                  <? endif; ?>
                <? endforeach; ?>
              <? endif; ?>
            <? endif; ?>
          </table>
          <? if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
        </div>
      </div>
      <div class="block">
        <?php if (!empty($before['image_culture_thumb'])): ?>
          <div class="image">
            <div class="date">Состояние культуры на <? print $before['date']; ?></div>
            <a href="<? print $before['image_culture_full']; ?>" class="fancybox" title="Состояние культуры <? print $content['culture'];?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
              <img src="<? print $before['image_culture_thumb']; ?>" property="dc:image" class="img-responsive" alt="Состояние культуры <? print $content['culture'];?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
            </a>
          </div>
          <div class="text">
            <div class="b2"><span>Состояние: </span><? print $before['condition']; ?></div>
            <div class="b2"><span>Фаза: </span><? print $before['phase']; ?></div>
          </div>
        <?php endif; ?>
        <? if ($content['pests']): ?>
          <div class="text">
            <? $ho_photo_message = false; ?>
            <table>
              <tr class="head"><th>Вредители</th><th width="30%"></th><th width="8%"></th></tr>
              <? foreach($before['hobjects'] as $hobject): ?>
                <? if ($hobject['type'] == 'pest'): ?>
                  <tr>
                    <td><a href="/<? print $hobject['link']; ?>" target="_blank"><? print $hobject['name']; ?></a></td>
                    <td></td>
                    <td>
                      <? if (!empty($hobject['photo'])): ?>
                        <a href="<? print $hobject['photo']; ?>" title="<? print $hobject["name"]; ?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" class="fancybox">
                          <i class="fa fa-camera" aria-hidden="true"></i>
                        </a>
                        <? $ho_photo_message = true; ?>
                      <? else: ?>
                        -
                      <? endif; ?>
                    </td>
                  </tr>
                <? endif; ?>
              <? endforeach; ?>
            </table>
            <? if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
          </div>
        <? endif; ?>
        <? if ($content['fungis']): ?>
          <div class="text">
            <? $ho_photo_message = false; ?>
            <table>
              <tr class="head"><th>Болезни</th><th width="30%"></th><th width="5%"></th></tr>
              <? foreach($before['hobjects'] as $hobject): ?>
                <? if ($hobject['type'] == 'disease'): ?>
                  <tr>
                    <td><a href="/<? print $hobject['link']; ?>" target="_blank"><? print $hobject['name']; ?></a></td>
                    <td></td>
                    <td>
                      <? if ($hobject['photo']): ?>
                        <a href="<? print $hobject['photo']; ?>" title="<? print $hobject["name"]; ?> до обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" class="fancybox">
                          <i class="fa fa-camera" aria-hidden="true"></i>
                        </a>
                        <? $ho_photo_message = true; ?>
                      <? else: ?>
                        -
                      <? endif; ?>
                    </td>
                  </tr>
                <? endif; ?>
              <? endforeach; ?>
            </table>
            <? if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
          </div>
        <? endif; ?>

      </div>
    </div>

    <div class="column after col-md-6">
      <header class="col-title">
        После обработки
      </header>

      <? if ($content['measurements']): ?>
        <ul class="nav nav-tabs" role="tablist">
          <? foreach($content['measurements'] as $key => $measurement): ?>
            <li role="presentation"<? if (!$key) print ' class="active"'; ?>>
              <a href="#tabp<? print $key; ?>" aria-controls="tabp<? print $key; ?>" role="tab" data-toggle="tab">
                <? print $measurement['days_after']; ?>
              </a>
            </li>
          <? endforeach; ?>
        </ul>

        <div class="tab-content">
          <? foreach($content['measurements'] as $key => $measurement): ?>
            <div role="tabpanel" class="tab-pane fade<? if (!$key) print ' in active'; ?>" id="tabp<? print $key; ?>">
              <div class="tab-pane-wrapper">

                <div class="block b1">

                  <div class="image">
                    <div class="date">Состояние поля на <? print $measurement['date']; ?></div>
                    <a href="<? print $measurement['image_field_full']; ?>" class="fancybox" title="Состояние поля <? print $content['culture'];?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                      <img src="<? print $measurement['image_field_thumb']; ?>" property="dc:image" class="img-responsive" alt="Состояние поля <? print $content['culture'];?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                    </a>
                  </div>
                  <div class="text c1">
                    <? if (!empty($measurement['comment_short'])): ?>
                      <div class="cc1"><? print $measurement['comment_short']; ?></div>
                      <div class="cc2"><? print $measurement['comment']; ?>&nbsp;<span class="s1">свернуть</span></div>
                    <? else: ?>
                      <div class="cc1"><? print $measurement['comment']; ?></div>
                    <? endif; ?>
                  </div>
                  <div class="text">
                    <? $ho_photo_message = false; ?>
                    <table>
                      <? if ($content['weeds_d'] || $content['weeds_o']): ?>
                        <tr class="head"><th width="62%">Сорное растение</th><th width="30%">% гибели</th><th width="8%"></th></tr>
                        <? if ($content['weeds_d']): ?>
                          <tr class="title dom" align="center"><td colspan="3">Доминантные</td></tr>
                          <? foreach($measurement['hobjects'] as $hobject): ?>
                            <? if ($hobject['type'] == 'weed' && $hobject['dominant']): ?>
                              <tr class="dom">
                                <td><a href="/<? print $hobject['link']; ?>" target="_blank"><? print $hobject['name']; ?></a></td>
                                <td align="center"><? print $hobject['percent']; ?></td>
                                <td>
                                  <? if ($hobject['photo']): ?>
                                    <a href="<? print $hobject['photo']; ?>" title="<? print $hobject["name"]; ?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" class="fancybox">
                                      <i class="fa fa-camera" aria-hidden="true"></i>
                                    </a>
                                    <? $ho_photo_message = true; ?>
                                  <? else: ?>
                                    -
                                  <? endif; ?>
                                </td>
                              </tr>
                            <? endif; ?>
                          <? endforeach; ?>
                        <? endif; ?>
                        <? if ($content['weeds_o']): ?>
                          <tr class="title" align="center"><td colspan="3">Прочие</td></tr>
                          <? foreach($measurement['hobjects'] as $hobject): ?>
                            <? if ($hobject['type'] == 'weed' && !$hobject['dominant']): ?>
                              <tr>
                                <td><a href="/<? print $hobject['link']; ?>" target="_blank"><? print $hobject['name']; ?></a></td>
                                <td align="center"><? print $hobject['percent']; ?></td>
                                <td>
                                  <? if ($hobject['photo']): ?>
                                    <a href="<? print $hobject['photo']; ?>" title="<? print $hobject["name"]; ?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" class="fancybox">
                                      <i class="fa fa-camera" aria-hidden="true"></i>
                                    </a>
                                    <? $ho_photo_message = true; ?>
                                  <? else: ?>
                                    -
                                  <? endif; ?>
                                </td>
                              </tr>
                            <? endif; ?>
                          <? endforeach; ?>
                        <? endif; ?>
                      <? endif; ?>
                    </table>
                    <? if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
                  </div>
                </div>

                <div class="block">
                  <?php if (!empty($measurement['image_culture_thumb'])): ?>
                    <div class="image">
                      <div class="date">Состояние культуры на <? print $measurement['date']; ?></div>
                      <a href="<? print $measurement['image_culture_full']; ?>" class="fancybox" title="Состояние культуры <? print $content['culture'];?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                        <img src="<? print $measurement['image_culture_thumb']; ?>" property="dc:image" class="img-responsive" alt="Состояние культуры <? print $content['culture'];?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                      </a>
                    </div>
                    <div class="text">
                      <div class="b2"><span>Состояние: </span><? print $measurement['condition']; ?></div>
                      <div class="b2"><span>Фаза: </span><? print $measurement['phase']; ?></div>
                    </div>
                  <?php endif; ?>
                  <? if ($content['pests']): ?>
                    <div class="text">
                      <? $ho_photo_message = false; ?>
                      <table>
                        <tr class="head"><th>Вредители</th><th width="30%">% гибели</th><th width="5%"></th></tr>
                        <? foreach($measurement['hobjects'] as $hobject): ?>
                          <? if ($hobject['type'] == 'pest'): ?>
                            <tr>
                              <td><a href="/<? print $hobject['link']; ?>" target="_blank"><? print $hobject['name']; ?></a></td>
                              <td align="center"><? print $hobject['percent']; ?></td>
                              <td>
                                <? if ($hobject['photo']): ?>
                                  <a href="<? print $hobject['photo']; ?>" title="<? print $hobject["name"]; ?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" class="fancybox">
                                    <i class="fa fa-camera" aria-hidden="true"></i>
                                  </a>
                                  <? $ho_photo_message = true; ?>
                                <? else: ?>
                                  -
                                <? endif; ?>
                              </td>
                            </tr>
                          <? endif; ?>
                        <? endforeach; ?>
                      </table>
                      <? if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
                    </div>
                  <? endif; ?>
                  <? if ($content['fungis']): ?>
                    <div class="text">
                      <? $ho_photo_message = false; ?>
                      <table>
                        <tr class="head"><th>Болезни</th><th width="30%">% гибели</th><th width="5%"></th></tr>
                        <? foreach($measurement['hobjects'] as $hobject): ?>
                          <? if ($hobject['type'] == 'disease'): ?>
                            <tr>
                              <td><a href="/<? print $hobject['link']; ?>" target="_blank"><? print $hobject['name']; ?></a></td>
                              <td align="center"><? print $hobject['percent']; ?></td>
                              <td>
                                <? if ($hobject['photo']): ?>
                                  <a href="<? print $hobject['photo']; ?>" title="<? print $hobject["name"]; ?> после обработки препаратами ООО ТД Кирово-Чепецкая Химическая Компания" class="fancybox">
                                    <i class="fa fa-camera" aria-hidden="true"></i>
                                  </a>
                                  <? $ho_photo_message = true; ?>
                                <? else: ?>
                                  -
                                <? endif; ?>
                              </td>
                            </tr>
                          <? endif; ?>
                        <? endforeach; ?>
                      </table>
                      <? if ($ho_photo_message) print '<div class="photo-text">Для просмотра фото нажмите на значок&nbsp;&nbsp;<i class="fa fa-camera" aria-hidden="true"></i></div>'; ?>
                    </div>
                  <? endif; ?>
                </div>

              </div>
            </div>
          <? endforeach; ?>
        </div>

      <? else: ?>
        <div class="no-content">
          Замеры<br />не проводились
        </div>
      <? endif; ?>
    </div>

    <div class="column processings col-sm-12">
      <header class="col-title">
        Проведённые обработки
      </header>

      <? if ($content['processings']): ?>
        <ul class="nav nav-tabs" role="tablist">
          <? foreach($content['processings'] as $key => $processing): ?>
            <li role="presentation"<? if (!$key) print ' class="active"'; ?>>
              <a href="#tab<? print $key; ?>" aria-controls="tab<? print $key; ?>" role="tab" data-toggle="tab">
                <? print $processing['date']; ?>
              </a>
            </li>
          <? endforeach; ?>
        </ul>

        <div class="tab-content">
          <? foreach($content['processings'] as $key => $processing): ?>
            <div role="tabpanel" class="tab-pane fade<? if (!$key) print ' in active'; ?>" id="tab<? print $key; ?>">
              <div class="tab-pane-wrapper row">
                <div class="image col-md-6">
                  <a href="<? print $processing['image_full']; ?>" class="fancybox" title="Процесс обработки поля <? print $content['culture'];?> препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                    <img src="<? print $processing['image_full']; ?>" property="dc:image" class="img-responsive" alt="Процесс обработки поля <? print $content['culture'];?> препаратами ООО ТД Кирово-Чепецкая Химическая Компания">
                  </a>
                  <div class="date">Обработка <? print $processing['date'] . (strpos($processing['time'], 'после') === false ? ' в ' : ' ') . $processing['time']; ?></div>
                </div>
                <div class="conditions col-md-6">
                  <dl>
                    <dt>Кислотность почвы:  <dd><? print $processing['acidity']; ?>
                    <dt>Влажность почвы:    <dd><? print $processing['humidity']; ?> %
                    <dt>Температура, ночь:  <dd><? print $processing['t_night']; ?> град. С
                    <dt>Температура, день:  <dd><? print $processing['t_day']; ?> град. С
                    <dt>Скорость ветра:     <dd><? print $processing['wind']; ?> м/c
                    <dt>Осадки:             <dd><? print $processing['precipitation']; ?>
                    <dt>Механизм внесения:  <dd><? print $processing['mechanism']; ?>
                  </dl>
                </div>

                <div class="preparations col-md-12">
                  <? foreach($processing['preparations'] as $prep) :?>
                    <div class="preparation">
                      <div class="image">
                        <a href="<? print $prep['image_full']; ?>" class="prep fancybox" title="<? print $prep["title"]; ?> - препарат ООО ТД Кирово-Чепецкая Химическая Компания">
                          <img src="<? print $prep['image_thumb']; ?>" property="dc:image" class="img-responsive" alt="<? print $prep["title"]; ?> - препарат ООО ТД Кирово-Чепецкая Химическая Компания">
                        </a>
                      </div>
                      <div class="info">
                        <div class="title"><a href="<? print $prep['url']; ?>" target="_blank"><? print $prep['title']; ?></a></div>
                        <div class="ingredients"><span><? print $prep['ingredients']; ?></span></div>
                        <div class="description"><? print $prep['description']; ?></div>
                      </div>
                      <? if(!empty($prep['rate'])): ?>
                        <div class="rate">
                          <div>Норма расхода</div>
                          <div><? print $prep['rate']; ?><span><? print $prep['unit_short']; ?>/<? print $prep['unit_field']; ?></span></div>
                        </div>
                      <? endif; ?>
                      <? if(!empty($prep['consumption'])): ?>
                        <div class="consumption">
                          <div>Расход рабочей жидкости</div>
                          <div><? print $prep['consumption']; ?><span>л/га</span></div>
                        </div>
                      <? endif; ?>
                    </div>
                  <? endforeach; ?>
                </div>
              </div>
            </div>
          <? endforeach; ?>
        </div>
      <? else: ?>
        <div class="no-content">
          Нет данных
        </div>
      <? endif; ?>
    </div>
  </div>

</article>

