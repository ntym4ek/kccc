<?php

/**
 * Email notifier.
 */
class MessageNotifierEmailWithRealname extends MessageNotifierBase {

    public function deliver(array $output = array()) {
        $plugin = $this->plugin;
        $message = $this->message;

        $options = $plugin['options'];

        $account = user_load($message->uid);
        // меняем стандартное имя на RealName
        if (module_exists('realname') && !empty($account->realname)) {
            $account->name = $account->realname;
        }
        // и отправляем вместо текстового email объект
        // из которого будет извлечено имя
        $mail = $options['mail'] ? $options['mail'] : $account;

        $languages = language_list();
        if (!$options['language override']) {
            $lang = !empty($account->language) && $account->language != LANGUAGE_NONE ? $languages[$account->language]: language_default();
        }
        else {
            $lang = $languages[$message->language];
        }

        // The subject in an email can't be with HTML, so strip it.
        $output['message_notify_email_subject'] = strip_tags($output['message_notify_email_subject']);

        // Pass the message entity along to hook_drupal_mail().
        $output['message_entity'] = $message;

        $result =  drupal_mail('message_notify', $message->type, $mail, $lang, $output);
        return $result['result'];
    }

}
