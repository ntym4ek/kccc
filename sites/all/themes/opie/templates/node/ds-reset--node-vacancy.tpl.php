
<article class="vacancy full"<?php print $attributes; ?>>
    <div class="section"><? print t('Vacancies'); ?></div>

    <h1 class="title"><? print $title; ?></h1>

    <div class="vac-body">
        <div class="vac-header">
            <?php if (isset($content['field_vacancy_employer'])) print render($content['field_vacancy_employer']); ?>
            <?php if (isset($content['field_vacancy_location'])) print render($content['field_vacancy_location']); ?>
            <?php if (isset($content['field_vacancy_mode'])) print render($content['field_vacancy_mode']); ?>
            <?php if (isset($content['post_date'])) print render($content['post_date']); ?>
        </div>

        <div class="vac-content">
            <?php print render($content['field_vacancy_duties']); ?>
            <?php print render($content['field_vacancy_requirements']); ?>
            <?php print render($content['field_vacancy_conditions']); ?>
        </div>
    </div>

    <div class="vac-form">
        <?php print $vacancy_form; ?>
    </div>
</article>