<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */


/**
 * функция, возвращающая контент страницы До и После
 */
function person_before_after($acid) {
  global $user;

  $output = false;

  // вернуть форму только, если свой аккаунт
  if ($user->uid == $acid) {
    $output = before_after_user_fields_page($acid);
  }

  return $output;
}

/**
 * контент До и После
 */
function person_before_after_list($acid) {
  return before_after_user_fields_page($acid);
}
