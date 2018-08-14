<?
// автор
$author = array();
$user = user_load($variables['form']['#node']->uid);
$author = person_get_user_array($variables['form']['#node']->uid);
$author_name = $author['surname'] . '<br />' . $author['name'] . ' ' . $author['name2'];

// компания
$company = person_get_user_company_array($user);
    // если нет компании, разместить ссылку на Добавить
if (empty($company['name'])) {
    $company['name'] = l(t('Add company'), '/person/' . $user->uid . '/company', array('attributes' => array('class' => array('add-link'))));
}

?>
<div class="grouped-form">
    <div class="group-contacts form-wrapper form-group" id="edit-group-contacts">
        <div class="group-header">
            <h2>Контактная информация</h2>
            <div class="group-help"></div>
        </div>
        <div class="form-type-markup form-wrapper">
            <label for="">Электронный адрес</label>
            <div><? print $author['email']; ?></div>
        </div>
        <div class="form-type-markup form-wrapper">
            <label for="">Разрешить сообщения</label>
            <div><?php print render($form['field_ad_allow_messages']); ?></div>
        </div>
        <div class="form-type-markup form-wrapper">
            <label for="">Ваше имя</label>
            <div><? print $author['name']; ?></div>
        </div>
        <div class="form-type-markup form-wrapper">
            <label for="">Компания</label>
            <div><?php print render($form['field_ad_allow_company']); ?></div>
        </div>

        <div class="form-type-markup form-company-info" style="display: none;">
            <label></label>
            <div class="author">
                <img src="<? print $company['photo']; ?>" />
                <div class="author-body">
                    <div class="author-title"><? print $company['name']; ?></div>
                    <div class="author-subtitle"><? print $company['opf']; ?></div>
                </div>
            </div>
        </div>


        <?php print render($form['field_phone']); ?>
    </div>

    <div class="group-settings form-wrapper form-group" id="edit-group-settings">
        <div class="group-header">
            <h2>Параметры</h2>
            <div class="group-help"></div>
        </div>
        <?php print render($form['field_ad_type']); ?>
        <?php print render($form['field_pa_activity']); ?>
        <?php print render($form['field_place']); ?>
    </div>

    <div class="group-place form-wrapper form-group" id="edit-group-place">
        <div class="group-header">
            <h2>Содержимое объявления</h2>
            <div class="group-help"></div>
        </div>
        <div class="row">
            <?php print render($form['title']); ?>
            <?php print render($form['body']); ?>
            <?php print render($form['field_price']); ?>
            <?php print render($form['field_image_gallery']); ?>
        </div>
    </div>

    <?php echo drupal_render_children($form)?>
</div>