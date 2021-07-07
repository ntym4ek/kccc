<div class="contacts">
    <div class="row">
        <div class="col-xs-12">

            <!-- закладки -->
            <ul class="nav nav-tabs magic-line-menu" role="tablist">
            <? foreach ($contacts as $key_t => $tab): ?>
                <li role="presentation" class="col-xs-12 col-sm-4<? print $key_t == 'td' ? ' active' :''; ?>">
                    <a href="#<? print 'tab-' . $key_t; ?>" class="<? print 'tab-' . $key_t; ?>" aria-controls="home" role="tab" data-toggle="tab">
                        <img class="logo-on" src="<? print $tab['logo_on']; ?>" alt="logo" />
                        <img class="logo-off" src="<? print $tab['logo_off']; ?>" alt="logo" />
                        <span><? print $tab['title']; ?></span>
                    </a>
                </li>
            <? endforeach; ?>
            </ul>

            <!-- подразделения -->
            <div class="tab-content">
            <? foreach ($contacts as $key_t => $tab): ?>
                <div role="tabpanel" class="row tab-pane fade<? print $key_t == 'td' ? ' in active' : ''; ?>" id="<? print 'tab-' . $key_t; ?>">

                    <!-- отделы -->
                    <? foreach ($tab['departments'] as $key_d => $department): ?>
                        <h3 class="department-title col-sm-12"><? print $department['title']; ?></h3>

                        <!-- контакты -->
                        <? foreach ($department['contacts'] as $key_c => $contact): ?>
                            <? print $contact['contact_html']; ?>
                            <? if (!(($key_c+1) % 2)): ?><div class="clearfix"></div><? endif; ?>
                        <? endforeach; ?>
                    <? endforeach; ?>

                </div>
            <? endforeach; ?>
            </div>
        </div>
    </div>
</div>
