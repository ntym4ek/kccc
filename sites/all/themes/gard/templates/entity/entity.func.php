<?php
/**
 * изменение вывода entityform_type
 * выводим только контент, темизируем через модуль ext_entityform
 */
function gard_entityform_type__entityform_type__full($vars) {
    return render($vars['content']);
}
