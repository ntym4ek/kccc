<?php
/**
 * @file
 * Default theme implementation to format an HTML mail.
 *
 * Copy this file in your default theme folder to create a custom themed mail.
 * Rename it to mimemail-message--[module]--[key].tpl.php to override it for a
 * specific mail.
 *
 * Available variables:
 * - $recipient: The recipient of the message
 * - $subject: The message subject
 * - $body: The message body
 * - $css: Internal style sheets
 * - $module: The sending module
 * - $key: The message identifier
 *
 * @see template_preprocess_mimemail_message()
 */

/** сформировать переменные
* message - текст сообщения на языке письма
* sign - подпись на языке письма
* auto - текст сообщения о том, что письмо сформировано автоматически
*/
$lang = drupal_strtolower($GLOBALS['language']->language);
$sign = empty($message['params']['context']['sign']) ? t('Postal robot') . ' ' . t('ООО Trade House "Kirovo-Chepetsk Chemical Company"') : $message['params']['context']['sign'];
$auto = !isset($message['params']['context']['auto']) ? t('This message was generated automatically and does not require a response') : $message['params']['context']['auto'];
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php if ($css): ?>
            <style type="text/css">
                <!--
                <?php print $css ?>
                -->
            </style>
        <?php endif; ?>
    </head>
    <body id="mimemail-body" <?php if ($module && $key): print 'class="'. $module .'-'. $key .'"'; endif; ?>>
        <table width="100%" style="font-family: ubuntu,Lucida Grande,Lucida Sans,Lucida Sans Unicode,Arial,Helvetica,Verdana,sans-serif; font-size: 14px;">
            <tr>
                <td align="center">
                    <table width="800px">
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">
                                <img src="https://kccc.ru/sites/all/themes/gard/images/logo/logo_long.png" style="width: 299px;">
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc; padding: 20px; height:300px; vertical-align: top;">
                                <? print $body; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 20px;">
                                <? print $sign; ?><br />
                                <? if ($auto): ?>
                                    <span style="color: #bbb; font-size: .8em;"><? print $auto; ?></span>
                                <? endif; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>

