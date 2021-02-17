<?php

/**
 * возвращает название компании
 * @param $pid - id профиля
 * @return string
 */
function ext_user_get_user_company_name($pid){
  $company_name = '';
  if (!empty($pid) && $company = profile2_load($pid)) {
    $company_wrapper = entity_metadata_wrapper('profile2', $company);
    $opf  = $company_wrapper->field_profile_company_type->value() ? $company_wrapper->field_profile_company_type->name->value() : '';
    $name = $company_wrapper->field_profile_name->value();
    $company_name = $opf . ' ' . $name;
  }

  return trim($company_name);
}
